<div x-data="financeiroPanel()" x-init="initDateRange()">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <p class="text-sm uppercase tracking-[0.35em] text-slate-500">Painel master</p>
            <h1 class="text-3xl font-bold text-slate-900">Financeiro</h1>
            <p class="text-slate-500">Visão consolidada de relatórios financeiros e transações de vendas.</p>
        </div>

        {{-- DateRangePicker --}}
        <div class="relative">
            <div class="relative">
                <i class="fas fa-calendar-alt absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" id="financeiro-daterange" readonly
                    class="w-64 h-11 pl-10 pr-4 rounded-xl bg-white border border-slate-200 font-semibold text-sm text-slate-700 cursor-pointer hover:border-[#448ccb] focus:outline-none focus:ring-4 focus:ring-[#448ccb]/20 focus:border-[#23366f] transition">
            </div>
            <div x-show="loading" x-cloak class="absolute right-3 top-1/2 -translate-y-1/2">
                <i class="fas fa-spinner fa-spin text-[#448ccb] text-sm"></i>
            </div>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- CARDS FINANCEIROS --}}
    {{-- ========================================== --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <template x-for="(card, idx) in cards" :key="idx">
            <div class="finance-card admin-card"
                :class="card.label === 'Vendas Revendedores' ? 'bg-gradient-to-br from-amber-50 to-orange-50 border-amber-200' : ''">
                <p class="text-xs uppercase tracking-[0.3em]"
                    :class="card.label === 'Vendas Revendedores' ? 'text-amber-600 font-bold' : 'text-slate-400'"
                    x-text="card.label"></p>
                <p class="text-2xl font-bold mt-2"
                    :class="card.label === 'Vendas Revendedores' ? 'text-amber-900' : 'text-slate-900'"
                    x-text="card.value"></p>
                <p class="text-sm mt-1"
                    :class="cardTrendClass(card)">
                    <template x-if="card.label === 'Vendas Revendedores'">
                        <i class="fas fa-crown mr-1"></i>
                    </template>
                    <span x-text="card.trend"></span>
                </p>
                <div class="chart-bar mt-3" :class="card.label === 'Vendas Revendedores' ? 'bg-amber-100' : ''">
                    <span :class="card.label === 'Vendas Revendedores' ? 'bg-amber-500' : ''"
                        :style="'width:' + Math.min(100, Math.max(0, card.bar)) + '%'"></span>
                </div>
            </div>
        </template>
    </div>

    {{-- ========================================== --}}
    {{-- LANÇAR RENOVAÇÃO DE VPS --}}
    {{-- ========================================== --}}
    <div class="admin-card mb-8" x-data="renovacaoVps()">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
            <div>
                <h2 class="text-xl font-semibold text-slate-900">Lançar Renovação de VPS</h2>
                <p class="text-sm text-slate-500">Registre manualmente a renovação de uma VPS como despesa.</p>
            </div>
        </div>

        {{-- Mensagens de feedback --}}
        <div x-show="successMsg" x-transition x-cloak
            class="mb-4 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-semibold flex items-center gap-2">
            <i class="fas fa-check-circle"></i>
            <span x-text="successMsg"></span>
        </div>
        <div x-show="errorMsg" x-transition x-cloak
            class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm font-semibold flex items-center gap-2">
            <i class="fas fa-exclamation-circle"></i>
            <span x-text="errorMsg"></span>
        </div>

        @php
            $vpsOptions = [];
            $vpsData = [];
            foreach ($vpsFarm ?? [] as $vps) {
                $dataContratacao = $vps->data_contratacao ? $vps->data_contratacao->format('d/m/Y') : '';
                $vpsOptions[$vps->id] = $vps->apelido . ' — ' . $dataContratacao;
                $vpsData[$vps->id] = [
                    'valor' => $vps->valor_renovacao ?? $vps->valor_raw,
                    'apelido' => $vps->apelido,
                ];
            }
        @endphp

        <form @submit.prevent="submit()" class="grid md:grid-cols-5 gap-4 items-end">
            {{-- Selecionar VPS --}}
            <div class="flex flex-col gap-1.5">
                <span class="text-xs font-semibold text-slate-600 uppercase tracking-wider">VPS</span>
                <x-ui.select name="vps_id" placeholder="Selecione uma VPS" :options="$vpsOptions" />
            </div>

            {{-- Valor --}}
            <label class="flex flex-col gap-1.5">
                <span class="text-xs font-semibold text-slate-600 uppercase tracking-wider">Valor (R$)</span>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm font-semibold">R$</span>
                    <input type="text" x-model="form.valor_display" @input="maskValor($event)" placeholder="0,00"
                        class="w-full h-11 px-4 pl-10 rounded-xl bg-white border border-slate-200 font-semibold text-slate-900 focus:outline-none focus:ring-4 focus:ring-[#448ccb]/20 focus:border-[#23366f] transition">
                </div>
            </label>

            {{-- Data de vencimento --}}
            <label class="flex flex-col gap-1.5">
                <span class="text-xs font-semibold text-slate-600 uppercase tracking-wider">Data</span>
                <input type="date" x-model="form.data_vencimento"
                    class="w-full h-11 px-4 rounded-xl bg-white border border-slate-200 font-semibold text-slate-900 focus:outline-none focus:ring-4 focus:ring-[#448ccb]/20 focus:border-[#23366f] transition">
            </label>

            {{-- Status --}}
            <div class="flex flex-col gap-1.5">
                <span class="text-xs font-semibold text-slate-600 uppercase tracking-wider">Status</span>
                <x-ui.select name="status_renovacao" value="pago" :options="['pago' => 'Pago', 'pendente' => 'Pendente']" />
            </div>

            {{-- Botão --}}
            <button type="submit" :disabled="loading || !form.vps_id || !form.valor"
                class="btn-primary text-xs px-4 h-11 rounded-xl whitespace-nowrap disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                <i class="fas fa-plus" x-show="!loading"></i>
                <i class="fas fa-spinner fa-spin" x-show="loading" x-cloak></i>
                <span x-text="loading ? 'Salvando...' : 'Lançar'"></span>
            </button>
        </form>
    </div>

    {{-- ========================================== --}}
    {{-- EXTRATO BANCÁRIO --}}
    {{-- ========================================== --}}
    <div class="admin-card mb-8">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
            <div>
                <h2 class="text-xl font-semibold text-slate-900">Extrato</h2>
                <p class="text-sm text-slate-500">Movimentações financeiras consolidadas.</p>
            </div>

            {{-- Toggle buttons (colados) --}}
            <div class="inline-flex rounded-xl overflow-hidden border border-slate-200">
                <button type="button" @click="tab = 'saidas'" :class="tab === 'saidas'
                        ? 'bg-red-500 text-white shadow-sm'
                        : 'bg-white text-slate-600 hover:bg-slate-50'"
                    class="px-4 py-2 text-xs font-bold uppercase tracking-wider transition-all flex items-center gap-2">
                    <i class="fas fa-arrow-down"></i> Saídas
                </button>
                <button type="button" @click="tab = 'entradas'" :class="tab === 'entradas'
                        ? 'bg-emerald-500 text-white shadow-sm'
                        : 'bg-white text-slate-600 hover:bg-slate-50'"
                    class="px-4 py-2 text-xs font-bold uppercase tracking-wider transition-all flex items-center gap-2">
                    <i class="fas fa-arrow-up"></i> Entradas
                </button>
            </div>
        </div>

        {{-- Tab: Saídas --}}
        <div x-show="tab === 'saidas'" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">

            <template x-if="saidas.length > 0">
                <div class="space-y-1 text-sm max-h-[480px] overflow-y-auto pr-1">
                    <template x-for="(saida, idx) in saidas" :key="idx">
                        <div class="flex items-center justify-between py-3 px-3 rounded-xl hover:bg-slate-50 transition-colors"
                            :class="idx < saidas.length - 1 ? 'border-b border-slate-100' : ''">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0"
                                    :class="saida.tipo === 'renovacao' ? 'bg-blue-100 text-blue-600' : (saida.tipo === 'uso_interno' ? 'bg-indigo-100 text-indigo-600' : (saida.tipo === 'compra' ? 'bg-emerald-100 text-emerald-600' : 'bg-red-100 text-red-500'))">
                                    <i class="fas text-xs" :class="saida.tipo === 'renovacao' ? 'fa-sync-alt' : (saida.tipo === 'uso_interno' ? 'fa-briefcase' : (saida.tipo === 'compra' ? 'fa-server' : 'fa-arrow-down'))"></i>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <p class="font-semibold text-slate-900" x-text="saida.descricao"></p>
                                        <template x-if="saida.tipo === 'renovacao'">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-gradient-to-r from-blue-100 to-cyan-100 border border-blue-200 rounded-full">
                                                <i class="fas fa-sync-alt text-[8px] text-blue-600"></i>
                                                <span class="text-[9px] font-black text-blue-700 uppercase tracking-wider">Renovação</span>
                                            </span>
                                        </template>
                                        <template x-if="saida.tipo === 'compra'">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-gradient-to-r from-emerald-100 to-green-100 border border-emerald-200 rounded-full">
                                                <i class="fas fa-server text-[8px] text-emerald-600"></i>
                                                <span class="text-[9px] font-black text-emerald-700 uppercase tracking-wider">Contratação</span>
                                            </span>
                                        </template>
                                        <template x-if="saida.tipo === 'uso_interno'">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-gradient-to-r from-indigo-100 to-purple-100 border border-indigo-200 rounded-full">
                                                <i class="fas fa-briefcase text-[8px] text-indigo-600"></i>
                                                <span class="text-[9px] font-black text-indigo-700 uppercase tracking-wider">Uso Interno</span>
                                            </span>
                                        </template>
                                        <template x-if="saida.status === 'pendente'">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-amber-50 border border-amber-200 rounded-full">
                                                <span class="text-[9px] font-black text-amber-700 uppercase tracking-wider">Pendente</span>
                                            </span>
                                        </template>
                                    </div>
                                    <p class="text-xs text-slate-400" x-text="saida.categoria + ' • ' + saida.data"></p>
                                </div>
                            </div>
                            <span class="font-bold whitespace-nowrap"
                                :class="saida.tipo === 'uso_interno' ? 'text-indigo-500 font-mono text-xs' : 'text-red-500'"
                                x-text="saida.valor"></span>
                        </div>
                    </template>
                </div>
            </template>
            <template x-if="saidas.length === 0 && !loading">
                <div class="text-center py-12 text-slate-400">
                    <i class="fas fa-inbox text-4xl mb-3"></i>
                    <p class="text-sm">Nenhuma despesa no período</p>
                </div>
            </template>
        </div>

        {{-- Tab: Entradas --}}
        <div x-show="tab === 'entradas'" x-cloak x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">

            <template x-if="entradas.length > 0">
                <div class="space-y-1 text-sm max-h-[480px] overflow-y-auto pr-1">
                    <template x-for="(entrada, idx) in entradas" :key="idx">
                        <div class="flex items-center justify-between py-3 px-3 rounded-xl hover:bg-slate-50 transition-colors"
                            :class="idx < entradas.length - 1 ? 'border-b border-slate-100' : ''">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 bg-emerald-100 text-emerald-600">
                                    <i class="fas fa-arrow-up text-xs"></i>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <p class="font-semibold text-slate-900" x-text="entrada.quantidade + ' ' + (entrada.quantidade === 1 ? 'proxy' : 'proxies')"></p>
                                        <template x-if="entrada.is_revendedor">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-gradient-to-r from-amber-100 to-orange-100 border border-amber-200 rounded-full">
                                                <i class="fas fa-crown text-[8px] text-amber-600"></i>
                                                <span class="text-[9px] font-black text-amber-700 uppercase tracking-wider">Revendedor</span>
                                            </span>
                                        </template>
                                    </div>
                                    <p class="text-xs text-slate-400 flex items-center gap-1">
                                        <i class="fas fa-user text-[9px]"></i>
                                        <span x-text="entrada.username + ' • ' + entrada.categoria + ' • ' + entrada.data"></span>
                                    </p>
                                </div>
                            </div>
                            <span class="text-emerald-500 font-bold whitespace-nowrap" x-text="entrada.valor_total"></span>
                        </div>
                    </template>
                </div>
            </template>
            <template x-if="entradas.length === 0 && !loading">
                <div class="text-center py-12 text-slate-400">
                    <i class="fas fa-inbox text-4xl mb-3"></i>
                    <p class="text-sm">Nenhuma entrada no período</p>
                </div>
            </template>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- RELATÓRIOS & PREVISÕES --}}
    {{-- ========================================== --}}
    <div class="admin-card">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
            <div>
                <h2 class="text-xl font-semibold text-slate-900">Relatórios & previsões</h2>
                <p class="text-sm text-slate-500">Baseado nos proxies disponíveis + preço médio de venda.</p>
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
</div>

