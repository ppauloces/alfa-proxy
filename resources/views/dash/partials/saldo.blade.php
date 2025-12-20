<div class="flex flex-col gap-6">
    {{-- Header da Seção --}}
    <div class="space-y-1">
        <p class="text-[10px] font-bold text-[#448ccb] uppercase tracking-[0.3em]">Financeiro</p>
        <h1 class="text-4xl font-black text-slate-900 tracking-tight">Minha <span class="text-[#23366f]">Carteira</span></h1>
        <p class="text-slate-500 font-medium max-w-xl">Gerencie seu saldo e adicione créditos para futuras compras.</p>
    </div>

    {{-- Cartão de Saldo Premium --}}
    <div class="relative overflow-hidden bg-[#23366f] rounded-[2.5rem] p-10 text-white shadow-2xl shadow-blue-900/20">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-8">
            <div class="space-y-4">
                <div class="flex items-center gap-2 opacity-70">
                    <i class="fas fa-wallet text-xs"></i>
                    <span class="text-[10px] font-bold uppercase tracking-[0.2em]">Saldo Disponível</span>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-bold opacity-50">R$</span>
                    <span class="text-6xl font-black tracking-tighter">{{ number_format($usuario->saldo ?? 0, 2, ',', '.') }}</span>
                </div>
                <div class="flex items-center gap-6 pt-4 border-t border-white/10">
                    <div>
                        <p class="text-[9px] font-bold uppercase tracking-wider opacity-40 mb-1">Titular da Conta</p>
                        <p class="text-sm font-bold">{{ $usuario->name ?? 'Usuario' }}</p>
                    </div>
                    <div class="w-px h-8 bg-white/10"></div>
                    <div>
                        <p class="text-[9px] font-bold uppercase tracking-wider opacity-40 mb-1">ID da Conta</p>
                        <p class="text-sm font-bold">#{{ str_pad($usuario->id ?? '0', 5, '0', STR_PAD_LEFT) }}</p>
                    </div>
                </div>
            </div>
            
            <div class="hidden lg:block">
                <div class="w-32 h-32 rounded-full bg-white/5 border border-white/10 flex items-center justify-center backdrop-blur-sm">
                    <i class="fas fa-shield-halved text-5xl opacity-20"></i>
                </div>
            </div>
        </div>

        {{-- Elementos Decorativos de Fundo --}}
        <div class="absolute -top-24 -right-24 w-64 h-64 bg-[#448ccb] rounded-full blur-[80px] opacity-20"></div>
        <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-blue-400 rounded-full blur-[80px] opacity-10"></div>
    </div>

    {{-- Área de Ações e Histórico --}}
    <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
        <div class="flex border-b border-slate-50">
            <button class="switch-tab flex-1 py-5 font-bold text-sm transition-all flex items-center justify-center gap-2 active text-[#23366f] bg-slate-50/50" data-view="add">
                <i class="fas fa-plus-circle text-xs"></i> Adicionar Saldo
            </button>
            <button class="switch-tab flex-1 py-5 font-bold text-sm transition-all flex items-center justify-center gap-2 text-slate-400 hover:bg-slate-50" data-view="history">
                <i class="fas fa-history text-xs"></i> Histórico de Recargas
            </button>
        </div>

        <div class="p-10">
            <!-- View: Adicionar Saldo -->
            <div id="addBalance" class="view-content animate-fadeIn">
                <div class="max-w-3xl">
                    <form action="{{ route('saldo.adicionar') }}" method="POST" id="rechargeForm" class="space-y-10">
                        @csrf
                        <div>
                            <label class="block text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mb-6">01. Escolha um valor</label>
                            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                                @foreach([10, 25, 50, 100, 200, 500] as $amount)
                                    <div class="amount-btn group p-6 rounded-2xl border-2 border-slate-50 hover:border-[#448ccb] hover:bg-blue-50/30 transition-all cursor-pointer text-center" data-amount="{{ $amount }}">
                                        <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">R$</p>
                                        <p class="text-2xl font-black text-slate-900 group-hover:text-[#23366f] transition-colors">{{ $amount }}</p>
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="mt-6 flex items-center gap-4">
                                <div class="h-px flex-1 bg-slate-100"></div>
                                <span class="text-[10px] font-bold text-slate-300 uppercase tracking-widest">ou valor personalizado</span>
                                <div class="h-px flex-1 bg-slate-100"></div>
                            </div>

                            <div class="mt-6 relative max-w-xs">
                                <span class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 font-bold">R$</span>
                                <input type="text" name="valor" id="customAmount" class="form-input pl-12 bg-slate-50 border-transparent focus:bg-white focus:border-[#448ccb] h-14 rounded-xl font-bold text-lg" placeholder="0,00" inputmode="decimal">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mb-6">02. Método de Pagamento</label>
                            <div class="grid md:grid-cols-3 gap-4">
                                <div class="payment-method p-6 rounded-2xl border-2 border-slate-50 hover:border-[#23366f] transition-all cursor-pointer group" data-method="pix">
                                    <i class="fas fa-qrcode text-2xl text-slate-300 group-hover:text-[#448ccb] mb-4 block"></i>
                                    <p class="font-bold text-slate-900">PIX</p>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase">Aprovação Instantânea</p>
                                </div>
                                <div class="p-6 rounded-2xl border-2 border-slate-100 bg-slate-50 cursor-not-allowed opacity-60">
                                    <i class="fas fa-credit-card text-2xl text-slate-300 mb-4 block"></i>
                                    <p class="font-bold text-slate-400">Cartão</p>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase">Em breve</p>
                                </div>
                                <div class="p-6 rounded-2xl border-2 border-slate-100 bg-slate-50 cursor-not-allowed opacity-60">
                                    <i class="fab fa-bitcoin text-2xl text-slate-300 mb-4 block"></i>
                                    <p class="font-bold text-slate-400">Crypto</p>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase">Em breve</p>
                                </div>
                            </div>
                            <input type="hidden" name="metodo_pagamento" id="walletPaymentMethod" required>
                        </div>

                        <div class="pt-6 border-t border-slate-50 flex flex-col md:flex-row items-center justify-between gap-6">
                            <div class="flex items-center gap-3 text-slate-400">
                                <i class="fas fa-info-circle"></i>
                                <p class="text-xs font-medium">O saldo será creditado imediatamente após a confirmação.</p>
                            </div>
                            <button type="submit" class="w-full md:w-auto px-12 py-4 rounded-2xl bg-[#23366f] text-white font-black hover:scale-[1.02] transition-all shadow-xl shadow-blue-900/20">
                                Confirmar Recarga
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- View: Histórico -->
            <div id="historyBalance" class="view-content animate-fadeIn hidden">
                @if(count($transacoes) > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                    <th class="text-left py-4">Tipo</th>
                                    <th class="text-left py-4">Data</th>
                                    <th class="text-center py-4">Valor</th>
                                    <th class="text-right py-4">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach($transacoes as $transacao)
                                    <tr class="group hover:bg-slate-50/50 transition-all">
                                        <td class="py-5">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-lg bg-green-50 text-green-600 flex items-center justify-center text-xs">
                                                    <i class="fas fa-arrow-up"></i>
                                                </div>
                                                <span class="font-bold text-slate-700 text-sm">Recarga de Saldo</span>
                                            </div>
                                        </td>
                                        <td class="py-5">
                                            <span class="text-xs font-bold text-slate-400">{{ \Carbon\Carbon::parse($transacao->created_at)->format('d/m/Y H:i') }}</span>
                                        </td>
                                        <td class="py-5 text-center">
                                            <span class="font-black text-slate-900">R$ {{ number_format($transacao->valor, 2, ',', '.') }}</span>
                                        </td>
                                        <td class="py-5 text-right">
                                            @if($transacao->status == 1)
                                                <span class="px-3 py-1 rounded-lg bg-green-50 text-green-600 text-[10px] font-black uppercase">Aprovada</span>
                                            @else
                                                <span class="px-3 py-1 rounded-lg bg-amber-50 text-amber-600 text-[10px] font-black uppercase">Pendente</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="py-20 text-center">
                        <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-receipt text-3xl text-slate-200"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 mb-2">Nenhuma recarga encontrada</h3>
                        <p class="text-slate-400 text-sm font-medium">Seu histórico de recargas aparecerá aqui.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
