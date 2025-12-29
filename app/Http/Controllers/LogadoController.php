<?php

namespace App\Http\Controllers;

use App\Models\Coupom;
use App\Models\Stock;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Cartao;
use App\Models\Despesa;
use App\Services\ProxyAllocationService;
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

        // Variáveis para seções admin
        $financeCards = [];
        $financeExtract = ['saida' => [], 'entrada' => []];
        $forecast = [];
        $soldProxyCards = [];
        $soldProxies = [];

        if ($usuario->isAdmin()) {
            $vpsList = \App\Models\Vps::with('proxies')->orderBy('created_at', 'desc')->get();

            $vpsFarm = $vpsList->map(function ($vps) {
                return (object) [
                    'id' => $vps->id,
                    'apelido' => $vps->apelido,
                    'ip' => $vps->ip,
                    'pais' => $vps->pais,
                    'hospedagem' => $vps->hospedagem,
                    'valor' => 'R$ ' . number_format($vps->valor, 2, ',', '.'),
                    'periodo' => $vps->periodo_dias . ' dias',
                    'contratada' => $vps->data_contratacao->format('d/m/Y'),
                    'status' => $vps->status,
                    'proxies' => $vps->proxies,
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
            ];


            // ===== EXTRATO DE SAÍDAS (Últimas 25 despesas) =====
            $saidas = Despesa::with('vps')
                ->orderBy('created_at', 'desc')
                ->limit(25)
                ->get()
                ->map(function ($despesa) {
                    return [
                        'descricao' => $despesa->descricao ?? 'VPS ' . ($despesa->vps->apelido ?? 'N/A'),
                        'categoria' => ucfirst($despesa->tipo),
                        'data' => $despesa->created_at->format('d/m/Y'),
                        'valor' => '- R$ ' . number_format((float) $despesa->valor, 2, ',', '.'),
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

                    $descricao = $tipoLabel . ' - ' . ($transacao->user->username ?? 'Usuário');

                    return [
                        'descricao' => $descricao,
                        'categoria' => $metodoLabel,
                        'data' => $transacao->created_at->format('d/m/Y'),
                        'valor' => '+ R$ ' . number_format((float) $transacao->valor, 2, ',', '.'),
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

            $proxiesDisponiveis = Stock::where('disponibilidade', true)->count();
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
                    ];
                })->toArray();
        }

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

    public function processarCompra(Request $request, ProxyAllocationService $proxyService, AbacatePayService $abacatePay)
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

        // Verificar se há proxies disponíveis no estoque
        if (!$proxyService->hasAvailableVps($request->pais)) {
            return back()
                ->withErrors(['pais' => 'Não há proxies disponíveis para este país no momento.'], 'novaCompra')
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $usuario = User::find(Auth::id());

            // Criar transação de compra
            $transacao = Transaction::create([
                'user_id' => Auth::id(),
                'email' => $usuario->email,
                'transacao' => 'TXN-' . strtoupper(uniqid()),
                'valor' => $valorTotal,
                'status' => 0, // Pendente
                'metodo_pagamento' => $request->metodo_pagamento,
                'tipo' => 'compra_proxy',
                'metadata' => [
                    'pais' => $request->pais,
                    'quantidade' => $request->quantidade,
                    'periodo' => $request->periodo,
                    'motivo' => $request->motivo,
                    'valor_unitario' => $valorUnitario,
                ],
            ]);

            // Se for Cartão de Crédito, processar via Aprovei
            if ($request->metodo_pagamento === 'credit_card') {
                $aproveiService = app(\App\Services\AproveiService::class);

                // Buscar o cartão
                $cartao = Cartao::where('id', $request->card_id)
                    ->where('user_id', Auth::id())
                    ->first();

                if (!$cartao) {
                    DB::rollBack();
                    return back()
                        ->withErrors(['card_id' => 'Cartão não encontrado'], 'novaCompra')
                        ->withInput();
                }

                // Atualizar transação com card_id
                $transacao->card_id = $cartao->id;
                $transacao->payment_method = 'credit_card';
                $transacao->save();

                try {
                    $valorEmCentavos = app()->environment('local')
                        ? 100
                        : (int) ($valorTotal * 100);
                    $quantidade = (int) $request->quantidade;
                    $quantidade = $quantidade > 0 ? $quantidade : 1;
                    $unitPriceEmCentavos = (int) floor($valorEmCentavos / $quantidade);

                    // Montar payload para Aprovei
                    $payload = $aproveiService->buildCreditCardPayload(
                        amountInCents: $valorEmCentavos,
                        cardToken: $cartao->token_gateway1,
                        installments: $request->installments ?? 1,
                        customer: $aproveiService->formatCustomer($usuario),
                        items: [$aproveiService->formatProxyItem($quantidade, $unitPriceEmCentavos, $request->periodo, $request->pais)],
                        externalRef: $transacao->transacao,
                        postbackUrl: route('postback.transacao'),
                        ip: $request->ip()
                    );

                    // Criar transação no Aprovei
                    $result = $aproveiService->createCreditCardTransaction($payload);

                    if (!$result['success']) {
                        DB::rollBack();
                        return back()
                            ->withErrors(['pagamento' => $result['error']], 'novaCompra')
                            ->withInput();
                    }

                    $aproveiData = $result['data'];

                    // Atualizar transação com ID do gateway
                    $transacao->update([
                        'gateway_transaction_id' => $aproveiData['id'],
                    ]);

                    // Verificar se já foi aprovado
                    if (in_array($aproveiData['status'], ['paid', 'approved'])) {
                        $transacao->update(['status' => 1]);

                        // Alocar proxies imediatamente
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
                                'Pagamento aprovado! %d proxies foram alocados e já estão disponíveis.',
                                count($proxiesAlocados)
                            ));
                    }

                    // Se pendente, retornar com mensagem de aguardando
                    DB::commit();

                    return redirect()
                        ->route('dash.show', ['section' => 'transacoes'])
                        ->with('success', 'Pagamento processado! Aguardando confirmação do gateway.');

                } catch (\Exception $e) {
                    DB::rollBack();
                    return back()
                        ->withErrors(['error' => 'Erro ao processar pagamento: ' . $e->getMessage()], 'novaCompra')
                        ->withInput();
                }
            }

            // Se for PIX, criar QR Code via AbacatePay
            if ($request->metodo_pagamento === 'pix') {
                try {
                    $pixData = $abacatePay->createPix([
                        'amount' => (int) ($valorTotal * 100), // Converter para centavos
                        'expiresIn' => 1800, // 30 minutos em segundos
                        'description' => sprintf('%d Proxy(s) %s - %d dias', $request->quantidade, $request->pais, $request->periodo),
                        'customer' => [
                            'name' => $usuario->name,
                            'cellphone' => $usuario->phone ?? '(00) 00000-0000',
                            'email' => $usuario->email,
                            'taxId' => $usuario->cpf ?? '293.235.470-18',
                        ],
                        'metadata' => [
                            'externalId' => $transacao->transacao,
                            'user_id' => Auth::id(),
                            'tipo' => 'compra_proxy',
                        ],
                    ]);

                    // Atualizar transação com dados do AbacatePay
                    $metadata = $transacao->metadata;
                    $metadata['abacatepay'] = [
                        'pix_id' => $pixData['id'],
                        'dev_mode' => $pixData['devMode'] ?? false,
                        'expires_at' => $pixData['expiresAt'],
                    ];
                    $transacao->metadata = $metadata;
                    $transacao->save();

                    DB::commit();

                    // Calcular timestamp de expiração
                    $expiresAt = \Carbon\Carbon::parse($pixData['expiresAt']);

                    // Retornar para dashboard com modal PIX
                    return redirect()
                        ->route('dash.show', ['section' => 'nova-compra'])
                        ->with('pix_modal', [
                            'transaction_id' => $transacao->id,
                            'transaction_code' => $transacao->transacao,
                            'pix_id' => $pixData['id'],
                            'valor' => $valorTotal,
                            'copia_e_cola' => $pixData['brCode'],
                            'qr_code_base64' => $pixData['brCodeBase64'],
                            'expira_em' => $expiresAt->format('d/m/Y H:i'),
                            'expira_timestamp' => $expiresAt->timestamp,
                            'dev_mode' => $pixData['devMode'] ?? false,
                        ]);

                } catch (\Exception $e) {
                    DB::rollBack();
                    return back()
                        ->withErrors(['error' => 'Erro ao gerar PIX: ' . $e->getMessage()], 'novaCompra')
                        ->withInput();
                }
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
