<div class="flex flex-col gap-2 mb-8">
    <p class="text-sm uppercase tracking-[0.35em] text-slate-500">Clientes & colaboradores</p>
    <h1 class="text-3xl font-bold text-slate-900">Quem compra e quem opera</h1>
    <p class="text-slate-500">Leads ativos, gasto acumulado e o time que mantém tudo em produção.</p>
</div>

<div class="">
    <div class="admin-card lg:col-span-2">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-slate-900">Clientes & leads</h2>
            <button type="button" class="btn-secondary text-xs px-3 py-2"><i class="fas fa-plus"></i> Novo lead</button>
        </div>
        <div class="overflow-x-auto">
            <table class="admin-table text-sm min-w-full">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>E-mail</th>
                        <th>Saldo</th>
                        <th>Gasto Total</th>
                        <th>Proxies</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clientLeads as $user)
                        <tr>
                            <td class="font-semibold text-slate-900">{{ $user['name'] }}</td>
                            <td class="text-xs text-slate-500">{{ $user['email'] }}</td>
                           
                            <td class="font-semibold text-slate-900">{{ $user['saldo'] ?? 'R$ 0,00' }}</td>
                            <td class="font-semibold text-slate-900">{{ $user['gasto'] }}</td>
                            <td class="text-center">
                                <span class="text-xs text-slate-500">{{ $user['proxies_count'] ?? 0 }}</span>
                            </td>
                            <td>
                                <span class="badge-status" data-status="{{ \Illuminate\Support\Str::slug($user['status'], '-') }}">
                                    {{ $user['status'] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-slate-500 py-8">Nenhum cliente encontrado</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
