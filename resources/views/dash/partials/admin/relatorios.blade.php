<div class="flex flex-col gap-2 mb-8">
    <p class="text-sm uppercase tracking-[0.35em] text-slate-500">Financeiro & previsões</p>
    <h1 class="text-3xl font-bold text-slate-900">Entradas, saídas e relatórios</h1>
    <p class="text-slate-500">Controle o fluxo de caixa, registre despesas manuais e visualize o potencial de vendas com
        base no estoque disponível.</p>
</div>

{{-- Cards Financeiros --}}
<div class="grid md:grid-cols-5 gap-4 mb-8">
    @foreach($financeCards as $card)
        <div
            class="finance-card admin-card {{ $card['label'] === 'Vendas Revendedores' ? 'bg-gradient-to-br from-amber-50 to-orange-50 border-amber-200' : '' }}">
            <p
                class="text-xs uppercase tracking-[0.3em] {{ $card['label'] === 'Vendas Revendedores' ? 'text-amber-600 font-bold' : 'text-slate-400' }}">
                {{ $card['label'] }}</p>
            <p
                class="text-2xl font-bold {{ $card['label'] === 'Vendas Revendedores' ? 'text-amber-900' : 'text-slate-900' }} mt-2">
                {{ $card['value'] }}</p>
            <p
                class="text-sm {{ $card['label'] === 'Vendas Revendedores' ? 'text-amber-600' : (str_contains($card['trend'], 'Negativo') ? 'text-red-500' : 'text-emerald-500') }} mt-1 {{ $card['label'] === 'Vendas Revendedores' ? 'flex items-center gap-1' : '' }}">
                @if($card['label'] === 'Vendas Revendedores')
                    <i class="fas fa-crown"></i>
                @endif
                {{ $card['trend'] }}
            </p>
            <div class="chart-bar mt-3 {{ $card['label'] === 'Vendas Revendedores' ? 'bg-amber-100' : '' }}">
                <span class="{{ $card['label'] === 'Vendas Revendedores' ? 'bg-amber-500' : '' }}"
                    style="width: {{ min(100, max(0, $card['bar'])) }}%"></span>
            </div>
        </div>
    @endforeach

    {{-- Card de Uso Interno --}}
    <div class="finance-card admin-card bg-gradient-to-br from-indigo-50 to-purple-50 border-indigo-200">
        <p class="text-xs uppercase tracking-[0.3em] text-indigo-600 font-bold">Uso Interno</p>
        <p class="text-2xl font-bold text-indigo-900 mt-2">{{ $usoInternoStats['total'] }}</p>
        <p class="text-sm text-indigo-600 mt-1 flex items-center gap-1">
            <i class="fas fa-briefcase"></i>
            {{ $usoInternoStats['total'] === 1 ? 'Proxy em uso' : 'Proxies em uso' }}
        </p>
        <div class="chart-bar mt-3 bg-indigo-100">
            <span class="bg-indigo-500" style="width: 100%"></span>
        </div>
    </div>

    {{-- Card de Substituidas --}}
    <div class="finance-card admin-card bg-gradient-to-br from-orange-50 to-red-50 border-orange-200">
        <p class="text-xs uppercase tracking-[0.3em] text-orange-600 font-bold">Substituídas</p>
        <p class="text-2xl font-bold text-orange-900 mt-2">{{ $substituidasStats['total'] ?? 0 }}</p>
        <p class="text-sm text-orange-600 mt-1 flex items-center gap-1">
            <i class="fas fa-exchange-alt"></i>
            {{ ($substituidasStats['total'] ?? 0) === 1 ? 'Proxy doado' : 'Proxies doados' }}
        </p>
        <div class="chart-bar mt-3 bg-orange-100">
            <span class="bg-orange-500" style="width: 100%"></span>
        </div>
    </div>
</div>

