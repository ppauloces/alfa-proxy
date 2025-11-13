<div class="flex flex-col gap-2 mb-8">
    <p class="text-sm uppercase tracking-[0.35em] text-slate-500">Financeiro</p>
    <h1 class="text-3xl font-bold text-slate-900">Histórico de Transações</h1>
    <p class="text-slate-500">Acompanhe todas as suas transações e pagamentos realizados.</p>
</div>

<!-- Cards de Estatísticas -->
<div class="grid md:grid-cols-3 gap-6 mb-8">
    <div class="stat-card">
        <div class="flex items-center justify-between mb-2">
            <p class="text-sm uppercase tracking-wider opacity-80">Total Gasto</p>
            <i class="fas fa-chart-line text-2xl opacity-60"></i>
        </div>
        <p class="text-3xl font-bold">R$ {{ number_format($totalValor, 2, ',', '.') }}</p>
        <p class="text-sm opacity-80 mt-1">Em todas as transações aprovadas</p>
    </div>

    <div class="stat-card" style="background: linear-gradient(120deg, #10b981, #059669);">
        <div class="flex items-center justify-between mb-2">
            <p class="text-sm uppercase tracking-wider opacity-80">Aprovadas</p>
            <i class="fas fa-check-circle text-2xl opacity-60"></i>
        </div>
        <p class="text-3xl font-bold">{{ count($pagamentos_aprovados) }}</p>
        <p class="text-sm opacity-80 mt-1">Transações concluídas</p>
    </div>

    <div class="stat-card" style="background: linear-gradient(120deg, #f59e0b, #d97706);">
        <div class="flex items-center justify-between mb-2">
            <p class="text-sm uppercase tracking-wider opacity-80">Pendentes</p>
            <i class="fas fa-clock text-2xl opacity-60"></i>
        </div>
        <p class="text-3xl font-bold">{{ count($pagamentos_pendentes) }}</p>
        <p class="text-sm opacity-80 mt-1">Aguardando pagamento</p>
    </div>
</div>

<!-- Lista de Transações -->
<div class="transactions-card">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-slate-900">Todas as Transações</h2>
        <button onclick="window.location.reload()" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:border-slate-400 transition-colors">
            <i class="fas fa-sync-alt"></i> Atualizar
        </button>
    </div>

    <div class="filter-tabs">
        <button class="filter-tab active" data-filter="all">Todas</button>
        <button class="filter-tab" data-filter="aprovadas">Aprovadas</button>
        <button class="filter-tab" data-filter="pendentes">Pendentes</button>
        <button class="filter-tab" data-filter="falhas">Falhas</button>
    </div>

    @if(count($pagamentos) > 0)
        <div class="overflow-x-auto">
            <table class="transactions-table">
                <thead>
                    <tr>
                        <th>ID da Transação</th>
                        <th>Método de Pagamento</th>
                        <th>Valor</th>
                        <th>Data</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="transactionsBody">
                    @foreach ($pagamentos as $pagamento)
                        <tr data-status="{{ $pagamento->status }}">
                            <td>
                                <p class="font-mono text-sm font-semibold text-slate-900">#{{ $pagamento->id }}</p>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    @if($pagamento->metodo_pagamento == 'PIX')
                                        <i class="fas fa-qrcode text-[#4F8BFF]"></i>
                                    @elseif($pagamento->metodo_pagamento == 'Cartao' || $pagamento->metodo_pagamento == 'Cartão')
                                        <i class="fas fa-credit-card text-[#4F8BFF]"></i>
                                    @else
                                        <i class="fab fa-bitcoin text-[#4F8BFF]"></i>
                                    @endif
                                    <span class="font-semibold text-slate-700">{{ $pagamento->metodo_pagamento }}</span>
                                </div>
                            </td>
                            <td>
                                <p class="font-semibold text-lg text-slate-900">R$ {{ number_format($pagamento->valor, 2, ',', '.') }}</p>
                            </td>
                            <td>
                                <p class="text-sm font-semibold text-slate-700">{{ \Carbon\Carbon::parse($pagamento->created_at)->format('d/m/Y') }}</p>
                                <p class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($pagamento->created_at)->format('H:i') }}</p>
                            </td>
                            <td>
                                @if($pagamento->status == 1)
                                    <span class="badge-success">
                                        <i class="fas fa-check"></i> Aprovada
                                    </span>
                                @elseif($pagamento->status == 0)
                                    <span class="badge-pending">
                                        <i class="fas fa-clock"></i> Pendente
                                    </span>
                                @else
                                    <span class="badge-failed">
                                        <i class="fas fa-times"></i> Falha
                                    </span>
                                @endif
                            </td>
                            <td>
                                <button class="text-[#4F8BFF] hover:text-[#2055dd] font-semibold text-sm" onclick="viewTransaction('{{ $pagamento->id }}')">
                                    Ver Detalhes
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="border border-dashed border-slate-200 rounded-2xl p-10 text-center">
            <i class="fas fa-receipt text-5xl text-slate-300 mb-4"></i>
            <p class="text-lg font-semibold text-slate-700 mb-2">Nenhuma transação encontrada</p>
            <p class="text-sm text-slate-500 mb-4">Você ainda não realizou nenhuma transação.</p>
            <a href="{{ route('compra.nova') }}" class="inline-flex items-center gap-2 px-5 py-2 rounded-2xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition-colors">
                Fazer primeira compra
                <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>
    @endif
</div>
