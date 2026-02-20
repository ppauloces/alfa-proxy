<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Transaction;
use App\Services\AbacatePayService;
use App\Services\AsaasService;
use App\Services\ProxyAllocationService;
use App\Services\ProxyRenewalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Webhook do AbacatePay para notificações de pagamento
     */
    public function abacatepay(Request $request, AbacatePayService $abacatePay, ProxyAllocationService $proxyService, ProxyRenewalService $renewalService)
    {
        try {
            $payload = $request->all();

            Log::info('Webhook AbacatePay recebido', ['payload' => $payload]);

            // Validar webhook
            if (!$abacatePay->validateWebhook($payload)) {
                Log::warning('Webhook AbacatePay inválido', ['payload' => $payload]);
                return response()->json(['error' => 'Invalid webhook'], 400);
            }

            $event = $payload['event'] ?? null;

            // Só processar eventos de pagamento confirmado
            if ($event !== 'billing.paid') {
                Log::info('Evento ignorado (não é billing.paid)', ['event' => $event]);
                return response()->json(['ok' => true], 200);
            }

            // Processar dados do webhook
            $webhookData = $abacatePay->processWebhook($payload);

            // Buscar transação pelo external_id
            $transacao = Transaction::where('transacao', $webhookData['external_id'])->first();

            if (!$transacao) {
                Log::error('Transação não encontrada no webhook', [
                    'external_id' => $webhookData['external_id'],
                ]);
                return response()->json(['error' => 'Transaction not found'], 404);
            }

            // Se já foi processada, retornar sucesso
            if ($transacao->status == 1) {
                Log::info('Transação já processada', ['transaction_id' => $transacao->id]);
                return response()->json(['message' => 'Already processed'], 200);
            }

            // Se o pagamento foi confirmado
            if ($webhookData['paid']) {
                DB::beginTransaction();

                try {
                    // Atualizar status da transação
                    $transacao->status = 1; // Pago
                    $transacao->save();

                    $metadata = $transacao->metadata;

                    // Verificar se é renovação ou compra
                    if ($transacao->tipo === 'renovacao' && isset($metadata['proxy_id'])) {
                        // Processar renovação de proxy
                        $proxy = Stock::find($metadata['proxy_id']);

                        if (!$proxy) {
                            Log::error('Proxy não encontrado para renovação', [
                                'transaction_id' => $transacao->id,
                                'proxy_id' => $metadata['proxy_id'],
                            ]);
                            throw new \Exception('Proxy não encontrado para renovação');
                        }

                        // Renovar proxy (estende expiração + desbloqueia via API se necessário)
                        $proxy = $renewalService->renewProxy($proxy, (int) $metadata['periodo_adicional']);

                        DB::commit();


                        return response()->json([
                            'success' => true,
                            'message' => 'Renewal processed successfully',
                            'proxy_renewed' => [
                                'id' => $proxy->id,
                                'ip' => $proxy->ip,
                                'porta' => $proxy->porta,
                                'expiracao' => $proxy->expiracao,
                            ],
                        ], 200);

                    } else {
                        // Processar compra de proxies
                        $proxiesAlocados = $proxyService->allocateProxies($transacao->user_id, [
                            'pais' => $metadata['pais'],
                            'quantidade' => (int) $metadata['quantidade'],
                            'periodo_dias' => (int) $metadata['periodo'],
                            'motivo' => $metadata['motivo'],
                        ]);

                        // Salvar quais proxies foram alocados nesta transação
                        $metadata['proxy_ids'] = collect($proxiesAlocados)->pluck('id')->toArray();
                        $transacao->metadata = $metadata;
                        $transacao->save();

                        DB::commit();

                        Log::info('Pagamento AbacatePay processado com sucesso', [
                            'transaction_id' => $transacao->id,
                            'proxies_alocados' => count($proxiesAlocados),
                        ]);

                        return response()->json([
                            'success' => true,
                            'message' => 'Payment processed successfully',
                            'proxies_allocated' => count($proxiesAlocados),
                        ], 200);
                    }

                } catch (\Exception $e) {
                    DB::rollBack();

                    Log::error('Erro ao processar pagamento AbacatePay', [
                        'transaction_id' => $transacao->id,
                        'error' => $e->getMessage(),
                    ]);

                    return response()->json(['error' => 'Processing error'], 500);
                }
            }

            return response()->json(['message' => 'Webhook received'], 200);

        } catch (\Exception $e) {
            Log::error('Erro no webhook AbacatePay', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => 'Webhook error'], 500);
        }
    }

    /**
     * Webhook do Asaas para notificações de pagamento PIX
     */
    public function asaas(Request $request, AsaasService $asaasService, ProxyAllocationService $proxyService, ProxyRenewalService $renewalService)
    {
        try {
            $payload = $request->all();

            Log::info('Webhook Asaas recebido', ['payload' => $payload]);

            // Validar webhook
            if (!$asaasService->validateWebhook($payload)) {
                Log::warning('Webhook Asaas inválido', ['payload' => $payload]);
                return response()->json(['error' => 'Invalid webhook'], 400);
            }

            $event = $payload['event'] ?? null;

            // Só processar eventos de pagamento confirmado
            $paidEvents = ['PAYMENT_CONFIRMED', 'PAYMENT_RECEIVED'];
            if (!in_array($event, $paidEvents)) {
                Log::info('Evento Asaas ignorado (não é pagamento confirmado)', ['event' => $event]);
                return response()->json(['ok' => true], 200);
            }

            // Processar dados do webhook
            $webhookData = $asaasService->processWebhook($payload);

            // Buscar transação pelo external_id
            $transacao = Transaction::where('transacao', $webhookData['external_id'])->first();

            if (!$transacao) {
                Log::error('Transação não encontrada no webhook Asaas', [
                    'external_id' => $webhookData['external_id'],
                    'billing_id' => $webhookData['billing_id'],
                ]);
                return response()->json(['error' => 'Transaction not found'], 404);
            }

            // Se já foi processada, retornar sucesso
            if ($transacao->status == 1) {
                Log::info('Transação já processada (Asaas)', ['transaction_id' => $transacao->id]);
                return response()->json(['message' => 'Already processed'], 200);
            }

            // Se o pagamento foi confirmado
            if ($webhookData['paid']) {
                DB::beginTransaction();

                try {
                    // Atualizar status da transação
                    $transacao->status = 1; // Pago
                    $transacao->save();

                    $metadata = $transacao->metadata;

                    // Verificar se é renovação ou compra
                    if ($transacao->tipo === 'renovacao' && isset($metadata['proxy_id'])) {
                        // Processar renovação de proxy
                        $proxy = Stock::find($metadata['proxy_id']);

                        if (!$proxy) {
                            Log::error('Proxy não encontrado para renovação (Asaas)', [
                                'transaction_id' => $transacao->id,
                                'proxy_id' => $metadata['proxy_id'],
                            ]);
                            throw new \Exception('Proxy não encontrado para renovação');
                        }

                        // Renovar proxy (estende expiração + desbloqueia via API se necessário)
                        $proxy = $renewalService->renewProxy($proxy, (int) $metadata['periodo_adicional']);

                        DB::commit();

                        Log::info('Renovação processada com sucesso via webhook Asaas', [
                            'transaction_id' => $transacao->id,
                            'proxy_id' => $proxy->id,
                            'proxy_ip' => $proxy->ip,
                            'proxy_porta' => $proxy->porta,
                            'nova_expiracao' => $proxy->expiracao,
                            'estava_bloqueado' => $metadata['estava_bloqueado'] ?? false,
                        ]);

                        return response()->json([
                            'success' => true,
                            'message' => 'Renewal processed successfully',
                            'proxy_renewed' => [
                                'id' => $proxy->id,
                                'ip' => $proxy->ip,
                                'porta' => $proxy->porta,
                                'expiracao' => $proxy->expiracao,
                            ],
                        ], 200);

                    } else {
                        // Processar compra de proxies
                        $proxiesAlocados = $proxyService->allocateProxies($transacao->user_id, [
                            'pais' => $metadata['pais'],
                            'quantidade' => (int) $metadata['quantidade'],
                            'periodo_dias' => (int) $metadata['periodo'],
                            'motivo' => $metadata['motivo'],
                        ]);

                        // Salvar quais proxies foram alocados nesta transação
                        $metadata['proxy_ids'] = collect($proxiesAlocados)->pluck('id')->toArray();
                        $transacao->metadata = $metadata;
                        $transacao->save();

                        DB::commit();

                        Log::info('Pagamento Asaas processado com sucesso', [
                            'transaction_id' => $transacao->id,
                            'proxies_alocados' => count($proxiesAlocados),
                        ]);

                        return response()->json([
                            'success' => true,
                            'message' => 'Payment processed successfully',
                            'proxies_allocated' => count($proxiesAlocados),
                        ], 200);
                    }

                } catch (\Exception $e) {
                    DB::rollBack();

                    Log::error('Erro ao processar pagamento Asaas', [
                        'transaction_id' => $transacao->id,
                        'error' => $e->getMessage(),
                    ]);

                    return response()->json(['error' => 'Processing error'], 500);
                }
            }

            return response()->json(['message' => 'Webhook received'], 200);

        } catch (\Exception $e) {
            Log::error('Erro no webhook Asaas', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => 'Webhook error'], 500);
        }
    }
}