{{-- Gráficos Analíticos --}}
<div class="grid md:grid-cols-3 gap-6 mb-8">
    {{-- Gráfico 1: Gateways --}}
    <div class="admin-card">
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-slate-900">Gateways de Pagamento</h3>
            <p class="text-xs text-slate-500">Distribuição por gateway</p>
        </div>
        <div class="relative" style="height: 260px;">
            <canvas id="chartGateways"></canvas>
        </div>
        @if(count($chartGateways ?? []) > 0)
            <div class="mt-4 pt-4 border-t border-slate-100 space-y-2">
                @foreach($chartGateways as $gw)
                    <div class="flex items-center justify-between text-sm">
                        <span class="font-medium text-slate-700">{{ $gw['label'] }}</span>
                        <span class="text-slate-500">{{ $gw['count'] }} vendas &middot; R$
                            {{ number_format($gw['value'], 2, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Gráfico 2: Formas de Pagamento --}}
    <div class="admin-card">
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-slate-900">Formas de Pagamento</h3>
            <p class="text-xs text-slate-500">PIX, Cartão, Saldo e outros</p>
        </div>
        <div class="relative" style="height: 260px;">
            <canvas id="chartPaymentMethods"></canvas>
        </div>
        @if(count($chartPaymentMethods ?? []) > 0)
            <div class="mt-4 pt-4 border-t border-slate-100 space-y-2">
                @foreach($chartPaymentMethods as $pm)
                    <div class="flex items-center justify-between text-sm">
                        <span class="font-medium text-slate-700">{{ $pm['label'] }}</span>
                        <span class="text-slate-500">{{ $pm['count'] }} vendas &middot; R$
                            {{ number_format($pm['value'], 2, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Gráfico 3: Motivos de Compra --}}
    <div class="admin-card">
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-slate-900">Motivos de Compra</h3>
            <p class="text-xs text-slate-500">Por que os clientes compram proxies</p>
        </div>
        <div class="relative" style="height: 260px;">
            <canvas id="chartPurchaseReasons"></canvas>
        </div>
        @if(count($chartPurchaseReasons ?? []) > 0)
            <div class="mt-4 pt-4 border-t border-slate-100 space-y-2">
                @foreach($chartPurchaseReasons as $pr)
                    <div class="flex items-center justify-between text-sm">
                        <span class="font-medium text-slate-700">{{ $pr['label'] }}</span>
                        <span class="text-slate-500">{{ $pr['count'] }} {{ $pr['count'] === 1 ? 'proxy' : 'proxies' }}</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>




{{-- Seção de Uso Interno --}}
<div class="admin-card mb-8">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-xl font-semibold text-slate-900">Proxies em Uso Interno</h2>
            <p class="text-sm text-slate-500 mt-1">Proxies reservadas para uso da empresa</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="px-4 py-2 bg-indigo-50 text-indigo-700 rounded-xl font-bold text-lg">
                {{ $usoInternoStats['total'] }} {{ $usoInternoStats['total'] === 1 ? 'proxy' : 'proxies' }}
            </span>
            <a href="{{ route('admin.proxies', ['section' => 'admin-proxies']) }}"
                class="btn-secondary text-xs px-3 py-2">
                <i class="fas fa-cog"></i> Gerenciar
            </a>
        </div>
    </div>

    @if($usoInternoStats['total'] > 0)
        <div class="overflow-x-auto">
            <table class="admin-table text-sm min-w-full">
                <thead>
                    <tr>
                        <th class="text-left">Endereço</th>
                        <th class="text-left">Finalidade</th>
                        <th class="text-left">VPS</th>
                        <th class="text-left">Data/Hora</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($usoInternoStats['proxies'] as $proxy)
                        <tr>
                            <td class="font-mono text-xs">{{ $proxy['endereco'] }}</td>
                            <td>
                                <span
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-indigo-50 text-indigo-700 rounded-lg text-xs font-semibold">
                                    <i class="fas fa-briefcase"></i>
                                    {{ $proxy['finalidade'] }}
                                </span>
                            </td>
                            <td class="font-medium text-slate-700">{{ $proxy['vps'] }}</td>
                            <td class="text-slate-500 text-xs">{{ $proxy['data'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($usoInternoStats['total'] > 5)
            <div class="mt-4 pt-3 border-t border-slate-100 text-center">
                <a href="{{ route('admin.proxies', ['section' => 'admin-proxies']) }}"
                    class="text-xs text-[#448ccb] font-bold uppercase tracking-wider hover:underline">
                    <i class="fas fa-arrow-right mr-1"></i> Ver todas as {{ $usoInternoStats['total'] }} proxies de uso interno
                </a>
            </div>
        @endif
    @else
        <div class="text-center py-12 text-slate-400">
            <div class="w-16 h-16 bg-indigo-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-briefcase text-2xl text-indigo-300"></i>
            </div>
            <p class="font-medium text-slate-700 mb-1">Nenhuma proxy em uso interno</p>
            <p class="text-sm">Proxies marcadas como uso interno aparecerão aqui</p>
        </div>
    @endif
</div>

{{-- Seção de Substituições --}}
<div class="admin-card mb-8">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-xl font-semibold text-slate-900">Histórico de Substituições</h2>
            <p class="text-sm text-slate-500 mt-1">Proxies doados passivamente em substituições aos clientes</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="px-4 py-2 bg-orange-50 text-orange-700 rounded-xl font-bold text-lg">
                {{ $substituidasStats['total'] ?? 0 }}
                {{ ($substituidasStats['total'] ?? 0) === 1 ? 'proxy' : 'proxies' }}
            </span>
            <a href="{{ route('admin.proxies', ['section' => 'admin-proxies']) }}"
                class="btn-secondary text-xs px-3 py-2">
                <i class="fas fa-cog"></i> Gerenciar
            </a>
        </div>
    </div>

    @if(($substituidasStats['total'] ?? 0) > 0)
        <div class="overflow-x-auto">
            <table class="admin-table text-sm min-w-full">
                <thead>
                    <tr>
                        <th class="text-left">Endereço (Proxy Antiga)</th>
                        <th class="text-left">Motivo</th>
                        <th class="text-left">VPS</th>
                        <th class="text-left">Data/Hora</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($substituidasStats['proxies'] as $proxy)
                        <tr>
                            <td class="font-mono text-xs">{{ $proxy['endereco'] }}</td>
                            <td>
                                <span
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-orange-50 text-orange-700 rounded-lg text-xs font-semibold">
                                    <i class="fas fa-exchange-alt"></i> Substituição Admin
                                </span>
                            </td>
                            <td class="font-medium text-slate-700">{{ $proxy['vps'] }}</td>
                            <td class="text-slate-500 text-xs">{{ $proxy['data'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($substituidasStats['total'] > 5)
            <div class="mt-4 pt-3 border-t border-slate-100 text-center">
                <a href="{{ route('admin.proxies', ['section' => 'admin-proxies']) }}"
                    class="text-xs text-[#448ccb] font-bold uppercase tracking-wider hover:underline">
                    <i class="fas fa-arrow-right mr-1"></i> Ver todas
                </a>
            </div>
        @endif
    @else
        <div class="text-center py-12 text-slate-400">
            <div class="w-16 h-16 bg-orange-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-exchange-alt text-2xl text-orange-300"></i>
            </div>
            <p class="font-medium text-slate-700 mb-1">Nenhuma substituição realizada</p>
            <p class="text-sm">Proxies que forem reposição a uma indisponibilidade aparecerão aqui</p>
        </div>
    @endif
</div>

{{-- Relatórios & Previsões --}}
<div class="admin-card">
    <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
        <div>
            <h2 class="text-xl font-semibold text-slate-900">Relatórios & previsões</h2>
            <p class="text-sm text-slate-500">Baseado nos proxies disponíveis + preço médio de venda.</p>
        </div>
        <div class="flex gap-2">
            <button type="button" class="btn-secondary text-xs px-3 py-2"
                onclick="alert('Relatório mensal em desenvolvimento')">
                <i class="fas fa-chart-line"></i> Relatório mensal
            </button>
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

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const chartColors = {
            gateways: ['#23366f', '#448ccb', '#6ab7ff'],
            payments: ['#10b981', '#6366f1', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#f97316', '#ec4899'],
            reasons: ['#23366f', '#448ccb', '#6ab7ff', '#93c5fd', '#3b82f6', '#1e40af', '#1d4ed8', '#60a5fa']
        };

        const defaultOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 16,
                        usePointStyle: true,
                        pointStyleWidth: 10,
                        font: { size: 11, weight: '600' }
                    }
                }
            }
        };

        // Gráfico 1: Gateways (Doughnut)
        const gatewaysData = @json($chartGateways ?? []);
        if (gatewaysData.length > 0) {
            new Chart(document.getElementById('chartGateways'), {
                type: 'doughnut',
                data: {
                    labels: gatewaysData.map(i => i.label),
                    datasets: [{
                        data: gatewaysData.map(i => i.count),
                        backgroundColor: chartColors.gateways.slice(0, gatewaysData.length),
                        borderWidth: 0,
                        hoverOffset: 6
                    }]
                },
                options: {
                    ...defaultOptions,
                    cutout: '65%',
                    plugins: {
                        ...defaultOptions.plugins,
                        tooltip: {
                            callbacks: {
                                label: ctx => {
                                    const item = gatewaysData[ctx.dataIndex];
                                    return ` ${item.label}: ${item.count} vendas — R$ ${item.value.toLocaleString('pt-BR', { minimumFractionDigits: 2 })}`;
                                }
                            }
                        }
                    }
                }
            });
        } else {
            document.getElementById('chartGateways').parentElement.innerHTML = '<div class="flex items-center justify-center h-full text-slate-400 text-sm">Sem dados</div>';
        }

        // Gráfico 2: Formas de Pagamento (Doughnut)
        const paymentsData = @json($chartPaymentMethods ?? []);
        if (paymentsData.length > 0) {
            new Chart(document.getElementById('chartPaymentMethods'), {
                type: 'doughnut',
                data: {
                    labels: paymentsData.map(i => i.label),
                    datasets: [{
                        data: paymentsData.map(i => i.count),
                        backgroundColor: chartColors.payments.slice(0, paymentsData.length),
                        borderWidth: 0,
                        hoverOffset: 6
                    }]
                },
                options: {
                    ...defaultOptions,
                    cutout: '65%',
                    plugins: {
                        ...defaultOptions.plugins,
                        tooltip: {
                            callbacks: {
                                label: ctx => {
                                    const item = paymentsData[ctx.dataIndex];
                                    return ` ${item.label}: ${item.count} vendas — R$ ${item.value.toLocaleString('pt-BR', { minimumFractionDigits: 2 })}`;
                                }
                            }
                        }
                    }
                }
            });
        } else {
            document.getElementById('chartPaymentMethods').parentElement.innerHTML = '<div class="flex items-center justify-center h-full text-slate-400 text-sm">Sem dados</div>';
        }

        // Gráfico 3: Motivos de Compra (Bar horizontal)
        const reasonsData = @json($chartPurchaseReasons ?? []);
        if (reasonsData.length > 0) {
            new Chart(document.getElementById('chartPurchaseReasons'), {
                type: 'bar',
                data: {
                    labels: reasonsData.map(i => i.label),
                    datasets: [{
                        data: reasonsData.map(i => i.count),
                        backgroundColor: chartColors.reasons.slice(0, reasonsData.length),
                        borderRadius: 8,
                        barThickness: 24
                    }]
                },
                options: {
                    ...defaultOptions,
                    indexAxis: 'y',
                    plugins: {
                        ...defaultOptions.plugins,
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: ctx => ` ${ctx.parsed.x} ${ctx.parsed.x === 1 ? 'proxy' : 'proxies'}`
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: { stepSize: 1, font: { size: 11 } },
                            grid: { color: 'rgba(0,0,0,0.04)' }
                        },
                        y: {
                            ticks: { font: { size: 11, weight: '600' } },
                            grid: { display: false }
                        }
                    }
                }
            });
        } else {
            document.getElementById('chartPurchaseReasons').parentElement.innerHTML = '<div class="flex items-center justify-center h-full text-slate-400 text-sm">Sem dados</div>';
        }
    });
</script>