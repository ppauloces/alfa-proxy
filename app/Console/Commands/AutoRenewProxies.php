<?php

namespace App\Console\Commands;

use App\Models\Cartao;
use App\Models\Stock;
use App\Models\Transaction;
use App\Services\MetaConversionService;
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

        // Agrupar proxies por usuário + período para cobrança única
        $grouped = [];

        foreach ($proxies as $proxy) {
            $user = $proxy->user;

            if (!$user) {
                $skipped++;
                Log::warning('Auto-renew skip: missing user', ['proxy_id' => $proxy->id]);
                continue;
            }

            $periodo = (int) ($proxy->periodo_dias ?? 30);
            $valorUnitario = (float) $renewalService->calculateRenewalPrice($user, $periodo);

            if ($valorUnitario <= 0) {
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

            $key = $user->id . '_' . $periodo;

            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'user' => $user,
                    'periodo' => $periodo,
                    'valor_unitario' => $valorUnitario,
                    'proxies' => [],
                ];
            }

            $grouped[$key]['proxies'][] = $proxy;
        }

        // Processar cada grupo (1 cobrança por usuário+período)
        foreach ($grouped as $group) {
            $user = $group['user'];
            $periodo = $group['periodo'];
            $proxiesDoGrupo = $group['proxies'];
            $valorUnitario = $group['valor_unitario'];
            $valorTotal = $valorUnitario * count($proxiesDoGrupo);
            $processed += count($proxiesDoGrupo);

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
                $skipped += count($proxiesDoGrupo);
                Log::warning('Auto-renew skip: no valid cards', [
                    'user_id' => $user->id,
                    'proxy_count' => count($proxiesDoGrupo),
                ]);
                continue;
            }

            $proxyIds = collect($proxiesDoGrupo)->pluck('id')->toArray();
            $proxyDetails = collect($proxiesDoGrupo)->map(fn ($p) => [
                'id' => $p->id,
                'ip' => $p->ip,
                'porta' => $p->porta,
                'expiracao_anterior' => $p->expiracao,
            ])->toArray();

            $metadata = [
                'auto_renew' => true,
                'batch' => true,
                'proxy_ids' => $proxyIds,
                'proxy_details' => $proxyDetails,
                'periodo_adicional' => $periodo,
                'quantidade' => count($proxiesDoGrupo),
                'valor_unitario' => $valorUnitario,
            ];

            if ($dryRun) {
                $this->line(sprintf(
                    'DRY RUN: would charge user #%d for %d proxies (%d days) = R$%.2f',
                    $user->id,
                    count($proxiesDoGrupo),
                    $periodo,
                    $valorTotal
                ));
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

            $proxyListStr = collect($proxiesDoGrupo)
                ->map(fn ($p) => "{$p->ip}:{$p->porta}")
                ->implode(', ');

            $paymentMethods = $cards->pluck('token_gateway1')->all();
            $result = $stripeService->chargeWithFallback([
                'amount' => (int) round($valorTotal * 100),
                'description' => sprintf(
                    'Renovacao automatica %d proxy(s) - %d dias [%s]',
                    count($proxiesDoGrupo),
                    $periodo,
                    mb_strimwidth($proxyListStr, 0, 200, '...')
                ),
                'customer' => $stripeService->formatCustomer($user),
                'metadata' => [
                    'auto_renew' => true,
                    'proxy_count' => count($proxiesDoGrupo),
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

                    // Renovar todos os proxies do grupo
                    $renovados = [];
                    foreach ($proxiesDoGrupo as $proxy) {
                        $renewalService->renewProxy($proxy, $periodo);
                        $renovados[] = [
                            'id' => $proxy->id,
                            'expiracao_nova' => $proxy->fresh()->expiracao,
                        ];
                    }

                    $transacao->update([
                        'status' => 1,
                        'gateway_transaction_id' => $result['data']['id'] ?? null,
                        'gateway_type' => 'stripe',
                        'card_id' => $usedCard?->id,
                        'stock_ids' => $proxyIds,
                        'metadata' => array_merge($metadata, [
                            'renovados' => $renovados,
                        ]),
                    ]);

                    DB::commit();
                    MetaConversionService::purchase($user, $transacao);
                    $charged += count($proxiesDoGrupo);
                    $this->info(sprintf(
                        'Auto-renew success: user #%d, %d proxies, R$%.2f',
                        $user->id,
                        count($proxiesDoGrupo),
                        $valorTotal
                    ));
                } catch (\Exception $e) {
                    DB::rollBack();
                    $failed += count($proxiesDoGrupo);
                    Log::error('Auto-renew failed after charge', [
                        'user_id' => $user->id,
                        'proxy_ids' => $proxyIds,
                        'error' => $e->getMessage(),
                    ]);
                }
                continue;
            }

            $failed += count($proxiesDoGrupo);
            $metadata['failed_at'] = now()->toIso8601String();
            $metadata['last_error'] = $result['error'] ?? 'Charge failed';
            $transacao->status = 2;
            $transacao->metadata = $metadata;
            $transacao->save();
            Log::warning('Auto-renew batch charge failed', [
                'user_id' => $user->id,
                'proxy_ids' => $proxyIds,
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