<script>
function financeiroPanel() {
    return {
        loading: false,
        tab: 'saidas',
        cards: @json($financeCards),
        saidas: @json($financeExtract['saida']),
        entradas: @json($financeExtract['entrada_agrupada'] ?? []),

        cardTrendClass(card) {
            if (card.label === 'Vendas Revendedores') return 'text-amber-600 flex items-center gap-1';
            if (card.trend && card.trend.includes('Negativo')) return 'text-red-500';
            return 'text-emerald-500';
        },

        initDateRange() {
            const self = this;
            moment.locale('pt-br');

            $('#financeiro-daterange').daterangepicker({
                startDate: moment().subtract(29, 'days'),
                endDate: moment(),
                ranges: {
                    'Hoje': [moment(), moment()],
                    'Últimos 7 dias': [moment().subtract(6, 'days'), moment()],
                    'Últimos 30 dias': [moment().subtract(29, 'days'), moment()],
                    'Este mês': [moment().startOf('month'), moment().endOf('month')],
                    'Último mês': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    'Últimos 3 meses': [moment().subtract(3, 'months').startOf('month'), moment()],
                    'Todo período': [moment('2020-01-01'), moment()],
                },
                locale: {
                    format: 'DD/MM/YYYY',
                    applyLabel: 'Aplicar',
                    cancelLabel: 'Cancelar',
                    fromLabel: 'De',
                    toLabel: 'Até',
                    customRangeLabel: 'Personalizado',
                    daysOfWeek: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'],
                    monthNames: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
                        'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
                    firstDay: 0
                },
                opens: 'left',
                alwaysShowCalendars: true,
                linkedCalendars: false,
            }, function(start, end) {
                self.fetchData(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
            });

            // Carregar dados iniciais (últimos 30 dias)
            this.fetchData(
                moment().subtract(29, 'days').format('YYYY-MM-DD'),
                moment().format('YYYY-MM-DD')
            );
        },

        async fetchData(startDate, endDate) {
            this.loading = true;

            try {
                const response = await fetch(
                    `{{ route('admin.financeiro.data') }}?start_date=${startDate}&end_date=${endDate}`,
                    {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        }
                    }
                );

                const data = await response.json();

                if (data.financeCards) this.cards = data.financeCards;
                if (data.extratoSaidas) this.saidas = data.extratoSaidas;
                if (data.extratoEntradas) this.entradas = data.extratoEntradas;
            } catch (err) {
                console.error('Erro ao carregar dados financeiros:', err);
            } finally {
                this.loading = false;
            }
        }
    };
}

