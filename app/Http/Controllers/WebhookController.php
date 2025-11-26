<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\AbacatePayService;
use App\Services\ProxyAllocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Webhook do AbacatePay para notificações de pagamento
     */
    public function abacatepay(Request $request, AbacatePayService $abacatePay, ProxyAllocationService $proxyService)
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

                    // Alocar proxies ao usuário
                    $metadata = $transacao->metadata;
                    $proxiesAlocados = $proxyService->allocateProxies($transacao->user_id, [
                        'pais' => $metadata['pais'],
                        'quantidade' => (int) $metadata['quantidade'],
                        'periodo_dias' => (int) $metadata['periodo'],
                        'motivo' => $metadata['motivo'],
                    ]);

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
}
