<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;


class PostbackController extends Controller
{

    //postback para recarregar saldo logado
    public function handle(Request $request)
    {
        
        $payload = $request->all();

        // Acessa os dados da transação
        $transacaoId = $payload['data']['id'] ?? null;
        $status = $payload['data']['status'] ?? null;
        $valor = $payload['data']['amount'] ?? null;

        // Evita continuar sem dados obrigatórios
        if (!$transacaoId || !$status || !$valor) {
            return response()->json(['error' => 'Dados inválidos ou incompletos.'], 400);
        }

        // Busca a transação
        $transacao = Transaction::where('transacao', $transacaoId)->first();

        if ($transacao->status == 1) {
            return response()->json(['success' => 'Transacao ja foi aceita.']);
        } else {

        }
            
        if (!$transacao) {
            return response()->json(['error' => 'Transação não encontrada.'], 404);
        }

        // Atualiza status da transação
        $transacao->status = ($status === 'paid') ? 1 : 0;
        $transacao->save();

        // Atualiza saldo do usuário dono da transação
        $user = User::find($transacao->user_id); // ou outro campo de referência

        if ($user) {
            $user->saldo = $user->saldo + ($valor / 100); // Divide por 100 se vier em centavos
            $user->save();
        }

        return response()->json(['success' => true]);
    }

    
}