function renovacaoVps() {
    const vpsData = @json($vpsData);

    return {
        loading: false,
        successMsg: '',
        errorMsg: '',
        form: {
            vps_id: '',
            valor: '',
            valor_display: '',
            data_vencimento: new Date().toISOString().split('T')[0],
            status: 'pago',
        },

        init() {
            const self = this;
            const vpsWrapper = this.$el.querySelector('input[name="vps_id"]')?.closest('[data-ui-select]');
            const statusWrapper = this.$el.querySelector('input[name="status_renovacao"]')?.closest('[data-ui-select]');

            if (vpsWrapper) {
                vpsWrapper.addEventListener('click', (e) => {
                    const opt = e.target.closest('[data-ui-select-option]');
                    if (opt) {
                        this.$nextTick(() => {
                            const val = vpsWrapper.querySelector('[data-ui-select-value]').value;
                            if (val !== self.form.vps_id) {
                                self.form.vps_id = val;
                                self.onVpsChange(val);
                            }
                        });
                    }
                });
            }

            if (statusWrapper) {
                statusWrapper.addEventListener('click', (e) => {
                    const opt = e.target.closest('[data-ui-select-option]');
                    if (opt) {
                        this.$nextTick(() => {
                            self.form.status = statusWrapper.querySelector('[data-ui-select-value]').value || 'pago';
                        });
                    }
                });
            }
        },

        onVpsChange(vpsId) {
            if (vpsId && vpsData[vpsId]) {
                const val = parseFloat(vpsData[vpsId].valor);
                if (val > 0) {
                    this.form.valor = val.toFixed(2);
                    this.form.valor_display = new Intl.NumberFormat('pt-BR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).format(val);
                }
            }
        },

        maskValor(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value === '') {
                this.form.valor = '';
                this.form.valor_display = '';
                return;
            }
            const floatValue = (parseInt(value) / 100).toFixed(2);
            this.form.valor = floatValue;
            this.form.valor_display = new Intl.NumberFormat('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(floatValue);
        },

        async submit() {
            this.loading = true;
            this.successMsg = '';
            this.errorMsg = '';

            try {
                const response = await fetch('{{ route("admin.despesa.renovacao") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        vps_id: this.form.vps_id,
                        valor: this.form.valor,
                        data_vencimento: this.form.data_vencimento,
                        status: this.form.status,
                    }),
                });

                const data = await response.json();

                if (data.success) {
                    this.successMsg = data.message;
                    this.form.vps_id = '';
                    this.form.valor = '';
                    this.form.valor_display = '';
                    this.form.data_vencimento = new Date().toISOString().split('T')[0];
                    this.form.status = 'pago';

                    const vpsSelect = this.$el.querySelector('input[name="vps_id"]');
                    const statusSelect = this.$el.querySelector('input[name="status_renovacao"]');
                    if (vpsSelect) {
                        vpsSelect.value = '';
                        const vpsLabel = vpsSelect.closest('[data-ui-select]')?.querySelector('[data-ui-select-label]');
                        if (vpsLabel) { vpsLabel.textContent = 'Selecione uma VPS'; vpsLabel.className = 'text-slate-400'; }
                    }
                    if (statusSelect) {
                        statusSelect.value = 'pago';
                        const statusLabel = statusSelect.closest('[data-ui-select]')?.querySelector('[data-ui-select-label]');
                        if (statusLabel) { statusLabel.textContent = 'Pago'; statusLabel.className = 'text-slate-900'; }
                    }
                } else {
                    this.errorMsg = data.error || 'Erro ao lançar renovação.';
                }
            } catch (err) {
                this.errorMsg = 'Erro de conexão. Tente novamente.';
            } finally {
                this.loading = false;
            }
        }
    };
}
</script>

