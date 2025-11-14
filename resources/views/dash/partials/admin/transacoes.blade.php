<div class="flex flex-col gap-2 mb-8">
    <p class="text-sm uppercase tracking-[0.35em] text-slate-500">Painel de proxies vendidas</p>
    <h1 class="text-3xl font-bold text-slate-900">Vendas recentes & ações rápidas</h1>
    <p class="text-slate-500">Da última venda para a primeira, com acesso rápido para testar, bloquear portas ou repor proxies.</p>
</div>

<div class="grid md:grid-cols-4 gap-4 mb-8">
    @foreach($soldProxyCards as $card)
        <div class="admin-card">
            <p class="text-sm uppercase tracking-[0.35em] text-slate-400">{{ $card['label'] }}</p>
            <p class="text-2xl font-bold text-slate-900 mt-2">{{ $card['value'] }}</p>
            <p class="text-sm text-emerald-500 mt-1">{{ $card['chip'] }}</p>
        </div>
    @endforeach
</div>

@php
    $availableVps = collect($vpsFarm ?? [])->pluck('apelido')->filter()->values();
@endphp

<div class="admin-card">
    <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
        <h2 class="text-xl font-semibold text-slate-900">Lista de vendas</h2>
        <div class="flex gap-2">
            <button type="button" class="btn-secondary text-xs px-3 py-2"><i class="fas fa-sync-alt"></i> Atualizar</button>
            <button type="button" class="btn-primary text-xs px-3 py-2"><i class="fas fa-download"></i> Exportar CSV</button>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="admin-table text-sm min-w-full">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Endereço</th>
                    <th>Comprador</th>
                    <th>Porta</th>
                    <th>Status</th>
                    <th>Período restante</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($soldProxies as $proxy)
                    @php
                        $statusId = 'sold-status-'.$loop->index;
                        $blocked = $proxy['status'] === 'bloqueada';
                    @endphp
                    <tr>
                        <td>{{ $proxy['data'] }}</td>
                        <td class="font-mono text-xs">{{ $proxy['endereco'] }}</td>
                        <td>
                            <button type="button"
                                class="text-slate-900 font-semibold hover:underline"
                                data-open-buyer
                                data-buyer-name="{{ $proxy['comprador'] }}"
                                data-buyer-email="{{ $proxy['email'] }}"
                                data-buyer-spent="{{ $proxy['gasto_cliente'] }}"
                                data-buyer-orders="{{ $proxy['pedidos'] }}">
                                {{ $proxy['comprador'] }}
                            </button>
                        </td>
                        <td>{{ $proxy['porta'] }}</td>
                        <td>
                            <span id="{{ $statusId }}" class="badge-status" data-status="{{ $proxy['status'] }}">{{ ucfirst($proxy['status']) }}</span>
                        </td>
                        <td>{{ $proxy['periodo'] }}</td>
                        <td>
                            <div class="flex flex-wrap gap-2">
                                <button type="button" class="btn-secondary text-xs px-3 py-2" data-action="test-proxy">
                                    <i class="fas fa-vial"></i> Testar
                                </button>
                                <button type="button"
                                    class="btn-secondary text-xs px-3 py-2"
                                    data-toggle-port
                                    data-target="#{{ $statusId }}"
                                    data-state="{{ $blocked ? 'blocked' : 'open' }}">
                                    <i class="fas fa-ban"></i> {{ $blocked ? 'Desbloquear' : 'Bloquear' }}
                                </button>
                                <button type="button"
                                    class="btn-secondary text-xs px-3 py-2"
                                    data-replace-toggle="#replaceRow{{ $loop->index }}">
                                    <i class="fas fa-rotate"></i> Repor proxy
                                </button>
                            </div>
                            <div id="replaceRow{{ $loop->index }}" class="replace-panel hidden mt-2">
                                <div class="grid sm:grid-cols-2 gap-2 mb-2">
                                    <select class="form-select text-xs">
                                        <option>Selecione a VPS</option>
                                        @foreach($availableVps as $vps)
                                            <option>{{ $vps }}</option>
                                        @endforeach
                                    </select>
                                    <select class="form-select text-xs">
                                        <option>Proxy disponível</option>
                                        <option>#001 - BR-ALFA 01</option>
                                        <option>#040 - US-NODE 03</option>
                                        <option>#072 - EU-SCALA 02</option>
                                    </select>
                                </div>
                                <button type="button" class="btn-primary text-xs px-3 py-2 w-full">
                                    <i class="fas fa-paper-plane"></i> Confirmar reposição (valor 0)
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
