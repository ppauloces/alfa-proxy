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
        {{-- Tabs --}}
        <div class="flex border-b border-slate-100" id="saldoTabs">
            <button onclick="saldoSwitchTab('addBalance', this)"
                class="saldo-tab flex-1 py-5 font-bold text-sm flex items-center justify-center gap-2 text-[#23366f] bg-slate-50/50 border-b-2 border-[#23366f]">
                <i class="fas fa-plus-circle text-xs"></i> Adicionar Saldo
            </button>
            <button onclick="saldoSwitchTab('historyBalance', this)"
                class="saldo-tab flex-1 py-5 font-bold text-sm flex items-center justify-center gap-2 text-slate-400">
                <i class="fas fa-history text-xs"></i> Histórico de Recargas
            </button>
        </div>

        <div class="p-10">
            {{-- Erros --}}
            @if($errors->saldo->any())
                <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-100 text-red-600 text-sm font-medium">
                    {{ $errors->saldo->first() }}
                </div>
            @endif

            <!-- View: Adicionar Saldo -->
            <div id="addBalance" class="saldo-view animate-fadeIn">
                <form id="saldoForm" class="space-y-10 max-w-3xl">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mb-6">01. Escolha um valor</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                            @foreach([10, 25, 50, 100, 200, 500] as $amount)
                                <button type="button"
                                    onclick="saldoSelectAmount({{ $amount }}, this)"
                                    class="amount-btn p-6 rounded-2xl border-2 border-slate-100 text-center hover:border-[#23366f] hover:bg-blue-50/50 transition-all">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">R$</p>
                                    <p class="text-2xl font-black text-slate-900">{{ $amount }}</p>
                                </button>
                            @endforeach
                        </div>

                        <div class="mt-6 flex items-center gap-4">
                            <div class="h-px flex-1 bg-slate-100"></div>
                            <span class="text-[10px] font-bold text-slate-300 uppercase tracking-widest">ou valor personalizado</span>
                            <div class="h-px flex-1 bg-slate-100"></div>
                        </div>

                        <div class="mt-6 flex items-center gap-3 bg-slate-50 border border-slate-100 rounded-xl h-14 px-5 focus-within:border-[#23366f] transition-colors">
                            <span class="text-slate-400 font-bold shrink-0">R$</span>
                            <input type="text" id="saldoCustomAmount"
                                class="flex-1 bg-transparent border-none outline-none font-bold text-lg text-slate-700 placeholder-slate-300"
                                placeholder="0,00" inputmode="decimal"
                                oninput="saldoCustomInput(this)">
                        </div>

                        <input type="hidden" id="saldoValor" name="valor" value="">
                    </div>

                    <div>
                        <label class="block text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mb-6">02. Método de Pagamento</label>
                        <div class="grid md:grid-cols-3 gap-4">
                            <button type="button"
                                onclick="saldoSelectMethod('pix', this)"
                                class="payment-method-btn p-6 rounded-2xl border-2 border-slate-100 text-left hover:border-[#23366f] hover:bg-blue-50/50 transition-all">
                                <i class="fas fa-qrcode text-2xl text-[#23366f] mb-4 block"></i>
                                <p class="font-bold text-slate-700">PIX</p>
                                <p class="text-[10px] font-bold text-green-500 uppercase">Aprovação imediata</p>
                            </button>
                            <button type="button" disabled
                                class="p-6 rounded-2xl border-2 border-slate-100 bg-slate-50 text-left cursor-not-allowed opacity-50">
                                <i class="fas fa-credit-card text-2xl text-slate-300 mb-4 block"></i>
                                <p class="font-bold text-slate-400">Cartão</p>
                                <p class="text-[10px] font-bold text-slate-400 uppercase">Em breve</p>
                            </button>
                            <button type="button" disabled
                                class="p-6 rounded-2xl border-2 border-slate-100 bg-slate-50 text-left cursor-not-allowed opacity-50">
                                <i class="fab fa-bitcoin text-2xl text-slate-300 mb-4 block"></i>
                                <p class="font-bold text-slate-400">Crypto</p>
                                <p class="text-[10px] font-bold text-slate-400 uppercase">Em breve</p>
                            </button>
                        </div>
                        <input type="hidden" id="saldoMetodo" name="metodo_pagamento" value="">
                    </div>

                    <div class="pt-6 border-t border-slate-50 flex flex-col md:flex-row items-center justify-between gap-6">
                        <div class="flex items-center gap-3 text-slate-400">
                            <i class="fas fa-info-circle"></i>
                            <p class="text-xs font-medium">O saldo será creditado imediatamente após a confirmação.</p>
                        </div>
                        <button type="button" id="saldoSubmitBtn" onclick="saldoSubmit()"
                            class="w-full md:w-auto px-12 py-4 rounded-2xl bg-[#23366f] text-white font-black hover:bg-[#1a2856] transition-all disabled:opacity-40 disabled:cursor-not-allowed">
                            Confirmar Recarga
                        </button>
                    </div>
                </form>
            </div>

            <!-- View: Histórico -->
            <div id="historyBalance" class="saldo-view animate-fadeIn hidden">
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

