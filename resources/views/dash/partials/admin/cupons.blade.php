<div class="flex flex-col gap-2 mb-8">
    <p class="text-sm uppercase tracking-[0.35em] text-slate-500">Campanhas & cupons</p>
    <h1 class="text-3xl font-bold text-slate-900">Incentivos para revendedores</h1>
    <p class="text-slate-500">Gerencie cupons ativos, metas de uso e validade para cada campanha.</p>
</div>

<div class="admin-card mb-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-semibold text-slate-900">Campanhas</h2>
        <div class="flex gap-2">
            <button type="button" class="btn-secondary text-xs px-3 py-2"><i class="fas fa-pen"></i> Editar regras</button>
            <button type="button" class="btn-primary text-xs px-3 py-2"><i class="fas fa-plus"></i> Novo cupom</button>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="admin-table text-sm min-w-full">
            <thead>
                <tr>
                    <th>Campanha</th>
                    <th>Desconto</th>
                    <th>Uso</th>
                    <th>Validade</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($couponCampaigns as $campaign)
                    <tr>
                        <td class="font-semibold text-slate-900">{{ $campaign['nome'] }}</td>
                        <td>{{ $campaign['desconto'] }}</td>
                        <td>{{ $campaign['uso'] }}</td>
                        <td>{{ $campaign['validade'] }}</td>
                        <td><span class="badge-status" data-status="{{ \Illuminate\Support\Str::slug($campaign['status'], '-') }}">{{ $campaign['status'] }}</span></td>
                        <td>
                            <div class="flex gap-2">
                                <button type="button" class="btn-secondary text-xs px-3 py-2"><i class="fas fa-toggle-on"></i> Alternar</button>
                                <button type="button" class="btn-secondary text-xs px-3 py-2"><i class="fas fa-chart-pie"></i> Relatório</button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="grid md:grid-cols-3 gap-4">
    <div class="admin-card">
        <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Meta de leads</p>
        <p class="text-2xl font-bold text-slate-900 mt-2">+320 novos</p>
        <p class="text-sm text-slate-500 mt-1">Clientes aguardando ativação de cupons.</p>
    </div>
    <div class="admin-card">
        <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Conversão média</p>
        <p class="text-2xl font-bold text-slate-900 mt-2">38%</p>
        <p class="text-sm text-slate-500 mt-1">Da lead com cupom para assinatura.</p>
    </div>
    <div class="admin-card">
        <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Receita incentivada</p>
        <p class="text-2xl font-bold text-slate-900 mt-2">R$ 52.800</p>
        <p class="text-sm text-slate-500 mt-1">Gerada com cupons nos últimos 30 dias.</p>
    </div>
</div>
