<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

use function Pest\Laravel\json;

class ApiController extends Controller
{
    public function transacao_status($transacao_id)
    {
        // Aceita tanto ID numérico quanto código de transação
        $transacao = Transaction::where('id', $transacao_id)
            ->orWhere('transacao', $transacao_id)
            ->first();

        if (!$transacao) {
            return response()->json([
                'status' => null,
                'error' => 'Transação não encontrada',
            ], 404);
        }

        return response()->json([
            'status' => $transacao->status,
        ]);
    }
}