<script>
(function () {
    let selectedAmount = null;
    let selectedMethod = null;

    window.saldoSwitchTab = function (viewId, btn) {
        document.querySelectorAll('.saldo-view').forEach(v => v.classList.add('hidden'));
        document.querySelectorAll('.saldo-tab').forEach(b => {
            b.classList.remove('text-[#23366f]', 'bg-slate-50/50', 'border-b-2', 'border-[#23366f]');
            b.classList.add('text-slate-400');
        });
        document.getElementById(viewId).classList.remove('hidden');
        btn.classList.add('text-[#23366f]', 'bg-slate-50/50', 'border-b-2', 'border-[#23366f]');
        btn.classList.remove('text-slate-400');
    };

    window.saldoSelectAmount = function (amount, btn) {
        document.querySelectorAll('.amount-btn').forEach(b => {
            b.classList.remove('border-[#23366f]', 'bg-blue-50/50');
            b.classList.add('border-slate-100');
        });
        btn.classList.add('border-[#23366f]', 'bg-blue-50/50');
        btn.classList.remove('border-slate-100');

        selectedAmount = amount;
        document.getElementById('saldoCustomAmount').value = '';
        document.getElementById('saldoValor').value = amount;
    };

    window.saldoCustomInput = function (input) {
        document.querySelectorAll('.amount-btn').forEach(b => {
            b.classList.remove('border-[#23366f]', 'bg-blue-50/50');
            b.classList.add('border-slate-100');
        });
        const val = parseFloat(input.value.replace(',', '.')) || 0;
        selectedAmount = val > 0 ? val : null;
        document.getElementById('saldoValor').value = val > 0 ? val : '';
    };

    window.saldoSelectMethod = function (method, btn) {
        document.querySelectorAll('.payment-method-btn').forEach(b => {
            b.classList.remove('border-[#23366f]', 'bg-blue-50/50');
            b.classList.add('border-slate-100');
        });
        btn.classList.add('border-[#23366f]', 'bg-blue-50/50');
        btn.classList.remove('border-slate-100');

        selectedMethod = method;
        document.getElementById('saldoMetodo').value = method;
    };

    window.saldoSubmit = async function () {
        if (!selectedAmount || selectedAmount < 1) {
            alert('Escolha um valor de R$ 1,00 ou mais.');
            return;
        }
        if (!selectedMethod) {
            alert('Selecione um método de pagamento.');
            return;
        }

        const btn = document.getElementById('saldoSubmitBtn');
        btn.disabled = true;
        btn.textContent = 'Aguarde...';

        const form = document.getElementById('saldoForm');
        const formData = new FormData(form);

        try {
            const response = await fetch('{{ route("saldo.adicionar") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: formData,
            });

            const data = await response.json();

            if (data.success && data.pix_modal) {
                renderPixModal(data.pix_modal);
                return;
            }

            if (data.errors) {
                alert(Object.values(data.errors).flat().join('\n'));
            } else {
                alert(data.error || data.message || 'Erro ao processar. Tente novamente.');
            }
        } catch (err) {
            console.error(err);
            alert('Erro de conexão. Tente novamente.');
        } finally {
            btn.disabled = false;
            btn.textContent = 'Confirmar Recarga';
        }
    };
})();
</script>
