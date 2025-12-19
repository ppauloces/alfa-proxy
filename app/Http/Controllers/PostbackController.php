<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class PostbackController extends Controller
{

    //postback para processar pagamentos do Aprovei
    public function handle(Request $request)
    {

        $payload = $request->all();

        Log::info('Postback recebido do Aprovei', ['payload' => $payload]);

        // Acessa os dados da transação
        $aproveiTransactionId = $payload['data']['id'] ?? null;
        $status = $payload['data']['status'] ?? null;
        $valor = $payload['data']['amount'] ?? null;
        $externalRef = $payload['data']['externalRef'] ?? null;
        $paymentMethod = $payload['data']['paymentMethod'] ?? null;

        // Evita continuar sem dados obrigatórios
        if (!$aproveiTransactionId || !$status || !$valor) {
            Log::error('Postback com dados incompletos', ['payload' => $payload]);
            return response()->json(['error' => 'Dados inválidos ou incompletos.'], 400);
        }

        // Busca a transação pela referência externa ou ID da transação Aprovei
        $transacao = Transaction::where('transacao', $externalRef)
            ->orWhere('gateway_transaction_id', $aproveiTransactionId)
            ->first();

        if (!$transacao) {
            Log::error('Transação não encontrada', [
                'externalRef' => $externalRef,
                'aproveiTransactionId' => $aproveiTransactionId,
            ]);
            return response()->json(['error' => 'Transação não encontrada.'], 404);
        }

        // Verificar se já foi processada
        if ($transacao->status == 1) {
            Log::info('Transação já foi processada', ['transaction_id' => $transacao->id]);
            return response()->json(['success' => 'Transacao ja foi aceita.']);
        }

        // Atualizar ID da transação no gateway se ainda não tiver
        if (!$transacao->gateway_transaction_id) {
            $transacao->gateway_transaction_id = $aproveiTransactionId;
        }

        // Atualizar status da transação baseado no status do Aprovei
        $isPaid = in_array($status, ['paid', 'approved']);
        $transacao->status = $isPaid ? 1 : 0;
        $transacao->payment_method = $paymentMethod;
        $transacao->save();

        // Se pagamento aprovado, processar a transação
        if ($isPaid) {
            $user = User::find($transacao->user_id);

            if ($user) {
                // Adicionar saldo ao usuário
                $valorReal = $valor / 100; // Converter centavos para reais
                $user->saldo = $user->saldo + $valorReal;
                $user->save();

                Log::info('Saldo adicionado ao usuário', [
                    'user_id' => $user->id,
                    'valor' => $valorReal,
                    'novo_saldo' => $user->saldo,
                ]);
            }
        } else {
            Log::warning('Pagamento não aprovado', [
                'transaction_id' => $transacao->id,
                'status' => $status,
            ]);
        }

        return response()->json(['success' => true]);
    }


}
