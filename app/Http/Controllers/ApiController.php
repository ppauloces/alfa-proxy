<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

use function Pest\Laravel\json;

class ApiController extends Controller
{
    public function transacao_status($transacao_id)
    {
        $transacao = Transaction::where('transacao', $transacao_id)->first();

        return response()->json([
            'status' => $transacao->status,
        ]);

    }
}
