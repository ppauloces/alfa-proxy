<div class="flex flex-col gap-2 mb-8">
    <p class="text-sm uppercase tracking-[0.35em] text-slate-500">Financeiro & previsões</p>
    <h1 class="text-3xl font-bold text-slate-900">Entradas, saídas e relatórios</h1>
    <p class="text-slate-500">Controle o fluxo de caixa, registre despesas manuais e visualize o potencial de vendas com base no estoque disponível.</p>
</div>

<div class="grid md:grid-cols-3 gap-4 mb-8">
    @foreach($financeCards as $card)
        <div class="finance-card admin-card">
            <p class="text-xs uppercase tracking-[0.3em] text-slate-400">{{ $card['label'] }}</p>
            <p class="text-2xl font-bold text-slate-900 mt-2">{{ $card['value'] }}</p>
            <p class="text-sm text-emerald-500 mt-1">{{ $card['trend'] }}</p>
            <div class="chart-bar mt-3">
                <span style="width: {{ $card['bar'] }}%"></span>
            </div>
        </div>
    @endforeach
</div>

<div class="grid lg:grid-cols-2 gap-6 mb-8">
    <div class="admin-card">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-xl font-semibold text-slate-900">Extrato de saídas</h2>
            <button type="button" class="btn-secondary text-xs px-3 py-2"><i class="fas fa-plus"></i> Despesa manual</button>
        </div>
        <div class="space-y-3 text-sm">
            @foreach($financeExtract['saida'] as $saida)
                <div class="timeline-item">
                    <div>
                        <p class="font-semibold text-slate-900">{{ $saida['descricao'] }}</p>
                        <p class="text-xs text-slate-500">{{ $saida['categoria'] }} • {{ $saida['data'] }}</p>
                    </div>
                    <span class="text-red-500 font-semibold">{{ $saida['valor'] }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <div class="admin-card">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-xl font-semibold text-slate-900">Extrato de entradas</h2>
            <button type="button" class="btn-secondary text-xs px-3 py-2"><i class="fas fa-file-invoice-dollar"></i> Gerar recibos</button>
        </div>
        <div class="space-y-3 text-sm">
            @foreach($financeExtract['entrada'] as $entrada)
                <div class="timeline-item">
                    <div>
                        <p class="font-semibold text-slate-900">{{ $entrada['descricao'] }}</p>
                        <p class="text-xs text-slate-500">{{ $entrada['categoria'] }} • {{ $entrada['data'] }}</p>
                    </div>
                    <span class="text-emerald-500 font-semibold">{{ $entrada['valor'] }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div class="admin-card">
    <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
        <div>
            <h2 class="text-xl font-semibold text-slate-900">Relatórios & previsões</h2>
            <p class="text-sm text-slate-500">Baseado nos proxies disponíveis + taxa média de recompra.</p>
        </div>
        <div class="flex gap-2">
            <button type="button" class="btn-secondary text-xs px-3 py-2"><i class="fas fa-chart-line"></i> Relatório mensal</button>
            <button type="button" class="btn-primary text-xs px-3 py-2"><i class="fas fa-calculator"></i> Simular previsão</button>
        </div>
    </div>
    <div class="grid md:grid-cols-3 gap-4">
        @foreach($forecast as $item)
            <div class="bg-slate-50 rounded-2xl p-4">
                <p class="text-xs uppercase tracking-[0.3em] text-slate-400">{{ $item['title'] }}</p>
                <p class="text-2xl font-bold text-slate-900 mt-2">{{ $item['value'] }}</p>
                <p class="text-sm text-slate-500 mt-1">{{ $item['detail'] }}</p>
            </div>
        @endforeach
    </div>
</div>
