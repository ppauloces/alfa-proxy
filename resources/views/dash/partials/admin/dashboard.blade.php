<div class="flex flex-col gap-2 mb-8">
    <p class="text-sm uppercase tracking-[0.35em] text-slate-500">Painel Administrativo</p>
    <h1 class="text-3xl font-bold text-slate-900">Visão geral do AlfaProxy</h1>
    <p class="text-slate-500">Acompanhe a produção de proxies, saúde das VPS e previsões financeiras em tempo real.</p>
</div>

<div class="grid md:grid-cols-4 gap-6 mb-8">
    @foreach($adminOverview as $card)
        <div class="admin-card">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs uppercase tracking-[0.35em] text-slate-400">{{ $card['label'] }}</span>
                <i class="fas {{ $card['icon'] }} text-lg text-slate-400"></i>
            </div>
            <p class="text-3xl font-bold text-slate-900">{{ $card['value'] }}</p>
            <p class="text-sm text-emerald-500 mt-2">{{ $card['chip'] }}</p>
        </div>
    @endforeach
</div>

<div class="grid xl:grid-cols-3 gap-6 mb-8">
    <div class="admin-card xl:col-span-2">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-slate-900">Fila de produção de proxies</h2>
            <span class="admin-chip"><i class="fas fa-robot"></i> Farm rodando</span>
        </div>
        <div class="grid md:grid-cols-2 gap-4">
            @if($vpsFarm)
            @foreach($vpsFarm as $farm)
                <div class="bg-slate-50 rounded-2xl p-4">
                    <div class="flex items-center justify-between mb-2">
                        <p class="font-semibold text-slate-900">{{ $farm['apelido'] }}</p>
                        <span class="badge-status" data-status="{{ Str::slug($farm['status'], '-') }}">{{ $farm['status'] }}</span>
                    </div>
                    <p class="text-sm text-slate-500">IP {{ $farm['ip'] }} • {{ $farm['pais'] }}</p>
                    <div class="flex flex-wrap gap-4 text-xs text-slate-500 mt-3">
                        <span><i class="fas fa-database"></i> {{ count($farm['proxies']) }} proxies</span>
                        <span><i class="fas fa-wallet"></i> {{ $farm['valor'] }}</span>
                        <span><i class="fas fa-calendar-alt"></i> {{ $farm['periodo'] }}</span>
                    </div>
                </div>
            @endforeach
            @else
            <div class="bg-slate-50 rounded-2xl p-4">
                <p class="text-sm text-slate-500">Nenhuma VPS cadastrada</p>
            </div>
            @endif
        </div>
    </div>

    <div class="admin-card">
        <h2 class="text-xl font-semibold text-slate-900 mb-3">Alertas do NOC</h2>
        <div class="space-y-3 text-sm text-slate-600">
            <div class="flex items-start gap-3 p-3 rounded-2xl bg-amber-50">
                <i class="fas fa-plug text-amber-500 mt-1"></i>
                <div>
                    <p class="font-semibold text-slate-900">Portas bloqueadas</p>
                    <p>3 proxies na VPS US-NODE 03 aguardando liberação.</p>
                </div>
            </div>
            <div class="flex items-start gap-3 p-3 rounded-2xl bg-red-50">
                <i class="fas fa-triangle-exclamation text-red-500 mt-1"></i>
                <div>
                    <p class="font-semibold text-slate-900">Queda detectada</p>
                    <p>Proxy #071 - EU-SCALA 02 está offline há 18 minutos.</p>
                </div>
            </div>
            <div class="flex items-start gap-3 p-3 rounded-2xl bg-emerald-50">
                <i class="fas fa-check-circle text-emerald-500 mt-1"></i>
                <div>
                    <p class="font-semibold text-slate-900">Health-check concluído</p>
                    <p>58 proxies validados nas últimas 2 horas.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="grid lg:grid-cols-2 gap-6">
    <div class="admin-card">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-slate-900">Previsões & capacidade</h2>
            <button type="button" class="btn-secondary text-xs px-3 py-2"><i class="fas fa-cloud-download-alt"></i> Exportar</button>
        </div>
        <div class="space-y-4">
            @foreach($forecast as $item)
                <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl">
                    <div>
                        <p class="text-xs uppercase tracking-[0.3em] text-slate-400">{{ $item['title'] }}</p>
                        <p class="text-xl font-semibold text-slate-900 mt-1">{{ $item['value'] }}</p>
                    </div>
                    <p class="text-sm text-slate-500 max-w-[160px]">{{ $item['detail'] }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <div class="admin-card">
        <h2 class="text-xl font-semibold text-slate-900 mb-4">Últimas movimentações</h2>
        <div class="timeline">
            <div class="timeline-item">
                <div>
                    <p class="font-semibold text-slate-900">Venda concluída</p>
                    <p class="text-xs text-slate-500">João Henrique • PIX • 14:20</p>
                </div>
                <span class="text-emerald-500 font-semibold">+R$ 210</span>
            </div>
            <div class="timeline-item">
                <div>
                    <p class="font-semibold text-slate-900">Despesa VPS</p>
                    <p class="text-xs text-slate-500">Hetzner • Cartão • 13:40</p>
                </div>
                <span class="text-red-500 font-semibold">-US$ 28</span>
            </div>
            <div class="timeline-item">
                <div>
                    <p class="font-semibold text-slate-900">Saldo adicionado</p>
                    <p class="text-xs text-slate-500">ApostaPrime • Cartão • 12:08</p>
                </div>
                <span class="text-emerald-500 font-semibold">+R$ 1.500</span>
            </div>
        </div>
    </div>
</div>
