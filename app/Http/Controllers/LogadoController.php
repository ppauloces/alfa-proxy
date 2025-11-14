<?php

namespace App\Http\Controllers;

use App\Models\Coupom;
use App\Models\Stock;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class LogadoController extends Controller
{
    // Dashboard principal - Gerenciar Proxies
    public function dash(Request $request)
    {
        $usuario = User::where('id', Auth::id())->first();

        // Buscar proxies do usuário agrupados por tipo
        $stocks = Stock::where('user_id', Auth::id())->get();

        $proxyGroups = [
            'SOCKS5' => [],
            'HTTP' => [],
            'HTTPS' => [],
        ];

        foreach ($stocks as $stock) {
            $tipo = strtoupper($stock->tipo ?? 'SOCKS5');
            if (!isset($proxyGroups[$tipo])) {
                $proxyGroups[$tipo] = [];
            }

            $proxyGroups[$tipo][] = [
                'id' => $stock->id,
                'ip' => $stock->ip,
                'port' => $stock->porta,
                'user' => $stock->usuario,
                'password' => $stock->senha,
                'country' => $stock->pais ?? 'Brasil',
                'country_code' => $stock->codigo_pais ?? 'BR',
                'purchased_at' => $stock->created_at,
                'expires_at' => $stock->expiracao,
                'remaining' => Carbon::parse($stock->expiracao)->diffForHumans(),
                'auto_renew' => $stock->renovacao_automatica ?? false,
            ];
        }

        // Remover grupos vazios
        $proxyGroups = array_filter($proxyGroups, function($proxies) {
            return count($proxies) > 0;
        });

        // Se não houver proxies, criar grupo padrão vazio
        if (empty($proxyGroups)) {
            $proxyGroups = ['SOCKS5' => []];
        }

        $pagamentos = Transaction::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        $pagamentos_aprovados = $pagamentos->where('status', 1);
        $pagamentos_pendentes = $pagamentos->where('status', 0);
        $totalValor = $pagamentos_aprovados->sum('valor');
        $transacoes = $pagamentos;
        $activeSection = $request->query('section', 'proxies');

        return view('dash.index', compact(
            'usuario',
            'proxyGroups',
            'pagamentos',
            'pagamentos_aprovados',
            'pagamentos_pendentes',
            'totalValor',
            'transacoes',
            'activeSection',
            'users',
        ));
    }

    // Meu Perfil
    public function perfil()
    {
        return redirect()->route('dash.show', ['section' => 'perfil']);
    }

    public function atualizarPerfil(Request $request)
    {
        $request->validateWithBag('perfil', [
            'name' => 'required|string|max:255',
        ]);

        $usuario = User::find(Auth::id());
        $usuario->name = $request->name;
        $usuario->save();

        return redirect()
            ->route('dash.show', ['section' => 'perfil'])
            ->with('perfil_success', 'Perfil atualizado com sucesso!');
    }

    public function alterarSenha(Request $request)
    {
        $request->validateWithBag('alterarSenha', [
            'senha_atual' => 'required',
            'nova_senha' => 'required|min:6|confirmed',
        ]);

        $usuario = User::find(Auth::id());

        if (!Hash::check($request->senha_atual, $usuario->password)) {
            return back()->withErrors(['senha_atual' => 'Senha atual incorreta.'], 'alterarSenha');
        }

        $usuario->password = Hash::make($request->nova_senha);
        $usuario->save();

        return redirect()
            ->route('dash.show', ['section' => 'perfil'])
            ->with('perfil_success', 'Senha alterada com sucesso!');
    }

    // Proxies Ativos
    public function proxies()
    {
        return redirect()->route('dash.show', ['section' => 'proxies']);
    }

    public function renovarProxy(Request $request)
    {
        $stock = Stock::where('id', $request->proxy_id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$stock) {
            return back()->withErrors(['error' => 'Proxy não encontrado.']);
        }

        $stock->renovacao_automatica = $request->auto_renew;
        $stock->save();

        return back()->with('success', 'Configuração de renovação atualizada!');
    }

    public function exportarProxies(Request $request)
    {
        $proxyIds = $request->proxy_ids ?? [];
        $stocks = Stock::whereIn('id', $proxyIds)
            ->where('user_id', Auth::id())
            ->get();

        $formato = $request->formato ?? 'ip:porta:usuario:senha';
        $conteudo = '';

        foreach ($stocks as $stock) {
            $linha = str_replace(
                ['ip', 'porta', 'usuario', 'senha'],
                [$stock->ip, $stock->porta, $stock->usuario, $stock->senha],
                $formato
            );
            $conteudo .= $linha . "\n";
        }

        return response($conteudo)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', 'attachment; filename="proxies.txt"');
    }

    // Nova Compra
    public function novaCompra()
    {
        return redirect()->route('dash.show', ['section' => 'nova-compra']);
    }

    public function processarCompra(Request $request)
    {
        $request->validateWithBag('novaCompra', [
            'pais' => 'required',
            'motivo' => 'required',
            'periodo' => 'required|integer',
            'quantidade' => 'required|integer|min:1',
            'metodo_pagamento' => 'required',
        ]);

        // Calcular valor baseado no período
        $precos = [
            30 => 20.00,
            60 => 35.00,
            90 => 45.00,
            180 => 80.00,
            360 => 120.00,
        ];

        $valorUnitario = $precos[$request->periodo] ?? 20.00;
        $valorTotal = $valorUnitario * $request->quantidade;

        // Criar transação
        $transacao = Transaction::create([
            'user_id' => Auth::id(),
            'valor' => $valorTotal,
            'status' => 0, // Pendente
            'metodo_pagamento' => $request->metodo_pagamento,
        ]);

        return redirect()
            ->route('dash.show', ['section' => 'transacoes'])
            ->with('transacoes_success', 'Pedido criado! Aguardando pagamento.');
    }
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

    // Carteira / Saldo
    public function saldo()
    {
        return redirect()->route('dash.show', ['section' => 'saldo']);
    }

    public function adicionarSaldo(Request $request)
    {
        $request->validateWithBag('saldo', [
            'valor' => 'required|numeric|min:1',
            'metodo_pagamento' => 'required',
        ]);

        $transacao = Transaction::create([
            'user_id' => Auth::id(),
            'valor' => $request->valor,
            'status' => 0, // Pendente
            'metodo_pagamento' => $request->metodo_pagamento,
        ]);

        return redirect()->route('saldo.show')->with('success', 'Solicitação de recarga criada! Aguardando pagamento.');
    }

    // Histórico de Transações
    public function transacoes()
    {
        return redirect()->route('dash.show', ['section' => 'transacoes']);
    }

    public function cupons()
    {
        $usuario = User::where('id', Auth::id())->first();
        $cupons = Coupom::all();

        return view('logado.cupons', compact('usuario', 'cupons'));
    }

    // Outras páginas
    public function suporte()
    {
        return redirect()->route('dash.show', ['section' => 'suporte']);
    }

    public function configuracoes()
    {
        return redirect()->route('dash.show', ['section' => 'configuracoes']);
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
}
