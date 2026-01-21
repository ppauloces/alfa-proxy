<?php

namespace App\Http\Controllers;

use App\Models\Coupom;
use App\Models\Stock;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Cartao;
use App\Models\Despesa;
use App\Services\ProxyAllocationService;
use App\Services\AsaasService;
use App\Services\AbacatePayService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;


class LogadoController extends Controller
{
    // Dashboard principal - Gerenciar Proxies
    public function dash(Request $request)
    {
        $usuario = User::where('id', Auth::id())->first();

        // Se for admin, precisamos dos dados do painel admin também
        $vpsFarm = collect();
        $vpsHistorico = collect();
        $estatisticas = [
            'total_vps' => 0,
            'vps_ativas' => 0,
            'vps_expiradas' => 0,
            'total_gasto' => 0,
            'total_proxies_geradas' => 0,
            'media_proxies_por_vps' => 0,
        ];
        $generatedProxies = [];
        // Stats internos (mantém compatibilidade caso alguma view/compact espere essa variável)
        $usoInternoStats = [];
        // Usuários + stats (admin - datatable)
        $clientLeads = collect();
        $statsCompraProxy = collect();

        // Variáveis para seções admin
        $financeCards = [];
        $financeExtract = ['saida' => [], 'entrada' => []];
        $forecast = [];
        $soldProxyCards = [];
        $soldProxies = [];

        if ($usuario->isAdmin()) {
            // ===== CLIENTES & LEADS (com busca + paginação) =====
            $usersQ = trim((string) $request->query('users_q', ''));
            $usersPerPage = (int) $request->query('users_per_page', 10);
            $usersPerPage = max(5, min(100, $usersPerPage));

            $clientLeads = User::query()
                ->whereIn('cargo', ['usuario', 'revendedor', 'super'])
                ->when($usersQ !== '', function ($q) use ($usersQ) {
                    $q->where(function ($qq) use ($usersQ) {
                        $qq->where('name', 'like', '%' . $usersQ . '%')
                            ->orWhere('email', 'like', '%' . $usersQ . '%');
                    });
                })
                ->orderBy('created_at', 'desc')
                ->paginate($usersPerPage, ['*'], 'users_page')
                ->withQueryString();

            // Stats agregadas das compras aprovadas (tipo compra_proxy): proxies (metadata.quantidade) + gasto (valor)
            $clientLeadIds = $clientLeads->getCollection()->pluck('id')->values();
            $statsCompraProxy = collect();
            if ($clientLeadIds->isNotEmpty()) {
                $qtyExpr = "CAST(JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.quantidade')) AS UNSIGNED)";

                $statsCompraProxy = Transaction::query()
                    ->where('tipo', 'compra_proxy')
                    ->where('status', 1)
                    ->whereIn('user_id', $clientLeadIds)
                    ->select('user_id')
                    ->selectRaw('SUM(valor) as gasto')
                    ->selectRaw("SUM(COALESCE($qtyExpr, 0)) as proxies")
                    ->groupBy('user_id')
                    ->get()
                    ->keyBy('user_id')
                    ->map(function ($row) {
                        return [
                            'proxies' => (int) ($row->proxies ?? 0),
                            'gasto' => (float) ($row->gasto ?? 0),
                        ];
                    });
            }

            $vpsList = \App\Models\Vps::with('proxies')->orderBy('created_at', 'desc')->get();

            $vpsFarm = $vpsList->map(function ($vps) {
                return (object) [
                    'id' => $vps->id,
                    'apelido' => $vps->apelido,
                    'ip' => $vps->ip,
                    'pais' => $vps->pais,
                    'hospedagem' => $vps->hospedagem,
                    'valor' => 'R$ ' . number_format($vps->valor, 2, ',', '.'),
                    'valor_raw' => $vps->valor, // Valor numérico para edição
                    'valor_renovacao' => $vps->valor_renovacao, // Valor de renovação para edição
                    'periodo' => $vps->periodo_dias . ' dias',
                    'periodo_dias' => $vps->periodo_dias, // Período em dias para edição
                    'contratada' => $vps->data_contratacao->format('d/m/Y'),
                    'data_contratacao' => $vps->data_contratacao, // Data Carbon para edição
                    'status' => $vps->status,
                    'proxies' => $vps->proxies,
                    'usuario_ssh' => $vps->usuario_ssh,
                    'senha_ssh' => $vps->senha_ssh,
                ];
            });

            $vpsHistorico = $vpsList->map(function ($vps) {
                $dataExpiracao = $vps->data_contratacao->addDays($vps->periodo_dias);
                $diasRestantes = now()->diffInDays($dataExpiracao, false);

                $statusExpiracao = 'Ativa';
                $badgeExpiracao = 'bg-green-100 text-green-700';

                if ($diasRestantes < 0) {
                    $statusExpiracao = 'Expirada';
                    $badgeExpiracao = 'bg-red-100 text-red-700';
                } elseif ($diasRestantes <= 5) {
                    $statusExpiracao = 'Expira em breve';
                    $badgeExpiracao = 'bg-amber-100 text-amber-700';
                }

                return (object) [
                    'id' => $vps->id,
                    'apelido' => $vps->apelido,
                    'ip' => $vps->ip,
                    'pais' => $vps->pais,
                    'hospedagem' => $vps->hospedagem,
                    'valor_formatado' => 'R$ ' . number_format($vps->valor, 2, ',', '.'),
                    'periodo_dias' => $vps->periodo_dias,
                    'data_contratacao' => $vps->data_contratacao->format('d/m/Y'),
                    'data_expiracao' => $dataExpiracao->format('d/m/Y'),
                    'status_expiracao' => $statusExpiracao,
                    'badge_expiracao' => $badgeExpiracao,
                    'status' => $vps->status,
                    'total_proxies' => $vps->proxies->count(),
                    'proxies_geradas' => $vps->proxies_geradas,
                    'status_geracao' => $vps->status_geracao,
                    'erro_geracao' => $vps->erro_geracao,
                ];
            });

            $estatisticas = [
                'total_vps' => $vpsList->count(),
                'vps_ativas' => $vpsList->filter(fn($v) => $v->data_contratacao->addDays($v->periodo_dias)->isFuture())->count(),
                'vps_expiradas' => $vpsList->filter(fn($v) => $v->data_contratacao->addDays($v->periodo_dias)->isPast())->count(),
                'total_gasto' => $vpsList->sum('valor'),
                'total_proxies_geradas' => $vpsList->sum('proxies_geradas'),
                'media_proxies_por_vps' => $vpsList->count() > 0 ? round($vpsList->sum('proxies_geradas') / $vpsList->count(), 1) : 0,
            ];

            $generatedProxies = Stock::with('vps')
                ->whereNotNull('vps_id')
                ->orderBy('created_at', 'desc')
                ->limit(25)
                ->get()
                ->map(function ($proxy) {
                    return [
                        'numero' => '#' . str_pad($proxy->id, 3, '0', STR_PAD_LEFT),
                        'endereco' => $proxy->ip . ':' . $proxy->porta,
                        'user' => $proxy->usuario,
                        'senha' => $proxy->senha,
                        'vps' => $proxy->vps ? $proxy->vps->apelido : 'N/A',
                        'status' => $proxy->disponibilidade ? 'Disponivel' : 'Vendida',
                    ];
                })->toArray();

            // ===== DADOS PARA RELATÓRIOS FINANCEIROS =====
            $totalEntradas = Transaction::where('status', 1)->sum('valor');
            $totalSaidas = Despesa::whereIn('status', ['pago', 'pendente'])->sum('valor');
            $saldoDisponivel = User::sum('saldo');
            $lucroLiquido = $totalEntradas - $totalSaidas;

            // Estatísticas de revendedores
            $revendedoresIds = User::where('cargo', 'revendedor')->pluck('id');
            $totalEntradasRevendedores = Transaction::where('status', 1)
                ->whereIn('user_id', $revendedoresIds)
                ->sum('valor');
            $proxiesVendidasRevendedores = Stock::whereIn('user_id', $revendedoresIds)
                ->where('disponibilidade', false)
                ->count();

            $financeCards = [
                [
                    'label' => 'Total Entradas',
                    'value' => 'R$ ' . number_format($totalEntradas, 2, ',', '.'),
                    'trend' => '+' . Transaction::where('status', 1)->whereMonth('created_at', now()->month)->count() . ' este mês',
                    'bar' => 100,
                ],
                [
                    'label' => 'Total Saídas',
                    'value' => 'R$ ' . number_format($totalSaidas, 2, ',', '.'),
                    'trend' => Despesa::whereMonth('created_at', now()->month)->count() . ' despesas este mês',
                    'bar' => $totalEntradas > 0 ? ($totalSaidas / $totalEntradas) * 100 : 0,
                ],
                [
                    'label' => 'Lucro Líquido',
                    'value' => 'R$ ' . number_format($lucroLiquido, 2, ',', '.'),
                    'trend' => $lucroLiquido >= 0 ? 'Positivo' : 'Negativo',
                    'bar' => $totalEntradas > 0 ? ($lucroLiquido / $totalEntradas) * 100 : 0,
                ],
                [
                    'label' => 'Vendas Revendedores',
                    'value' => 'R$ ' . number_format($totalEntradasRevendedores, 2, ',', '.'),
                    'trend' => $proxiesVendidasRevendedores . ' ' . ($proxiesVendidasRevendedores === 1 ? 'proxy vendida' : 'proxies vendidas'),
                    'bar' => $totalEntradas > 0 ? ($totalEntradasRevendedores / $totalEntradas) * 100 : 0,
                ],
            ];


            // ===== EXTRATO DE SAÍDAS (Últimas 25 despesas) =====
            $saidas = Despesa::with('vps')
                ->orderBy('data_vencimento', 'desc')
                ->limit(25)
                ->get()
                ->map(function ($despesa) {
                    $tipoLabel = match ($despesa->tipo) {
                        'compra' => 'Compra VPS',
                        'cobranca' => 'Cobrança',
                        'renovacao' => 'Renovação',
                        default => ucfirst($despesa->tipo),
                    };

                    return [
                        'descricao' => $despesa->descricao ?? 'VPS ' . ($despesa->vps->apelido ?? 'N/A'),
                        'categoria' => $tipoLabel,
                        'tipo' => $despesa->tipo,
                        'data' => $despesa->data_vencimento ? $despesa->data_vencimento->format('d/m/Y') : $despesa->created_at->format('d/m/Y'),
                        'valor' => '- R$ ' . number_format((float) $despesa->valor, 2, ',', '.'),
                        'status' => $despesa->status,
                        'vps_apelido' => $despesa->vps->apelido ?? 'N/A',
                    ];
                });


            // ===== EXTRATO DE ENTRADAS (Últimas 25 transações aprovadas) =====
            $entradas = Transaction::with('user')
                ->where('status', 1)
                ->orderBy('created_at', 'desc')
                ->limit(25)
                ->get()
                ->map(function ($transacao) {
                    // Determinar tipo de transação
                    $tipo = $transacao->tipo ?? 'compra_proxy';
                    $tipoLabel = match ($tipo) {
                        'compra_proxy' => 'Compra de Proxy',
                        'recarga' => 'Recarga de Saldo',
                        default => ucfirst($tipo),
                    };


                    // Determinar método de pagamento
                    $metodo = $transacao->metodo_pagamento ?? 'saldo';

                    $metodoLabel = match ($metodo) {
                        'pix' => 'PIX',
                        'credit_card' => 'Cartão de Crédito',
                        'saldo' => 'Saldo',
                        'boleto' => 'Boleto',
                        'usdt' => 'USDT',
                        'btc' => 'Bitcoin',
                        'ltc' => 'Litecoin',
                        'bnb' => 'Binance',
                        default => ucfirst($metodo),
                    };

                    // Identificar se é revendedor
                    $isRevendedor = $transacao->user && $transacao->user->cargo === 'revendedor';
                    $username = $transacao->user->username ?? 'Usuário';

                    $descricao = $tipoLabel . ' - ' . $username;

                    return [
                        'descricao' => $descricao,
                        'categoria' => $metodoLabel,
                        'data' => $transacao->created_at->format('d/m/Y'),
                        'valor' => '+ R$ ' . number_format((float) $transacao->valor, 2, ',', '.'),
                        'is_revendedor' => $isRevendedor,
                        'user_id' => $transacao->user_id,
                    ];
                });

            $financeExtract = [
                'saida' => $saidas,
                'entrada' => $entradas,
            ];
            $precosPorPeriodo = [
                30 => 20.00,
                60 => 35.00,
                90 => 45.00,
                180 => 80.00,
                360 => 120.00,
            ];

            $proxiesDisponiveis = Stock::where('disponibilidade', true)
                ->where('uso_interno', false)
                ->count();
            $precoMedio = array_sum($precosPorPeriodo) / count($precosPorPeriodo);

            $potencialVenda = $proxiesDisponiveis * $precoMedio;


            $forecast = [
                [
                    'title' => 'Estoque Disponível',
                    'value' => number_format($proxiesDisponiveis, 0, ',', '.') . ' proxies',
                    'detail' => 'Prontos para venda',
                ],
                [
                    'title' => 'Potencial de Vendas',
                    'value' => 'R$ ' . number_format($potencialVenda, 2, ',', '.'),
                    'detail' => 'Baseado no preço médio',
                ],
                [
                    'title' => 'Saldo em Carteiras',
                    'value' => 'R$ ' . number_format($saldoDisponivel, 2, ',', '.'),
                    'detail' => 'Saldo total dos usuários',
                ],
            ];

            // ===== DADOS DE USO INTERNO =====
            $usoInternoProxies = Stock::where('uso_interno', true)
                ->with('vps')
                ->orderBy('updated_at', 'desc')
                ->get()
                ->map(function ($stock) {
                    return [
                        'endereco' => ($stock->vps->ip ?? $stock->ip ?? 'N/A') . ':' . $stock->porta,
                        'finalidade' => $stock->finalidade_interna ?? 'Não especificada',
                        'vps' => $stock->vps->apelido ?? 'N/A',
                        'data' => $stock->updated_at->format('d/m/Y H:i'),
                    ];
                });

            $usoInternoStats = [
                'total' => Stock::where('uso_interno', true)->count(),
                'proxies' => $usoInternoProxies,
            ];

            // ===== DADOS PARA TRANSAÇÕES (VENDAS) =====
            $proxiesVendidos = Stock::where('disponibilidade', false)->count();
            $proxiesAtivos = Stock::where('disponibilidade', false)->where('bloqueada', false)->count();
            $proxiesBloqueados = Stock::where('bloqueada', true)->count();
            $receitaTotal = Transaction::where('status', 1)->sum('valor');

            $soldProxyCards = [
                [
                    'label' => 'Total Vendidos',
                    'value' => number_format($proxiesVendidos, 0, ',', '.'),
                    'chip' => 'Proxies',
                ],
                [
                    'label' => 'Ativos',
                    'value' => number_format($proxiesAtivos, 0, ',', '.'),
                    'chip' => 'Em uso',
                ],
                [
                    'label' => 'Bloqueados',
                    'value' => number_format($proxiesBloqueados, 0, ',', '.'),
                    'chip' => 'Suspensos',
                ],
                [
                    'label' => 'Receita Total',
                    'value' => 'R$ ' . number_format($receitaTotal, 2, ',', '.'),
                    'chip' => 'Arrecadado',
                ],
            ];

            $soldProxies = Stock::with(['user', 'vps'])
                ->where('disponibilidade', false)
                ->orderBy('updated_at', 'desc')
                ->get()
                ->map(function ($proxy) {
                    $expiracao = $proxy->expiracao ? Carbon::parse($proxy->expiracao) : null;
                    $diasRestantes = $expiracao ? now()->diffInDays($expiracao, false) : 0;

                    $gastoCliente = Transaction::where('user_id', $proxy->user_id)
                        ->where('status', 1)
                        ->sum('valor');

                    $pedidos = Stock::where('user_id', $proxy->user_id)
                        ->where('disponibilidade', false)
                        ->count();

                    // Busca transações pagas por esse user, ordenadas por data decrescente
                    $transactions = Transaction::where('user_id', $proxy->user_id)
                        ->where('tipo', 'compra_proxy')
                        ->where('status', 1)
                        ->orderBy('created_at', 'desc')
                        ->get();

                    // Acha a transação mais próxima por tempo
                    $matchedTransaction = $transactions->first(function ($txn) use ($proxy) {
                        return abs(strtotime($txn->created_at) - strtotime($proxy->updated_at)) < 120; // 2 minutos de tolerância
                    });

                    $metadata = [];

                    if ($matchedTransaction) {
                        $metaRaw = $matchedTransaction->metadata;

                        if (is_string($metaRaw)) {
                            $metadata = json_decode($metaRaw, true);
                        } elseif (is_array($metaRaw)) {
                            $metadata = $metaRaw; // já é array, não decodifica de novo
                        }
                    }

                    return [
                        'id' => $proxy->id,
                        'stock_id' => $proxy->id,
                        'data' => $proxy->updated_at->format('d/m/Y'),
                        'endereco' => $proxy->ip . ':' . $proxy->porta,
                        'comprador' => $proxy->user->username ?? 'Anônimo',
                        'email' => $proxy->user->email ?? 'N/A',
                        'ip' => $proxy->ip,
                        'porta' => $proxy->porta,
                        'usuario' => $proxy->usuario,
                        'senha' => $proxy->senha,
                        'status' => $proxy->bloqueada ? 'bloqueada' : 'ativa',
                        'periodo' => $diasRestantes > 0 ? $diasRestantes . ' dias' : 'Expirado',
                        'gasto_cliente' => 'R$ ' . number_format($gastoCliente, 2, ',', '.'),
                        'pedidos' => $pedidos,

                        // Novo: dados da transação associada
                        'valor_unitario' => $metadata['valor_unitario'] ?? null,
                        'periodo_comprado' => $metadata['periodo'] ?? null,
                        'motivo' => $metadata['motivo'] ?? null,
                    ];

                })->toArray();
        }

        // Buscar proxies do usuário agrupados por tipo
        $stocks = Stock::where('user_id', Auth::id())->orderBy('id', 'desc')->get();

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
                'purchased_at' => $stock->updated_at,
                'expires_at' => $stock->expiracao,
                'remaining' => Carbon::parse($stock->expiracao)->diffForHumans(),
                'auto_renew' => $stock->renovacao_automatica ?? false,
            ];
        }

        // Remover grupos vazios
        $proxyGroups = array_filter($proxyGroups, function ($proxies) {
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

        // Buscar cartões salvos do usuário
        $cartoes = Cartao::where('user_id', Auth::id())
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $savedCards = $cartoes->map(function ($cartao) {
            return (object) [
                'id' => $cartao->id,
                'bandeira' => $cartao->bandeira,
                'ultimos_digitos' => $cartao->ultimos_digitos,
                'brand' => $cartao->bandeira,
                'last4' => $cartao->ultimos_digitos,
                'exp_month' => str_pad($cartao->mes_expiracao, 2, '0', STR_PAD_LEFT),
                'exp_year' => $cartao->ano_expiracao,
                'mes_expiracao' => str_pad($cartao->mes_expiracao, 2, '0', STR_PAD_LEFT), // Adicionar
                'ano_expiracao' => $cartao->ano_expiracao, // Adicionar
                'is_default' => $cartao->is_default,
                'nome_titular' => $cartao->nome_titular,
            ];
        });

        $savedCardsOptions = $savedCards->mapWithKeys(function ($card) {
            return [
                $card->id => ucfirst($card->bandeira) . ' •••• ' . $card->ultimos_digitos
            ];
        });


        return view('dash.index', compact(
            'usuario',
            'proxyGroups',
            'pagamentos',
            'pagamentos_aprovados',
            'pagamentos_pendentes',
            'totalValor',
            'transacoes',
            'activeSection',
            'savedCards',
            'savedCardsOptions',
            'vpsFarm',
            'vpsHistorico',
            'estatisticas',
            'generatedProxies',
            'financeCards',
            'financeExtract',
            'forecast',
            'soldProxyCards',
            'soldProxies',
            'usoInternoStats',
            'clientLeads',
            'statsCompraProxy',
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

    /**
     * Salvar dados iniciais (CPF e Telefone) - Modal de boas-vindas
     */
    public function salvarDadosIniciais(Request $request)
    {
        $request->validate([
            'cpf' => 'required|string|min:11|max:18',
            'phone' => 'required|string|min:10|max:20',
        ]);

        $usuario = User::find(Auth::id());

        // Remover formatacao do CPF (manter apenas numeros)
        $cpf = preg_replace('/\D/', '', $request->cpf);

        // Remover formatacao do telefone (manter apenas numeros)
        $phone = preg_replace('/\D/', '', $request->phone);

        $usuario->cpf = $cpf;
        $usuario->phone = $phone;
        $usuario->save();

        return response()->json([
            'success' => true,
            'message' => 'Dados salvos com sucesso!'
        ]);
    }

    // Proxies Ativos
    public function proxies()
    {
        return redirect()->route('dash.show', ['section' => 'proxies']);
    }

    public function renovarProxy(Request $request)
    {
        $request->validate([
            'proxy_id' => 'required|exists:stocks,id',
            'auto_renew' => 'required|boolean',
        ]);

        $stock = Stock::where('id', $request->proxy_id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$stock) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Proxy nao encontrado.',
                ], 404);
            }

            return back()->withErrors(['error' => 'Proxy nao encontrado.']);
        }

        $autoRenew = filter_var($request->auto_renew, FILTER_VALIDATE_BOOLEAN);
        $stock->renovacao_automatica = $autoRenew;
        $stock->save();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'auto_renew' => $stock->renovacao_automatica,
            ]);
        }

        return back()->with('success', 'Configuracao de renovacao atualizada!');
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

    public function processarCompra(Request $request, ProxyAllocationService $proxyService, AsaasService $asaas, AbacatePayService $abacatePay)
    {
        $request->validateWithBag('novaCompra', [
            'pais' => 'required',
            'motivo' => 'required',
            'periodo' => 'required|integer',
            'quantidade' => 'required|integer|min:1|max:100',
            'metodo_pagamento' => 'required',
            'card_id' => 'required_if:metodo_pagamento,credit_card|nullable|exists:cartaos,id',
            'installments' => 'nullable|integer|min:1|max:12',
        ]);

        // Obter usuário e calcular valor baseado no cargo
        $usuario = User::find(Auth::id());
        $valorUnitario = $usuario->getPrecoBase($request->periodo);
        $valorTotal = $valorUnitario * $request->quantidade;

        // Verificar se há proxies disponíveis no estoque
        if (!$proxyService->hasAvailableVps($request->pais)) {
            return back()
                ->withErrors(['pais' => 'Não há proxies disponíveis para este país no momento.'], 'novaCompra')
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Criar transação de compra
            $transacao = Transaction::create([
                'user_id' => Auth::id(),
                'email' => $usuario->email,
                'transacao' => 'TXN-' . strtoupper(uniqid()),
                'valor' => $valorTotal,
                'status' => 0, // Pendente
                'metodo_pagamento' => $request->metodo_pagamento,
                'tipo' => 'compra_proxy',
                'gateway_type' => null,
                'metadata' => [
                    'pais' => $request->pais,
                    'quantidade' => $request->quantidade,
                    'periodo' => $request->periodo,
                    'motivo' => $request->motivo,
                    'valor_unitario' => $valorUnitario,
                ],
            ]);

            // Se for Cartão de Crédito, processar via Stripe
            if ($request->metodo_pagamento === 'credit_card') {
                $stripeService = app(\App\Services\StripeService::class);
                $isAjax = $request->ajax() || $request->wantsJson();

                // Buscar o cartão
                $cartao = Cartao::where('id', $request->card_id)
                    ->where('user_id', Auth::id())
                    ->first();

                if (!$cartao) {
                    DB::rollBack();
                    if ($isAjax) {
                        return response()->json([
                            'success' => false,
                            'error' => 'Cartão não encontrado',
                        ], 400);
                    }
                    return back()
                        ->withErrors(['card_id' => 'Cartão não encontrado'], 'novaCompra')
                        ->withInput();
                }

                // Atualizar transação com card_id
                $transacao->card_id = $cartao->id;
                $transacao->payment_method = 'credit_card';
                $transacao->save();

                try {
                    $valorEmCentavos = (int) ($valorTotal * 100);

                    // Processar cobrança via Stripe
                    $result = $stripeService->charge([
                        'amount' => $valorEmCentavos,
                        'payment_method_id' => $cartao->token_gateway1,
                        'customer' => $stripeService->formatCustomer($usuario),
                        'description' => sprintf('%d Proxy(s) %s - %d dias', $request->quantidade, $request->pais, $request->periodo),
                        'metadata' => [
                            'transaction_id' => $transacao->transacao,
                            'user_id' => Auth::id(),
                            'quantidade' => $request->quantidade,
                            'periodo' => $request->periodo,
                            'pais' => $request->pais,
                        ],
                    ]);

                    $transacao->gateway_type = 'stripe';
                    $transacao->save();

                    if (!$result['success']) {
                        DB::rollBack();
                        if ($isAjax) {
                            return response()->json([
                                'success' => false,
                                'error' => $result['error'],
                            ], 400);
                        }
                        return back()
                            ->withErrors(['pagamento' => $result['error']], 'novaCompra')
                            ->withInput();
                    }

                    $stripeData = $result['data'];

                    // Atualizar transação com ID do gateway
                    $transacao->update([
                        'gateway_transaction_id' => $stripeData['id'],
                    ]);

                    // Verificar se já foi aprovado
                    if ($stripeData['status'] === 'paid') {
                        $transacao->update(['status' => 1]);

                        // Alocar proxies imediatamente
                        $proxiesAlocados = $proxyService->allocateProxies(Auth::id(), [
                            'pais' => $request->pais,
                            'quantidade' => (int) $request->quantidade,
                            'periodo_dias' => (int) $request->periodo,
                            'motivo' => $request->motivo,
                        ]);

                        DB::commit();

                        if ($isAjax) {
                            return response()->json([
                                'success' => true,
                                'message' => sprintf('Pagamento aprovado! %d proxies alocados.', count($proxiesAlocados)),
                                'redirect' => route('dash.show', ['section' => 'proxies']),
                            ]);
                        }

                        return redirect()
                            ->route('dash.show', ['section' => 'proxies'])
                            ->with('proxies_success', sprintf(
                                'Pagamento aprovado! %d proxies foram alocados e já estão disponíveis.',
                                count($proxiesAlocados)
                            ));
                    }

                    // Se pendente ou requer ação, retornar com mensagem
                    DB::commit();

                    if ($isAjax) {
                        return response()->json([
                            'success' => true,
                            'message' => 'Pagamento processado! Aguardando confirmação.',
                            'redirect' => route('dash.show', ['section' => 'transacoes']),
                        ]);
                    }

                    return redirect()
                        ->route('dash.show', ['section' => 'transacoes'])
                        ->with('success', 'Pagamento processado! Aguardando confirmação.');

                } catch (\Exception $e) {
                    DB::rollBack();
                    if ($isAjax) {
                        return response()->json([
                            'success' => false,
                            'error' => 'Erro ao processar pagamento: ' . $e->getMessage(),
                        ], 500);
                    }
                    return back()
                        ->withErrors(['error' => 'Erro ao processar pagamento: ' . $e->getMessage()], 'novaCompra')
                        ->withInput();
                }
            }

            // Se for PIX, criar QR Code (Asaas como primário, AbacatePay como fallback)
            if ($request->metodo_pagamento === 'pix') {
                $isAjax = $request->ajax() || $request->wantsJson();

                $pixPayload = [
                    'amount' => (int) ($valorTotal * 100), // Converter para centavos
                    'expiresIn' => 1800, // 30 minutos em segundos
                    'description' => sprintf('%d Proxy(s) %s - %d dias', $request->quantidade, $request->pais, $request->periodo),
                    'customer' => [
                        'name' => $usuario->name,
                        'cellphone' => $usuario->phone ?? '',
                        'email' => $usuario->email,
                        'taxId' => $usuario->cpf ?? '', // Asaas exige CPF válido, AbacatePay aceita vazio
                    ],
                    'metadata' => [
                        'externalId' => $transacao->transacao,
                        'user_id' => Auth::id(),
                        'tipo' => 'compra_proxy',
                    ],
                ];

                $pixData = null;
                $usedGateway = null;
                $lastError = null;

                try {
                    // AbacatePay pode ter requisitos diferentes - garantir taxId válido
                    $abacatePayLoad = $pixPayload;
                    if (empty($abacatePayLoad['customer']['taxId'])) {
                        // AbacatePay pode exigir CPF, usar placeholder se não disponível
                        $abacatePayLoad['customer']['taxId'] = '00000000000';
                    }
                    if (empty($abacatePayLoad['customer']['cellphone'])) {
                        $abacatePayLoad['customer']['cellphone'] = '00000000000';
                    }

                    $abacatePayLoad['metadata'] = (object) array_map('strval', $pixPayload['metadata']);

                    $transacao->gateway_type = 'abacatepay';
                    $transacao->save();


                    $pixData = $abacatePay->createPix($abacatePayLoad);
                    $usedGateway = 'abacatepay';
                    \Log::info('PIX criado via AbacatePay (fallback)', ['pix_id' => $pixData['id']]);
                } catch (\Exception $e) {
                    DB::rollBack();
                    // $errorMsg = $lastError
                    //     ? "Asaas: {$lastError} | AbacatePay: {$e->getMessage()}"
                    //     : $e->getMessage();

                    // if ($isAjax) {
                    //     return response()->json([
                    //         'success' => false,
                    //         'error' => 'Erro ao gerar PIX: ' . $errorMsg,
                    //     ], 500);
                    // }
                    // return back()
                    //     ->withErrors(['error' => 'Erro ao gerar PIX: ' . $errorMsg], 'novaCompra')
                    //     ->withInput();
                }

                // Tentar Asaas primeiro
                if (!$pixData && $asaas->isConfigured()) {
                    try {
                        $pixData = $asaas->createPix($pixPayload);
                        $usedGateway = 'asaas';
                        $transacao->gateway_type = 'asaas';
                        $transacao->save();
                    } catch (\Exception $e) {
                        $lastError = $e->getMessage();
                        \Log::warning('Asaas falhou, tentando AbacatePay', ['error' => $e->getMessage()]);
                    }
                }


                // Atualizar transação com dados do gateway usado
                $metadata = $transacao->metadata;
                $metadata['pix_gateway'] = $usedGateway;
                $metadata[$usedGateway] = [
                    'pix_id' => $pixData['id'],
                    'dev_mode' => $pixData['devMode'] ?? false,
                    'expires_at' => $pixData['expiresAt'],
                ];
                $transacao->metadata = $metadata;
                $transacao->save();

                DB::commit();

                // Calcular timestamp de expiração
                $expiresAt = \Carbon\Carbon::parse($pixData['expiresAt']);

                $pixModalData = [
                    'transaction_id' => $transacao->id,
                    'transaction_code' => $transacao->transacao,
                    'pix_id' => $pixData['id'],
                    'valor' => $valorTotal,
                    'copia_e_cola' => $pixData['brCode'],
                    'qr_code_base64' => $pixData['brCodeBase64'],
                    'expira_em' => $expiresAt->format('d/m/Y H:i'),
                    'expira_timestamp' => $expiresAt->timestamp,
                    'dev_mode' => $pixData['devMode'] ?? false,
                    'gateway' => $usedGateway,
                ];

                // Se for AJAX, retornar JSON
                if ($isAjax) {
                    return response()->json([
                        'success' => true,
                        'pix_modal' => $pixModalData,
                        'redirect' => route('dash.show', ['section' => 'nova-compra']),
                    ]);
                }

                // Retornar para dashboard com modal PIX
                return redirect()
                    ->route('dash.show', ['section' => 'nova-compra'])
                    ->with('pix_modal', $pixModalData);
            }

            // Para outros métodos de pagamento, manter simulação por enquanto
            $transacao->status = 1; // Aprovado (simulado)
            $transacao->save();

            // Alocar proxies de forma randomizada entre diferentes VPS
            $proxiesAlocados = $proxyService->allocateProxies(Auth::id(), [
                'pais' => $request->pais,
                'quantidade' => (int) $request->quantidade,
                'periodo_dias' => (int) $request->periodo,
                'motivo' => $request->motivo,
            ]);

            DB::commit();

            return redirect()
                ->route('dash.show', ['section' => 'proxies'])
                ->with('proxies_success', sprintf(
                    'Compra realizada com sucesso! %d proxies foram alocados e já estão disponíveis.',
                    count($proxiesAlocados)
                ));

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withErrors(['error' => 'Erro ao processar compra: ' . $e->getMessage()], 'novaCompra')
                ->withInput();
        }
    }

    /**
     * Processar renovação de proxy via PIX
     */
    public function processarRenovacao(Request $request, AsaasService $asaas, AbacatePayService $abacatePay)
    {
        // Validar dados de entrada
        $validated = $request->validate([
            'proxy_id' => 'required|exists:stocks,id',
            'periodo' => 'required|integer|in:30,60,90,180,360',
        ]);

        $usuario = User::find(Auth::id());
        $renewalService = app(\App\Services\ProxyRenewalService::class);

        try {
            // Buscar proxy
            $proxy = Stock::with('vps')->findOrFail($validated['proxy_id']);

            // Verificar se proxy pertence ao usuário
            if (!$renewalService->canRenewProxy($proxy, $usuario)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Você não tem permissão para renovar este proxy.',
                ], 403);
            }

            // Calcular valor e nova data de expiração
            $valorTotal = $renewalService->calculateRenewalPrice($usuario, $validated['periodo']);
            $novaExpiracao = $renewalService->calculateNewExpiration($proxy, $validated['periodo']);

            DB::beginTransaction();

            // Criar transação de renovação
            $transacao = Transaction::create([
                'user_id' => Auth::id(),
                'email' => $usuario->email,
                'transacao' => 'REN-' . strtoupper(uniqid()),
                'valor' => $valorTotal,
                'status' => 0, // Pendente
                'metodo_pagamento' => 'pix',
                'tipo' => 'renovacao',
                'gateway_type' => null,
                'metadata' => [
                    'proxy_id' => $proxy->id,
                    'proxy_ip' => $proxy->ip,
                    'proxy_porta' => $proxy->porta,
                    'periodo_adicional' => $validated['periodo'],
                    'expiracao_anterior' => $proxy->expiracao,
                    'expiracao_nova' => $novaExpiracao->format('Y-m-d H:i:s'),
                    'estava_bloqueado' => $proxy->bloqueada,
                ],
            ]);

            // Criar PIX (Asaas como primário, AbacatePay como fallback)
            $pixPayload = [
                'amount' => (int) ($valorTotal * 100), // Converter para centavos
                'expiresIn' => 1800, // 30 minutos
                'description' => sprintf('Renovação Proxy %s:%s - %d dias', $proxy->ip, $proxy->porta, $validated['periodo']),
                'customer' => [
                    'name' => $usuario->name,
                    'cellphone' => $usuario->phone ?? '',
                    'email' => $usuario->email,
                    'taxId' => $usuario->cpf ?? '', // Asaas exige CPF válido, AbacatePay aceita vazio
                ],
                'metadata' => [
                    'externalId' => $transacao->transacao,
                    'user_id' => Auth::id(),
                    'tipo' => 'renovacao',
                    'proxy_id' => $proxy->id,
                ],
            ];

            $pixData = null;
            $usedGateway = null;
            $lastError = null;

            try {
                $abacatePayLoad = $pixPayload;
                if (empty($abacatePayLoad['customer']['taxId'])) {
                    $abacatePayLoad['customer']['taxId'] = '00000000000';
                }
                if (empty($abacatePayLoad['customer']['cellphone'])) {
                    $abacatePayLoad['customer']['cellphone'] = '00000000000';
                }

                $abacatePayLoad['metadata'] = (object) array_map('strval', $pixPayload['metadata']);

                $transacao->gateway_type = 'abacatepay';
                $transacao->save();

                $pixData = $abacatePay->createPix($abacatePayLoad);
                $usedGateway = 'abacatepay';
                \Log::info('PIX renovação criado via AbacatePay (fallback)', ['pix_id' => $pixData['id']]);
            } catch (\Exception $e) {
                $lastError = $e->getMessage();
                \Log::warning('AbacatePay falhou na renovação, tentando Asaas', ['error' => $e->getMessage()]);
            }

            if (!$pixData && $asaas->isConfigured()) {
                try {
                    $pixData = $asaas->createPix($pixPayload);
                    $usedGateway = 'asaas';
                    $transacao->gateway_type = 'asaas';
                    $transacao->save();
                } catch (\Exception $e) {
                    $lastError = $e->getMessage();
                    \Log::warning('Asaas falhou na renovação, tentando AbacatePay', ['error' => $e->getMessage()]);
                }
            }

            // Atualizar transação com dados do gateway usado
            $metadata = $transacao->metadata;
            $metadata['pix_gateway'] = $usedGateway;
            $metadata[$usedGateway] = [
                'pix_id' => $pixData['id'],
                'dev_mode' => $pixData['devMode'] ?? false,
                'expires_at' => $pixData['expiresAt'],
            ];
            $transacao->metadata = $metadata;
            $transacao->save();

            DB::commit();

            // Calcular timestamp de expiração
            $expiresAt = \Carbon\Carbon::parse($pixData['expiresAt']);

            // Retornar sucesso com redirecionamento para modal PIX
            return response()->json([
                'success' => true,
                'redirect' => route('dash.show', ['section' => 'proxies']),
                'pix_modal' => [
                    'transaction_id' => $transacao->id,
                    'transaction_code' => $transacao->transacao,
                    'pix_id' => $pixData['id'],
                    'valor' => $valorTotal,
                    'copia_e_cola' => $pixData['brCode'],
                    'qr_code_base64' => $pixData['brCodeBase64'],
                    'expira_em' => $expiresAt->format('d/m/Y H:i'),
                    'expira_timestamp' => $expiresAt->timestamp,
                    'dev_mode' => $pixData['devMode'] ?? false,
                    'gateway' => $usedGateway,
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Erro ao processar renovação', [
                'user_id' => Auth::id(),
                'proxy_id' => $validated['proxy_id'],
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Erro ao processar renovação: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function dashboard()
    {
        $usuario = User::where('id', Auth::id())->first();

        $expiracao = $usuario->expiracao;

        if ($usuario->plano == "Grátis") {
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
            'metodo_pagamento' => 'required|in:pix,credit_card,boleto',
            'card_id' => 'required_if:metodo_pagamento,credit_card|exists:cartaos,id',
            'installments' => 'nullable|integer|min:1|max:12',
        ]);

        $user = User::find(Auth::id());
        $valorEmCentavos = (int) ($request->valor * 100);

        // Se for cartão de crédito, processar pelo Aprovei
        if ($request->metodo_pagamento === 'credit_card') {
            $aproveiService = app(\App\Services\AproveiService::class);

            // Buscar o cartão
            $cartao = \App\Models\Cartao::where('id', $request->card_id)
                ->where('user_id', Auth::id())
                ->first();

            if (!$cartao) {
                return redirect()->route('saldo.show')
                    ->withErrors(['card_id' => 'Cartão não encontrado']);
            }

            // Criar transação no banco primeiro
            $externalRef = 'balance-' . time() . '-' . Auth::id();
            $transacao = Transaction::create([
                'user_id' => Auth::id(),
                'email' => $user->email,
                'transacao' => $externalRef,
                'valor' => $request->valor,
                'status' => 0, // Pendente
                'metodo_pagamento' => 'credit_card',
                'payment_method' => 'credit_card',
                'card_id' => $cartao->id,
                'tipo' => 'recarga',
            ]);

            // Montar payload para Aprovei
            $payload = $aproveiService->buildCreditCardPayload(
                amountInCents: $valorEmCentavos,
                cardToken: $cartao->token_gateway1,
                installments: $request->installments ?? 1,
                customer: $aproveiService->formatCustomer($user),
                items: [$aproveiService->formatBalanceItem($valorEmCentavos)],
                externalRef: $externalRef,
                postbackUrl: route('postback.transacao'),
                ip: $request->ip()
            );

            // Criar transação no Aprovei
            $result = $aproveiService->createCreditCardTransaction($payload);

            if ($result['success']) {
                $aproveiData = $result['data'];

                // Atualizar transação com ID do gateway
                $transacao->update([
                    'gateway_transaction_id' => $aproveiData['id'],
                ]);

                // Verificar se já foi aprovado
                if (in_array($aproveiData['status'], ['paid', 'approved'])) {
                    $transacao->update(['status' => 1]);
                    $user->saldo += $request->valor;
                    $user->save();

                    return redirect()->route('saldo.show')
                        ->with('success', 'Pagamento aprovado! Saldo adicionado com sucesso.');
                }

                return redirect()->route('saldo.show')
                    ->with('success', 'Pagamento processado! Aguardando confirmação.');
            } else {
                $transacao->delete();

                return redirect()->route('saldo.show')
                    ->withErrors(['pagamento' => $result['error']]);
            }
        }

        // PIX ou Boleto - manter lógica antiga
        $transacao = Transaction::create([
            'user_id' => Auth::id(),
            'valor' => $request->valor,
            'status' => 0, // Pendente
            'metodo_pagamento' => $request->metodo_pagamento,
            'payment_method' => $request->metodo_pagamento,
            'tipo' => 'recarga',
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

    /**
     * Gera código PIX Copia e Cola simulado
     */
    private function gerarPixCopiaECola(float $valor, string $transacaoId): string
    {
        // Formato PIX EMV simplificado (simulado)
        // Em produção, você usaria uma biblioteca para gerar o código real
        $chave = 'contato@alfaproxy.com'; // Chave PIX da empresa
        $cidade = 'SAO PAULO';
        $nome = 'ALFA PROXY';

        // Gerar código base64 simulado que parece um código PIX real
        $payload = sprintf(
            '%s|%s|%s|%s|%.2f',
            $chave,
            $nome,
            $cidade,
            $transacaoId,
            $valor
        );

        return '00020126' . strlen($payload) . $payload . 'BR.GOV.BCB.PIX' . date('YmdHis');
    }

    public function testarProxy(Request $request)
    {
        $request->validate([
            'ip' => 'required|string',
            'porta' => 'required|integer',
            'usuario' => 'required|string',
            'senha' => 'required|string',
        ]);

        try {
            $apiUrl = config('services.python_api.url', env('PYTHON_API_URL', 'http://127.0.0.1:8001'));

            $response = \Illuminate\Support\Facades\Http::timeout(15)->post("{$apiUrl}/testar", [
                'ip' => $request->ip,
                'porta' => (int) $request->porta,
                'usuario' => $request->usuario,
                'senha' => $request->senha,
                'timeout' => 5,
            ]);

            if ($response->successful()) {
                return response()->json($response->json());
            } else {
                return response()->json([
                    'status' => 'error',
                    'error' => $response->json()['detail'] ?? 'Erro ao testar proxy',
                ], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'error' => 'Erro ao conectar com servidor de testes: ' . $e->getMessage(),
            ], 500);
        }
    }

}

