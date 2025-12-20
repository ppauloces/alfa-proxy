<div class="flex flex-col gap-6">
    {{-- Header da Seção --}}
    <div class="space-y-1">
        <p class="text-[10px] font-bold text-[#448ccb] uppercase tracking-[0.3em]">Financeiro</p>
        <h1 class="text-4xl font-black text-slate-900 tracking-tight">Histórico de <span class="text-[#23366f]">Transações</span></h1>
        <p class="text-slate-500 font-medium max-w-xl">Acompanhe todas as suas transações e pagamentos realizados.</p>
    </div>

    <!-- Cards de Estatísticas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm group hover:border-[#23366f] transition-all">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-[#23366f] flex items-center justify-center text-xl">
                    <i class="fas fa-chart-line"></i>
                </div>
                <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest text-right">Acumulado</span>
            </div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Gasto</p>
            <p class="text-3xl font-black text-slate-900">R$ {{ number_format($totalValor, 2, ',', '.') }}</p>
        </div>

        <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm group hover:border-green-500 transition-all">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-green-50 text-green-600 flex items-center justify-center text-xl">
                    <i class="fas fa-check-circle"></i>
                </div>
                <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest text-right">Sucesso</span>
            </div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Aprovadas</p>
            <p class="text-3xl font-black text-slate-900">{{ count($pagamentos_aprovados) }}</p>
        </div>

        <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm group hover:border-amber-500 transition-all">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                    <i class="fas fa-clock"></i>
                </div>
                <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest text-right">Em espera</span>
            </div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Pendentes</p>
            <p class="text-3xl font-black text-slate-900">{{ count($pagamentos_pendentes) }}</p>
        </div>
    </div>

    <!-- Lista de Transações -->
    <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-8 border-b border-slate-50 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-2 bg-slate-50 p-1.5 rounded-2xl w-fit">
                <button class="filter-tab px-6 py-2 rounded-xl font-bold text-xs transition-all active bg-[#23366f] text-white shadow-lg shadow-blue-900/20" data-filter="all">Todas</button>
                <button class="filter-tab px-6 py-2 rounded-xl font-bold text-xs transition-all text-slate-500 hover:bg-white" data-filter="aprovadas">Aprovadas</button>
                <button class="filter-tab px-6 py-2 rounded-xl font-bold text-xs transition-all text-slate-500 hover:bg-white" data-filter="pendentes">Pendentes</button>
                <button class="filter-tab px-6 py-2 rounded-xl font-bold text-xs transition-all text-slate-500 hover:bg-white" data-filter="falhas">Falhas</button>
            </div>
            
            <button onclick="window.location.reload()" class="flex items-center gap-2 px-5 py-2.5 rounded-xl border border-slate-100 text-xs font-bold text-slate-400 hover:text-[#23366f] hover:bg-slate-50 transition-all">
                <i class="fas fa-sync-alt"></i> Atualizar Dados
            </button>
        </div>

        @if(count($pagamentos) > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-slate-50/30">
                            <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-left">Transação</th>
                            <th class="px-6 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-left">Método</th>
                            <th class="px-6 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Valor</th>
                            <th class="px-6 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Data</th>
                            <th class="px-6 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Status</th>
                            <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody id="transactionsBody" class="divide-y divide-slate-50">
                        @foreach ($pagamentos as $pagamento)
                            <tr class="hover:bg-slate-50/50 transition-all" data-status="{{ $pagamento->status }}">
                                <td class="px-8 py-6">
                                    <span class="font-mono text-xs font-bold text-slate-400">#{{ $pagamento->id }}</span>
                                </td>
                                <td class="px-6 py-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center text-[#23366f] text-sm">
                                            @if($pagamento->metodo_pagamento == 'PIX')
                                                <i class="fas fa-qrcode"></i>
                                            @elseif($pagamento->metodo_pagamento == 'Cartao' || $pagamento->metodo_pagamento == 'Cartão')
                                                <i class="fas fa-credit-card"></i>
                                            @else
                                                <i class="fab fa-bitcoin"></i>
                                            @endif
                                        </div>
                                        <span class="font-bold text-slate-700 text-sm">{{ $pagamento->metodo_pagamento }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-6 text-center">
                                    <span class="font-black text-slate-900">R$ {{ number_format($pagamento->valor, 2, ',', '.') }}</span>
                                </td>
                                <td class="px-6 py-6 text-center">
                                    <p class="text-sm font-bold text-slate-700">{{ \Carbon\Carbon::parse($pagamento->created_at)->format('d/m/Y') }}</p>
                                    <p class="text-[10px] font-medium text-slate-400 uppercase">{{ \Carbon\Carbon::parse($pagamento->created_at)->format('H:i') }}</p>
                                </td>
                                <td class="px-6 py-6 text-center">
                                    @if($pagamento->status == 1)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-green-50 text-green-600 text-[10px] font-black uppercase tracking-wider border border-green-100">
                                            <i class="fas fa-check-circle text-[8px]"></i> Aprovada
                                        </span>
                                    @elseif($pagamento->status == 0)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-50 text-amber-600 text-[10px] font-black uppercase tracking-wider border border-amber-100">
                                            <i class="fas fa-clock text-[8px]"></i> Pendente
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-red-50 text-red-600 text-[10px] font-black uppercase tracking-wider border border-red-100">
                                            <i class="fas fa-times-circle text-[8px]"></i> Falha
                                        </span>
                                    @endif
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <button class="px-4 py-2 rounded-xl text-[11px] font-black text-[#23366f] hover:bg-[#23366f] hover:text-white transition-all uppercase tracking-widest" 
                                        onclick="viewTransaction('{{ $pagamento->id }}')">
                                        Detalhes
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="py-24 text-center">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-receipt text-3xl text-slate-200"></i>
                </div>
                <h3 class="text-xl font-black text-slate-900 mb-2">Nenhuma transação encontrada</h3>
                <p class="text-slate-400 text-sm font-medium mb-8">Você ainda não realizou transações na plataforma.</p>
                <button type="button" data-section-link="nova-compra"
                    class="inline-flex items-center gap-3 px-8 py-4 rounded-2xl bg-[#23366f] text-white font-bold hover:scale-105 transition-all">
                    Fazer primeira compra
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        @endif
    </div>
</div>