<style>
    /* DateRangePicker override para AlfaProxy */
    .daterangepicker {
        font-family: inherit;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 12px 40px rgba(15, 23, 42, 0.12);
        z-index: 9999;
    }
    .daterangepicker .ranges li {
        font-size: 12px;
        font-weight: 600;
        padding: 8px 16px;
        border-radius: 8px;
        color: #475569;
    }
    .daterangepicker .ranges li:hover {
        background: #f1f5f9;
        color: #0f172a;
    }
    .daterangepicker .ranges li.active {
        background: #23366f;
        color: #fff;
    }
    .daterangepicker td.active,
    .daterangepicker td.active:hover {
        background-color: #23366f;
        color: #fff;
        border-radius: 8px;
    }
    .daterangepicker td.in-range {
        background-color: #e8edf5;
        color: #23366f;
    }
    .daterangepicker td.start-date {
        border-radius: 8px 0 0 8px;
    }
    .daterangepicker td.end-date {
        border-radius: 0 8px 8px 0;
    }
    .daterangepicker .drp-buttons .btn {
        font-size: 12px;
        font-weight: 700;
        padding: 6px 16px;
        border-radius: 8px;
    }
    .daterangepicker .drp-buttons .btn-primary {
        background: #23366f;
        border-color: #23366f;
    }
    .daterangepicker .drp-buttons .btn-primary:hover {
        background: #1a2855;
    }
    .daterangepicker .calendar-table th,
    .daterangepicker .calendar-table td {
        font-size: 12px;
        min-width: 32px;
        height: 32px;
    }
    .daterangepicker select.monthselect,
    .daterangepicker select.yearselect {
        font-size: 12px;
        font-weight: 600;
    }
</style>
