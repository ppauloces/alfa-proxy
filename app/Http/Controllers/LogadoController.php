<?php

namespace App\Http\Controllers;

use App\Models\Coupom;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;


class LogadoController extends Controller
{
    public function dashboard()
    {
        $usuario = User::where('id', Auth::id())->first();

        $expiracao = $usuario->expiracao;

        if($usuario->plano == "Grátis") {
            $expiracao = "Nunca";
        } else {
            $expiracao = Carbon::parse($expiracao)->format('d/m/Y');
        }

        return view('logado.painel', compact('usuario', 'expiracao'));
    }

    public function saldo()
    {
        $usuario = User::where('id', Auth::id())->first();
        return view('logado.saldo', compact('usuario'));
    }

    public function socks5()
    {
        $usuario = User::where('id', Auth::id())->first();
        return view('logado.socks5', compact('usuario'));
    }

    public function transacoes()
    {
        $usuario = User::where('id', Auth::id())->first();

        $pagamentos_aprovados = Transaction::where('id', Auth::id())->where('status', 1)->get();

        $pagamentos_pendentes = Transaction::where('id', Auth::id())->where('status', 0)->get();

        $pagamentos = Transaction::where('id', Auth::id())->get();

        $totalValor = Transaction::where('user_id', Auth::id())
        ->where('status', 1)
        ->sum('valor');

        return view('logado.transacoes', compact('usuario', 'pagamentos_aprovados', 'pagamentos_pendentes', 'pagamentos', 'totalValor'));
    }

    public function cupons()
    {
        $usuario = User::where('id', Auth::id())->first();
        $cupons = Coupom::all();

        return view('logado.cupons', compact('usuario', 'cupons'));
    }

    public function duvidas()
    {
        $usuario = User::where('id', Auth::id())->first();
        return view('logado.duvidas', compact('usuario'));
    }

    public function api()
    {
        $usuario = User::where('id', Auth::id())->first();
        return view('logado.api', compact('usuario'));
    }

        public function comprar_proxies()
    {
        $usuario = User::where('id', Auth::id())->first();
        return view('logado.comprar-proxies', compact('usuario'));
    }

        public function pagamento()
    {
        $usuario = User::where('id', Auth::id())->first();
        return view('logado.pagamento', compact('usuario'));
    }
}
