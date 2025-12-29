
<div class="flex flex-col gap-2 mb-8">
    <p class="text-sm uppercase tracking-[0.35em] text-slate-500">Financeiro & previsões</p>
    <h1 class="text-3xl font-bold text-slate-900">Entradas, saídas e relatórios</h1>
    <p class="text-slate-500">Controle o fluxo de caixa, registre despesas manuais e visualize o potencial de vendas com base no estoque disponível.</p>
</div>

{{-- Cards Financeiros --}}
<div class="grid md:grid-cols-4 gap-4 mb-8">
    @foreach($financeCards as $card)
        <div class="finance-card admin-card">
            <p class="text-xs uppercase tracking-[0.3em] text-slate-400">{{ $card['label'] }}</p>
            <p class="text-2xl font-bold text-slate-900 mt-2">{{ $card['value'] }}</p>
            <p class="text-sm {{ str_contains($card['trend'], 'Negativo') ? 'text-red-500' : 'text-emerald-500' }} mt-1">{{ $card['trend'] }}</p>
            <div class="chart-bar mt-3">
                <span style="width: {{ min(100, max(0, $card['bar'])) }}%"></span>
            </div>
        </div>
    @endforeach

    {{-- Card de Uso Interno --}}
    <div class="finance-card admin-card bg-gradient-to-br from-indigo-50 to-purple-50 border-indigo-200">
        <p class="text-xs uppercase tracking-[0.3em] text-indigo-600 font-bold">Uso Interno</p>
        <p class="text-2xl font-bold text-indigo-900 mt-2">{{ $usoInternoStats['total'] }}</p>
        <p class="text-sm text-indigo-600 mt-1 flex items-center gap-1">
            <i class="fas fa-briefcase"></i>
            {{ $usoInternoStats['total'] === 1 ? 'Proxy reservada' : 'Proxies reservadas' }}
        </p>
        <div class="chart-bar mt-3 bg-indigo-100">
            <span class="bg-indigo-500" style="width: 100%"></span>
        </div>
    </div>
</div>

{{-- Extratos de Saídas e Entradas --}}
<div class="grid lg:grid-cols-2 gap-6 mb-8">
    {{-- Extrato de Saídas --}}
    <div class="admin-card">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-xl font-semibold text-slate-900">Extrato de saídas</h2>
            <button type="button" class="btn-secondary text-xs px-3 py-2" onclick="alert('Funcionalidade em desenvolvimento')">
                <i class="fas fa-plus"></i> Despesa manual
            </button>
        </div>

        @if(count($financeExtract['saida']) > 0)
            <div class="space-y-3 text-sm max-h-[400px] overflow-y-auto">
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

            <div class="mt-4 pt-3 border-t border-slate-100">
                <a href="{{ route('admin.proxies', ['section' => 'admin-historico-vps']) }}"
                   class="text-xs text-[#448ccb] font-bold uppercase tracking-wider hover:underline">
                    <i class="fas fa-arrow-right mr-1"></i> Ver histórico completo de VPS
                </a>
            </div>
        @else
            <div class="text-center py-8 text-slate-400">
                <i class="fas fa-inbox text-4xl mb-3"></i>
                <p class="text-sm">Nenhuma despesa registrada</p>
            </div>
        @endif
    </div>

    {{-- Extrato de Entradas --}}
    <div class="admin-card">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-xl font-semibold text-slate-900">Extrato de entradas</h2>
            <button type="button" class="btn-secondary text-xs px-3 py-2" onclick="alert('Funcionalidade em desenvolvimento')">
                <i class="fas fa-file-invoice-dollar"></i> Gerar recibos
            </button>
        </div>

        @if(count($financeExtract['entrada']) > 0)
            <div class="space-y-3 text-sm max-h-[400px] overflow-y-auto">
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

            <div class="mt-4 pt-3 border-t border-slate-100">
                <a href="{{ route('admin.transacoes') }}"
                   class="text-xs text-[#448ccb] font-bold uppercase tracking-wider hover:underline">
                    <i class="fas fa-arrow-right mr-1"></i> Ver todas as transações
                </a>
            </div>
        @else
            <div class="text-center py-8 text-slate-400">
                <i class="fas fa-inbox text-4xl mb-3"></i>
                <p class="text-sm">Nenhuma transação registrada</p>
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
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-indigo-50 text-indigo-700 rounded-lg text-xs font-semibold">
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

{{-- Relatórios & Previsões --}}
<div class="admin-card">
    <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
        <div>
            <h2 class="text-xl font-semibold text-slate-900">Relatórios & previsões</h2>
            <p class="text-sm text-slate-500">Baseado nos proxies disponíveis + preço médio de venda.</p>
        </div>
        <div class="flex gap-2">
            <button type="button" class="btn-secondary text-xs px-3 py-2" onclick="alert('Relatório mensal em desenvolvimento')">
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
