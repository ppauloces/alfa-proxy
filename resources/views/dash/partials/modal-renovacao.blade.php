{{-- Modal de Renovação de Proxy --}}
<div id="modalRenovacao" class="admin-modal-overlay" style="display: none;">
    <div class="admin-modal" style="max-width: 800px;">
        {{-- Header --}}
        <div class="flex justify-between items-start mb-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-400 to-orange-500 text-white flex items-center justify-center text-xl shadow-lg shadow-amber-500/30">
                    <i class="fas fa-sync-alt"></i>
                </div>
                <div>
                    <h3 class="text-xl font-black text-slate-900 tracking-tight">Renovar Proxy</h3>
                    <p class="text-xs text-slate-400 font-bold uppercase tracking-widest" id="renovacao-proxy-endereco">
                        <!-- Preenchido via JS -->
                    </p>
                </div>
            </div>
            <button onclick="fecharModalRenovacao()" class="text-slate-400 hover:text-slate-900 transition-all">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        {{-- Informações do Proxy --}}
        <div class="bg-slate-50 rounded-2xl p-6 mb-6">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">País</p>
                    <p class="text-sm font-bold text-slate-900" id="renovacao-proxy-pais"><!-- JS --></p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Status Atual</p>
                    <p class="text-sm font-bold" id="renovacao-proxy-status"><!-- JS --></p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Expiração Atual</p>
                    <p class="text-sm font-bold text-slate-900" id="renovacao-expiracao-atual"><!-- JS --></p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Nova Expiração</p>
                    <p class="text-sm font-bold text-blue-600" id="renovacao-expiracao-nova">Selecione o período</p>
                </div>
            </div>
        </div>

        {{-- Alerta para Proxies Bloqueados --}}
        <div id="renovacao-alerta-bloqueado" class="hidden bg-amber-50 border-2 border-amber-200 rounded-2xl p-4 mb-6">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-400 text-white flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-lock"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-bold text-amber-900 mb-1">Proxy Bloqueado</p>
                    <p class="text-xs text-amber-700">Este proxy está bloqueado. Ao renovar, ele será automaticamente desbloqueado e ficará ativo novamente.</p>
                </div>
            </div>
        </div>

        {{-- Seleção de Período --}}
        <div class="mb-6">
            <h4 class="text-sm font-bold text-slate-900 mb-4 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-blue-50 text-blue-600 text-xs flex items-center justify-center font-black">1</span>
                Escolha o período de renovação
            </h4>

            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-3">
                @php
                    $periods = [
                        ['days' => 30, 'price' => Auth::user()->getPrecoBase(30), 'badge' => null],
                        ['days' => 60, 'price' => Auth::user()->getPrecoBase(60), 'badge' => '-12%'],
                        ['days' => 90, 'price' => Auth::user()->getPrecoBase(90), 'badge' => '-25%'],
                        ['days' => 180, 'price' => Auth::user()->getPrecoBase(180), 'badge' => '-33%'],
                        ['days' => 360, 'price' => Auth::user()->getPrecoBase(360), 'badge' => 'Melhor Preço'],
                    ];
                @endphp

                @foreach($periods as $period)
                    <label class="relative cursor-pointer group">
                        <input
                            type="radio"
                            name="periodo_renovacao"
                            value="{{ $period['days'] }}"
                            data-price="{{ $period['price'] }}"
                            class="peer hidden renovacao-period-radio"
                        >

                        <div class="flex flex-col items-center text-center p-4 rounded-2xl border-2 border-slate-100 bg-white transition-all duration-300 peer-checked:border-blue-600 peer-checked:bg-blue-50/20 peer-checked:ring-4 peer-checked:ring-blue-600/10 group-hover:border-slate-200">
                            @if($period['badge'])
                                <span class="absolute -top-2 left-1/2 -translate-x-1/2 px-2 py-0.5 bg-blue-600 text-white text-[9px] font-black rounded-full shadow-md z-10 whitespace-nowrap">
                                    {{ $period['badge'] }}
                                </span>
                            @endif

                            <span class="text-3xl mt-6 font-black text-slate-900 leading-none mb-1 group-hover:scale-110 transition-transform duration-300">
                                {{ $period['days'] }}
                            </span>
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-3">Dias</span>

                            <div class="flex items-center justify-center gap-1 text-blue-600">
                                <span class="text-xs font-bold">R$</span>
                                <span class="text-lg font-black">{{ number_format($period['price'], 0, ',', '.') }}</span>
                            </div>

                            <div class="mt-3 pt-3 border-t border-slate-100 w-full flex items-center justify-center gap-1 text-[8px] font-bold text-slate-400 group-hover:text-green-600 transition-colors">
                                <i class="fas fa-bolt"></i> Renovação imediata
                            </div>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Resumo --}}
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-6 mb-6 border-2 border-blue-100">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-sm font-black text-slate-900 uppercase tracking-wider">Resumo da renovação</h4>
                <div class="px-3 py-1 bg-white rounded-full shadow-sm">
                    <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest">PIX instantâneo</p>
                </div>
            </div>

            <div class="space-y-3">
                <div class="flex justify-between items-center py-2 border-b border-blue-100">
                    <span class="text-xs font-medium text-slate-600">Período adicional:</span>
                    <span class="text-sm font-black text-slate-900" id="renovacao-resumo-periodo">Selecione</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-blue-100">
                    <span class="text-xs font-medium text-slate-600">Valor:</span>
                    <span class="text-sm font-black text-slate-900" id="renovacao-resumo-valor">R$ 0,00</span>
                </div>
                <div class="pt-3 flex justify-between items-end">
                    <div>
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Total a Pagar</p>
                        <p class="text-3xl font-black text-blue-600" id="renovacao-resumo-total">R$ 0,00</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[9px] font-bold text-green-600 uppercase tracking-widest">Nova Validade</p>
                        <p class="text-sm font-black text-slate-900" id="renovacao-resumo-nova-data">--</p>
                    </div>
                </div>
            </div>
        </div>

        
        {{-- Renovacao automatica --}}
        <div class="bg-slate-50 rounded-2xl p-4 mb-6 border border-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-800">Renovação automática no cartão</p>
                    <p class="text-[10px] text-slate-500 font-semibold mt-1">Cobrança automática no cartão padrão. Se falhar, tentamos os outros cartões cadastrados.</p>
                </div>
                <label class="switch scale-90">
                    <input type="checkbox" id="renovacao-auto-toggle">
                    <span class="slider"></span>
                </label>
            </div>
        </div>
{{-- Botões --}}
        <div class="flex gap-3">
            <button
                type="button"
                onclick="fecharModalRenovacao()"
                class="flex-1 py-4 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-2xl font-black text-sm uppercase tracking-wider transition-all"
            >
                Cancelar
            </button>
            <button
                type="button"
                id="btn-confirmar-renovacao"
                onclick="confirmarRenovacao()"
                disabled
                class="flex-1 py-4 bg-gradient-to-r from-amber-400 to-orange-500 hover:from-amber-500 hover:to-orange-600 text-white rounded-2xl font-black text-sm uppercase tracking-wider transition-all shadow-lg shadow-amber-500/30 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
            >
                <i class="fas fa-shopping-cart"></i>
                Renovar via PIX
            </button>
        </div>
    </div>
</div>

<style>
    #modalRenovacao.admin-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(15, 23, 42, 0.7);
        backdrop-filter: blur(8px);
        z-index: 10000;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: fadeIn 0.2s ease;
        padding: 2rem;
    }

    #modalRenovacao .admin-modal {
        background: white;
        border-radius: 28px;
        padding: 2rem;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 25px 70px rgba(15, 23, 42, 0.3);
        animation: slideInScale 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    @keyframes slideInScale {
        from {
            opacity: 0;
            transform: scale(0.95) translateY(20px);
        }
        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }
</style>

