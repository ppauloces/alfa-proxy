<?php

namespace App\Console\Commands;

use App\Models\Cartao;
use App\Models\Stock;
use App\Models\Transaction;
use App\Services\ProxyRenewalService;
use App\Services\StripeService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoRenewProxies extends Command
{
    protected $signature = 'proxies:auto-renew
                            {--hours-before=12 : Renew proxies expiring within this window}
                            {--limit=500 : Max proxies to process per run}
                            {--dry-run : Do not charge, only log}';

    protected $description = 'Auto-renew proxies using saved credit cards (Stripe) with fallback';

    public function handle()
    {
        $hoursBefore = (int) $this->option('hours-before');
        $limit = (int) $this->option('limit');
        $dryRun = (bool) $this->option('dry-run');
        $cutoff = now()->addHours($hoursBefore);

        $query = Stock::with(['user', 'vps'])
            ->where('renovacao_automatica', true)
            ->whereNotNull('expiracao')
            ->whereNotNull('user_id')
            ->where('uso_interno', false)
            ->where('expiracao', '<=', $cutoff)
            ->orderBy('expiracao');

        if ($limit > 0) {
            $query->limit($limit);
        }

        $proxies = $query->get();

        if ($proxies->isEmpty()) {
            $this->info('No proxies eligible for auto-renewal.');
            return Command::SUCCESS;
        }

        $stripeService = app(StripeService::class);
        $renewalService = app(ProxyRenewalService::class);

        $processed = 0;
        $charged = 0;
        $failed = 0;
        $skipped = 0;

        foreach ($proxies as $proxy) {
            $processed++;
            $user = $proxy->user;

            if (!$user) {
                $skipped++;
                Log::warning('Auto-renew skip: missing user', ['proxy_id' => $proxy->id]);
                continue;
            }

            $periodo = (int) ($proxy->periodo_dias ?? 30);
            $valorTotal = (float) $renewalService->calculateRenewalPrice($user, $periodo);

            if ($valorTotal <= 0) {
                $skipped++;
                Log::warning('Auto-renew skip: zero price', [
                    'proxy_id' => $proxy->id,
                    'user_id' => $user->id,
                ]);
                continue;
            }

            $hasPending = Transaction::where('user_id', $user->id)
                ->where('tipo', 'renovacao')
                ->where('status', 0)
                ->where('created_at', '>=', now()->subHours(12))
                ->where('metadata->proxy_id', $proxy->id)
                ->exists();

            if ($hasPending) {
                $skipped++;
                Log::info('Auto-renew skip: pending renewal found', [
                    'proxy_id' => $proxy->id,
                    'user_id' => $user->id,
                ]);
                continue;
            }

            $cards = Cartao::where('user_id', $user->id)
                ->where('gateway', 'stripe')
                ->orderByDesc('is_default')
                ->orderBy('created_at')
                ->get()
                ->filter(function (Cartao $card) {
                    return !$card->isExpired() && !empty($card->token_gateway1);
                })
                ->values();

            if ($cards->isEmpty()) {
                $skipped++;
                Log::warning('Auto-renew skip: no valid cards', [
                    'proxy_id' => $proxy->id,
                    'user_id' => $user->id,
                ]);
                continue;
            }

            $metadata = [
                'auto_renew' => true,
                'proxy_id' => $proxy->id,
                'proxy_ip' => $proxy->ip,
                'proxy_porta' => $proxy->porta,
                'periodo_adicional' => $periodo,
                'expiracao_anterior' => $proxy->expiracao,
            ];

            if ($dryRun) {
                $this->line("DRY RUN: would charge proxy #{$proxy->id} (user {$user->id})");
                continue;
            }

            $transacao = Transaction::create([
                'user_id' => $user->id,
                'email' => $user->email,
                'transacao' => 'REN-AUTO-' . strtoupper(uniqid()),
                'valor' => $valorTotal,
                'status' => 0,
                'metodo_pagamento' => 'credit_card',
                'payment_method' => 'credit_card',
                'tipo' => 'renovacao',
                'metadata' => $metadata,
            ]);

            $paymentMethods = $cards->pluck('token_gateway1')->all();
            $result = $stripeService->chargeWithFallback([
                'amount' => (int) round($valorTotal * 100),
                'description' => sprintf('Renovacao automatica Proxy %s:%s - %d dias', $proxy->ip, $proxy->porta, $periodo),
                'customer' => $stripeService->formatCustomer($user),
                'metadata' => [
                    'auto_renew' => true,
                    'proxy_id' => $proxy->id,
                    'transaction_id' => $transacao->id,
                ],
            ], $paymentMethods);

            $attempts = $result['attempts'] ?? [];
            $metadata['card_attempts'] = $attempts;
            $transacao->metadata = $metadata;

            if (!empty($result['success'])) {
                $usedPaymentMethod = $result['payment_method_id'] ?? null;
                $usedCard = $cards->firstWhere('token_gateway1', $usedPaymentMethod);

                try {
                    DB::beginTransaction();

                    $renewalService->renewProxy($proxy, $periodo);

                    $transacao->update([
                        'status' => 1,
                        'gateway_transaction_id' => $result['data']['id'] ?? null,
                        'gateway_type' => 'stripe',
                        'card_id' => $usedCard?->id,
                        'metadata' => array_merge($metadata, [
                            'expiracao_nova' => $proxy->fresh()->expiracao,
                        ]),
                    ]);

                    DB::commit();
                    $charged++;
                    $this->info("Auto-renew success for proxy #{$proxy->id}");
                } catch (\Exception $e) {
                    DB::rollBack();
                    $failed++;
                    Log::error('Auto-renew failed after charge', [
                        'proxy_id' => $proxy->id,
                        'user_id' => $user->id,
                        'error' => $e->getMessage(),
                    ]);
                }
                continue;
            }

            $failed++;
            $metadata['failed_at'] = now()->toIso8601String();
            $metadata['last_error'] = $result['error'] ?? 'Charge failed';
            $transacao->metadata = $metadata;
            $transacao->save();
            Log::warning('Auto-renew charge failed', [
                'proxy_id' => $proxy->id,
                'user_id' => $user->id,
                'error' => $result['error'] ?? 'Charge failed',
            ]);
        }

        $this->newLine();
        $this->info("Processed: {$processed}");
        $this->info("Charged: {$charged}");
        $this->info("Failed: {$failed}");
        $this->info("Skipped: {$skipped}");

        return Command::SUCCESS;
    }
}
