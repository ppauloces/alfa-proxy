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
                        <td>{{ intval($proxy['periodo']) }} dias</td>
                        <td>
                            <div class="flex flex-wrap gap-2">
                                <button type="button"
                                    class="btn-secondary text-xs px-3 py-2"
                                    data-action="test-proxy"
                                    data-ip="{{ $proxy['ip'] }}"
                                    data-porta="{{ $proxy['porta'] }}"
                                    data-usuario="{{ $proxy['usuario'] }}"
                                    data-senha="{{ $proxy['senha'] }}">
                                    <i class="fas fa-vial"></i> Testar
                                </button>
                                <button type="button"
                                    class="btn-secondary text-xs px-3 py-2"
                                    data-toggle-port
                                    data-stock-id="{{ $proxy['stock_id'] }}"
                                    data-target="#{{ $statusId }}"
                                    data-state="{{ $blocked ? 'blocked' : 'open' }}">
                                    <i class="fas fa-ban"></i> <span data-btn-text>{{ $blocked ? 'Desbloquear' : 'Bloquear' }}</span>
                                </button>
                            </div>

                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Container de Notificações Toast -->
<div id="toastContainer" class="fixed top-20 right-4 space-y-3" style="max-width: 400px; z-index: 9999;">
    <!-- Toasts serão injetados aqui -->
</div>

<script>
    // Prevenir execução duplicada do script
    if (window.transacoesScriptLoaded) {
        console.log('Script de transações já carregado, pulando inicialização');
    } else {
        window.transacoesScriptLoaded = true;

        // ============================================
        // SISTEMA DE TOAST DE NOTIFICAÇÕES
        // ============================================
        if (typeof window.showToast === 'undefined') {
            window.showToast = function(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `transform transition-all duration-300 translate-x-full`;

        const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
        const icon = type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle';

        toast.innerHTML = `
            <div class="${bgColor} text-white px-6 py-4 rounded-lg shadow-xl flex items-start gap-3 min-w-[320px] max-w-[400px]">
                <i class="fas ${icon} text-xl flex-shrink-0 mt-0.5"></i>
                <div class="font-medium flex-1 text-sm leading-relaxed">${message}</div>
                <button onclick="this.closest('.transform').remove()" class="flex-shrink-0 hover:bg-white/20 rounded p-1 transition-colors">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
        `;

        const toastContainer = document.getElementById('toastContainer');
        if (!toastContainer) return;

        // Garantir que o container fique acima da modal
        if (toastContainer.parentNode !== document.body) {
            document.body.appendChild(toastContainer);
        }

        toastContainer.appendChild(toast);

        // Animar entrada
        setTimeout(() => {
            toast.className = 'transform transition-all duration-300 translate-x-0';
        }, 10);

        // Remover após 8 segundos
        setTimeout(() => {
            toast.className = 'transform transition-all duration-300 translate-x-full';
            setTimeout(() => toast.remove(), 300);
        }, 8000);
            };
        }

        // ============================================
        // TESTAR PROXY
        // ============================================
        if (typeof window.getIpGeolocation === 'undefined') {
            window.getIpGeolocation = async function(ip) {
        try {
            const response = await fetch(`https://ipapi.co/${ip}/json/`);
            if (response.ok) {
                const data = await response.json();
                return {
                    city: data.city || 'N/A',
                    region: data.region || 'N/A',
                    country: data.country_name || 'N/A',
                    flag: data.country_code ? `https://flagcdn.com/16x12/${data.country_code.toLowerCase()}.png` : null
                };
            }
        } catch (error) {
            console.error('Erro ao buscar geolocalização:', error);
            }
            return null;
            };
        }

        document.addEventListener('click', async function handleTogglePort(e) {
            const toggleButton = e.target.closest('[data-toggle-port]');
            if (!toggleButton) return;
        });
    }
</script>
