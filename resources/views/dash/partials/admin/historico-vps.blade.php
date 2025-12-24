<div class="flex flex-col gap-6">
    {{-- Header da Seção --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div class="space-y-1">
            <p class="text-[10px] font-bold text-[#448ccb] uppercase tracking-[0.3em]">Gestão de Infraestrutura</p>
            <h1 class="text-4xl font-black text-slate-900 tracking-tight">Histórico <span class="text-[#23366f]">VPS</span></h1>
            <p class="text-slate-500 font-medium max-w-xl">Visualize todas as VPS contratadas, status de expiração e estatísticas gerais do seu farm.</p>
        </div>

        <div class="flex items-center gap-3">
            <button type="button" class="px-5 py-2.5 rounded-2xl bg-white border border-slate-200 text-slate-600 text-sm font-bold hover:bg-slate-50 transition-all flex items-center gap-2">
                <i class="fas fa-download text-xs"></i> Exportar CSV
            </button>
            <button type="button" data-section-link="admin-proxies" class="px-5 py-2.5 rounded-2xl bg-[#23366f] text-white text-sm font-bold shadow-lg shadow-blue-900/20 hover:scale-[1.02] transition-all flex items-center gap-2">
                <i class="fas fa-plus text-xs"></i> Nova VPS
            </button>
        </div>
    </div>

    <!-- Estatísticas Gerais -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-[#23366f] flex items-center justify-center text-xl">
                    <i class="fas fa-server"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total de VPS</p>
                    <p class="text-2xl font-black text-slate-900">{{ $estatisticas['total_vps'] }}</p>
                </div>
            </div>
            <div class="flex items-center gap-4 text-xs font-bold">
                <span class="text-green-600">{{ $estatisticas['vps_ativas'] }} ativas</span>
                <span class="text-slate-300">|</span>
                <span class="text-red-500">{{ $estatisticas['vps_expiradas'] }} expiradas</span>
            </div>
        </div>

        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-2xl bg-green-50 text-green-600 flex items-center justify-center text-xl">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Investido</p>
                    <p class="text-2xl font-black text-slate-900">R$ {{ number_format($estatisticas['total_gasto'], 2, ',', '.') }}</p>
                </div>
            </div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Acumulado em todas as VPS</p>
        </div>

        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl">
                    <i class="fas fa-network-wired"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Proxies Gerados</p>
                    <p class="text-2xl font-black text-slate-900">{{ number_format($estatisticas['total_proxies_geradas'], 0, ',', '.') }}</p>
                </div>
            </div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Média: {{ $estatisticas['media_proxies_por_vps'] }} por VPS</p>
        </div>
    </div>

    <!-- Tabela de Histórico -->
    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-8 border-b border-slate-50 flex items-center justify-between">
            <h2 class="text-xl font-black text-slate-900 tracking-tight">Histórico Completo</h2>
        </div>

        @if($vpsHistorico->count() === 0)
            <div class="py-20 text-center">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-server text-3xl text-slate-200"></i>
                </div>
                <h3 class="text-xl font-black text-slate-900 mb-2">Nenhuma VPS cadastrada</h3>
                <p class="text-slate-400 text-sm font-medium">O histórico aparecerá aqui quando você cadastrar sua primeira VPS.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="bg-slate-50/50">
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">VPS</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Dados Técnicos</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Investimento</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Datas</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Portas</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($vpsHistorico as $vps)
                            <tr class="hover:bg-slate-50/30 transition-colors">
                                <td class="px-8 py-6">
                                    <div class="flex flex-col">
                                        <span class="font-black text-slate-900 text-base">{{ $vps->apelido }}</span>
                                        <span class="text-xs font-bold text-slate-400">{{ $vps->hospedagem }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex flex-col">
                                        <span class="font-mono font-black text-[#23366f]">{{ $vps->ip }}</span>
                                        <span class="text-xs font-bold text-slate-500 flex items-center gap-1.5">
                                            <i class="fas fa-globe-americas text-[#448ccb]"></i> {{ $vps->pais }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex flex-col">
                                        <span class="font-black text-green-600">{{ $vps->valor_formatado }}</span>
                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $vps->periodo_dias }} dias</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex flex-col gap-1">
                                        <div class="flex items-center gap-2">
                                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Expira:</span>
                                            <span class="font-black text-slate-700">{{ $vps->data_expiracao }}</span>
                                        </div>
                                        <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider {{ $vps->badge_expiracao }} w-fit">
                                            {{ $vps->status_expiracao }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <div class="inline-flex flex-col items-center justify-center w-12 h-12 rounded-2xl bg-slate-50 border border-slate-100">
                                        <span class="font-black text-slate-900 leading-none">{{ $vps->total_proxies }}</span>
                                        <span class="text-[8px] font-black text-slate-400 uppercase mt-1">total</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    @php
                                        $genStatusClass = match($vps->status_geracao) {
                                            'pending' => 'bg-amber-50 text-amber-600',
                                            'processing' => 'bg-blue-50 text-blue-600',
                                            'completed' => 'bg-green-50 text-green-600',
                                            'failed' => 'bg-red-50 text-red-600',
                                            default => 'bg-slate-50 text-slate-600',
                                        };
                                        $genStatusText = match($vps->status_geracao) {
                                            'pending' => 'Na fila',
                                            'processing' => 'Gerando',
                                            'completed' => 'Concluído',
                                            'failed' => 'Erro',
                                            default => 'N/A',
                                        };
                                    @endphp
                                    <div class="flex flex-col gap-1">
                                        <span class="px-2 py-1 rounded-md text-[9px] font-black uppercase tracking-wider {{ $genStatusClass }} w-fit">
                                            {{ $genStatusText }}
                                        </span>
                                        @if($vps->status_geracao === 'completed')
                                            <span class="text-[10px] font-bold text-green-500">{{ $vps->proxies_geradas }} IPs ativos</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center justify-center gap-2">
                                        <button type="button" class="w-9 h-9 rounded-xl bg-slate-50 text-slate-400 hover:text-[#23366f] hover:bg-blue-50 transition-all flex items-center justify-center" title="Ver detalhes">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button type="button" class="w-9 h-9 rounded-xl bg-slate-50 text-slate-400 hover:text-slate-900 hover:bg-slate-100 transition-all flex items-center justify-center" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
