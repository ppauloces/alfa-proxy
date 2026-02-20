<div x-data="adminTransacoesPanel()" x-init="initDateRange()" class="space-y-8">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-sm uppercase tracking-[0.35em] text-slate-500">Painel de proxies vendidas</p>
            <h1 class="text-3xl font-bold text-slate-900">Vendas recentes & ações rápidas</h1>
            <p class="text-slate-500">Da última venda para a primeira, com acesso rápido para testar, bloquear portas ou
                repor proxies.</p>
        </div>

        {{-- DateRangePicker --}}
        <div class="relative">
            <div class="relative">
                <i class="fas fa-calendar-alt absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" id="transacoes-daterange" readonly
                    class="w-64 h-11 pl-10 pr-4 rounded-xl bg-white border border-slate-200 font-semibold text-sm text-slate-700 cursor-pointer hover:border-[#448ccb] focus:outline-none focus:ring-4 focus:ring-[#448ccb]/20 focus:border-[#23366f] transition">
            </div>
            <div x-show="loading" x-cloak class="absolute right-3 top-1/2 -translate-y-1/2">
                <i class="fas fa-spinner fa-spin text-[#448ccb] text-sm"></i>
            </div>
        </div>
    </div>

    {{-- Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <template x-for="(card, idx) in cards" :key="idx">
            <div class="admin-card">
                <p class="text-sm uppercase tracking-[0.35em] text-slate-400" x-text="card.label"></p>
                <p class="text-2xl font-bold text-slate-900 mt-2" x-text="card.value"></p>
                <p class="text-sm text-emerald-500 mt-1" x-text="card.chip"></p>
            </div>
        </template>
    </div>

    {{-- Tabela de vendas --}}
    <div class="admin-card">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
            <h2 class="text-xl font-semibold text-slate-900">Lista de vendas</h2>
            <div class="flex gap-2">
                <button type="button" onclick="window.location.reload()" class="btn-secondary text-xs px-3 py-2"><i
                        class="fas fa-sync-alt"></i> Atualizar</button>
                <button type="button" class="btn-primary text-xs px-3 py-2"><i class="fas fa-download"></i> Exportar
                    CSV</button>
            </div>
        </div>

        {{-- Filtros --}}
        <div class="flex items-center gap-2 bg-slate-50 p-1.5 rounded-2xl w-fit mb-5">
            <button type="button" class="px-5 py-2 rounded-xl font-bold text-xs transition-all"
                :class="filterStatus === 'all' ? 'bg-[#23366f] text-white shadow-lg shadow-blue-900/20' : 'text-slate-500 hover:bg-white'"
                @click="filterStatus = 'all'">Todas</button>
            <button type="button" class="px-5 py-2 rounded-xl font-bold text-xs transition-all"
                :class="filterStatus === 'ativa' ? 'bg-[#23366f] text-white shadow-lg shadow-blue-900/20' : 'text-slate-500 hover:bg-white'"
                @click="filterStatus = 'ativa'">Ativas</button>
            <button type="button" class="px-5 py-2 rounded-xl font-bold text-xs transition-all"
                :class="filterStatus === 'bloqueada' ? 'bg-[#23366f] text-white shadow-lg shadow-blue-900/20' : 'text-slate-500 hover:bg-white'"
                @click="filterStatus = 'bloqueada'">Bloqueadas</button>
            <button type="button" class="px-5 py-2 rounded-xl font-bold text-xs transition-all"
                :class="filterStatus === 'expirada' ? 'bg-[#23366f] text-white shadow-lg shadow-blue-900/20' : 'text-slate-500 hover:bg-white'"
                @click="filterStatus = 'expirada'">Expiradas</button>
            <button type="button" class="px-5 py-2 rounded-xl font-bold text-xs transition-all"
                :class="filterStatus === 'substituida' ? 'bg-[#23366f] text-white shadow-lg shadow-blue-900/20' : 'text-slate-500 hover:bg-white'"
                @click="filterStatus = 'substituida'">Substituídas</button>
        </div>

        <div class="overflow-x-auto">
            <table class="admin-table text-sm min-w-full">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Endereço</th>
                        <th>Comprador</th>
                        <th>Status</th>
                        <th>Período</th>
                        <th>Valor Unit.</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(proxy, idx) in filteredProxies" :key="proxy.id + '-' + idx">
                        <tr>
                            <td>
                                <p class="font-semibold text-slate-900 text-sm" x-text="proxy.data"></p>
                            </td>
                            <td>
                                <span
                                    class="font-mono text-xs font-bold text-slate-700 bg-slate-50 px-2 py-1 rounded-lg"
                                    x-text="proxy.endereco"></span>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div
                                        class="w-7 h-7 rounded-lg bg-slate-100 flex items-center justify-center shrink-0">
                                        <i class="fas fa-user text-[10px] text-slate-400"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900 text-sm" x-text="proxy.comprador"></p>
                                        <p class="text-[10px] text-slate-400" x-text="proxy.email"></p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <template x-if="proxy.status === 'substituida'">
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-orange-50 text-orange-600 text-[10px] font-black uppercase tracking-wider border border-orange-100">
                                        <i class="fas fa-exchange-alt text-[8px]"></i> Substituída
                                    </span>
                                </template>
                                <template x-if="proxy.status === 'bloqueada'">
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-red-50 text-red-600 text-[10px] font-black uppercase tracking-wider border border-red-100">
                                        <i class="fas fa-ban text-[8px]"></i> Bloqueada
                                    </span>
                                </template>
                                <template x-if="proxy.status === 'ativa' && parseInt(proxy.periodo) <= 0">
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-50 text-slate-500 text-[10px] font-black uppercase tracking-wider border border-slate-200">
                                        <i class="fas fa-hourglass-end text-[8px]"></i> Expirada
                                    </span>
                                </template>
                                <template x-if="proxy.status === 'ativa' && parseInt(proxy.periodo) > 0">
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-600 text-[10px] font-black uppercase tracking-wider border border-emerald-100">
                                        <i class="fas fa-check-circle text-[8px]"></i> Ativa
                                    </span>
                                </template>
                            </td>
                            <td>
                                <template x-if="parseInt(proxy.periodo) <= 0">
                                    <span class="text-sm font-bold text-red-500">Expirado</span>
                                </template>
                                <template x-if="parseInt(proxy.periodo) > 0 && parseInt(proxy.periodo) <= 3">
                                    <span class="text-sm font-bold text-amber-600"
                                        x-text="parseInt(proxy.periodo) + 'd restantes'"></span>
                                </template>
                                <template x-if="parseInt(proxy.periodo) > 3">
                                    <span class="text-sm font-bold text-slate-700"
                                        x-text="parseInt(proxy.periodo) + 'd restantes'"></span>
                                </template>
                            </td>
                            <td>
                                <template x-if="proxy.valor_unitario">
                                    <span class="text-sm font-bold text-slate-900"
                                        x-text="'R$ ' + parseFloat(proxy.valor_unitario).toLocaleString('pt-BR', {minimumFractionDigits: 2})"></span>
                                </template>
                                <template x-if="!proxy.valor_unitario">
                                    <span class="text-xs text-slate-400">&mdash;</span>
                                </template>
                            </td>
                            <td class="text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    {{-- Olho: detalhes --}}
                                    <button type="button" @click="openDetail(proxy)"
                                        class="w-8 h-8 rounded-lg bg-slate-50 hover:bg-[#23366f] text-slate-400 hover:text-white transition-all inline-flex items-center justify-center"
                                        title="Ver detalhes">
                                        <i class="fas fa-eye text-xs"></i>
                                    </button>
                                    {{-- Testar --}}
                                    <button type="button"
                                        class="w-8 h-8 rounded-lg bg-slate-50 hover:bg-emerald-600 text-slate-400 hover:text-white transition-all inline-flex items-center justify-center"
                                        data-action="test-proxy" :data-ip="proxy.ip" :data-porta="proxy.porta"
                                        :data-usuario="proxy.usuario" :data-senha="proxy.senha" title="Testar proxy">
                                        <i class="fas fa-vial text-xs"></i>
                                    </button>
                                    {{-- Bloquear/Desbloquear --}}
                                    <button type="button"
                                        class="w-8 h-8 rounded-lg bg-slate-50 text-slate-400 transition-all inline-flex items-center justify-center"
                                        :class="proxy.status === 'bloqueada' ? 'hover:bg-emerald-600 hover:text-white' : 'hover:bg-red-500 hover:text-white'"
                                        data-toggle-port :data-stock-id="proxy.stock_id"
                                        :data-state="proxy.status === 'bloqueada' ? 'blocked' : 'open'"
                                        :data-expirada="parseInt(proxy.periodo) <= 0 ? 'true' : 'false'"
                                        :title="proxy.status === 'bloqueada' ? 'Desbloquear' : 'Bloquear'">
                                        <i class="fas text-xs"
                                            :class="proxy.status === 'bloqueada' ? 'fa-lock-open' : 'fa-ban'"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <template x-if="proxies.length === 0 && !loading">
            <div class="py-16 text-center">
                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-box-open text-2xl text-slate-200"></i>
                </div>
                <h3 class="text-lg font-black text-slate-900 mb-1">Nenhuma venda no período</h3>
                <p class="text-slate-400 text-sm">Ajuste o período no filtro de datas acima.</p>
            </div>
        </template>
    </div>

    {{-- ========================================== --}}
    {{-- MODAL DE DETALHES DA VENDA --}}
    {{-- ========================================== --}}
    <div x-show="showModal" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center p-4"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" @click="showModal = false"></div>

        {{-- Modal --}}
        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto"
            x-show="showModal" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95 translate-y-4" @click.away="showModal = false">

            {{-- Header --}}
            <div class="px-6 pt-6 pb-4 border-b border-slate-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black text-[#448ccb] uppercase tracking-[0.3em]">Detalhes da venda
                        </p>
                        <h3 class="text-xl font-black text-slate-900 mt-1 font-mono" x-text="sel?.endereco"></h3>
                    </div>
                    <button @click="showModal = false"
                        class="w-9 h-9 rounded-xl bg-slate-50 hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-600 transition-colors">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>

                {{-- Status badge --}}
                <div class="mt-3 flex items-center gap-2">
                    <template x-if="sel?.status === 'ativa' && parseInt(sel?.periodo) > 0">
                        <div
                            class="inline-flex items-center gap-2 px-3 py-1.5 bg-emerald-50 border border-emerald-200 rounded-xl">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-[10px] font-black text-emerald-700 uppercase tracking-wider">Ativa</span>
                        </div>
                    </template>
                    <template x-if="sel?.status === 'bloqueada'">
                        <div
                            class="inline-flex items-center gap-2 px-3 py-1.5 bg-red-50 border border-red-200 rounded-xl">
                            <span class="w-2 h-2 rounded-full bg-red-500"></span>
                            <span class="text-[10px] font-black text-red-700 uppercase tracking-wider">Bloqueada</span>
                        </div>
                    </template>
                    <template x-if="sel?.status === 'substituida'">
                        <div
                            class="inline-flex items-center gap-2 px-3 py-1.5 bg-orange-50 border border-orange-200 rounded-xl">
                            <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                            <span
                                class="text-[10px] font-black text-orange-700 uppercase tracking-wider">Substituída</span>
                        </div>
                    </template>
                    <template x-if="sel?.status === 'ativa' && parseInt(sel?.periodo) <= 0">
                        <div
                            class="inline-flex items-center gap-2 px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl">
                            <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                            <span class="text-[10px] font-black text-slate-600 uppercase tracking-wider">Expirada</span>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Dados do Proxy --}}
            <div class="px-6 py-4">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">
                    <i class="fas fa-server mr-1"></i> Dados do Proxy
                </p>
                <div class="space-y-0">
                    <div class="flex justify-between items-center py-2.5 border-b border-slate-50">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">IP</span>
                        <span class="text-sm font-mono font-bold text-slate-900" x-text="sel?.ip"></span>
                    </div>
                    <div class="flex justify-between items-center py-2.5 border-b border-slate-50">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Porta</span>
                        <span class="text-sm font-mono font-bold text-slate-900" x-text="sel?.porta"></span>
                    </div>
                    <div class="flex justify-between items-center py-2.5 border-b border-slate-50">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Usuário</span>
                        <span class="text-sm font-mono font-bold text-slate-900" x-text="sel?.usuario"></span>
                    </div>
                    <div class="flex justify-between items-center py-2.5 border-b border-slate-50">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Senha</span>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-mono font-bold text-slate-900"
                                x-text="showPass ? sel?.senha : '••••••••'"></span>
                            <button type="button" @click="showPass = !showPass"
                                class="text-slate-400 hover:text-slate-600 transition-colors">
                                <i class="fas text-xs" :class="showPass ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>
                    <div class="flex justify-between items-center py-2.5 border-b border-slate-50">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Período
                            restante</span>
                        <span class="text-sm font-bold"
                            :class="parseInt(sel?.periodo) <= 0 ? 'text-red-500' : parseInt(sel?.periodo) <= 3 ? 'text-amber-600' : 'text-slate-900'"
                            x-text="parseInt(sel?.periodo) > 0 ? parseInt(sel?.periodo) + ' dias' : 'Expirado'"></span>
                    </div>
                    <div class="flex justify-between items-center py-2.5">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Data da venda</span>
                        <span class="text-sm font-bold text-slate-900" x-text="sel?.data"></span>
                    </div>
                </div>
            </div>

            {{-- Dados do Comprador --}}
            <div class="px-6 py-4 border-t border-slate-100">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">
                    <i class="fas fa-user mr-1"></i> Comprador
                </p>
                <div class="space-y-0">
                    <div class="flex justify-between items-center py-2.5 border-b border-slate-50">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Username</span>
                        <span class="text-sm font-bold text-slate-900" x-text="sel?.comprador"></span>
                    </div>
                    <div class="flex justify-between items-center py-2.5 border-b border-slate-50">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">E-mail</span>
                        <span class="text-sm font-bold text-slate-700" x-text="sel?.email"></span>
                    </div>
                    <div class="flex justify-between items-center py-2.5 border-b border-slate-50">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total gasto</span>
                        <span class="text-sm font-bold text-emerald-600" x-text="sel?.gasto_cliente"></span>
                    </div>
                    <div class="flex justify-between items-center py-2.5">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Proxies
                            compradas</span>
                        <span class="text-sm font-bold text-slate-900" x-text="sel?.pedidos"></span>
                    </div>
                </div>
            </div>

            {{-- Dados da Compra --}}
            <div class="px-6 py-4 border-t border-slate-100"
                x-show="sel?.valor_unitario || sel?.periodo_comprado || sel?.motivo">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">
                    <i class="fas fa-cart-shopping mr-1"></i> Dados da Compra
                </p>
                <div class="space-y-0">
                    <div class="flex justify-between items-center py-2.5 border-b border-slate-50"
                        x-show="sel?.valor_unitario">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Valor
                            unitário</span>
                        <span class="text-sm font-bold text-slate-900"
                            x-text="'R$ ' + parseFloat(sel?.valor_unitario || 0).toLocaleString('pt-BR', {minimumFractionDigits: 2})"></span>
                    </div>
                    <div class="flex justify-between items-center py-2.5 border-b border-slate-50"
                        x-show="sel?.periodo_comprado">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Período
                            contratado</span>
                        <span class="text-sm font-bold text-slate-900" x-text="sel?.periodo_comprado + ' dias'"></span>
                    </div>
                    <div class="flex justify-between items-start py-2.5" x-show="sel?.motivo">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Motivo</span>
                        <span class="text-sm font-bold text-slate-700 max-w-[200px] text-right"
                            x-text="sel?.motivo"></span>
                    </div>
                </div>
            </div>

            {{-- Ações rápidas --}}
            <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <button type="button"
                        class="px-4 py-2.5 rounded-xl bg-slate-50 hover:bg-emerald-50 text-sm font-bold text-slate-600 hover:text-emerald-700 transition-colors flex items-center gap-2"
                        data-action="test-proxy" :data-ip="sel?.ip" :data-porta="sel?.porta"
                        :data-usuario="sel?.usuario" :data-senha="sel?.senha">
                        <i class="fas fa-vial text-xs"></i> Testar
                    </button>
                    <button type="button"
                        class="px-4 py-2.5 rounded-xl text-sm font-bold transition-colors flex items-center gap-2"
                        :class="sel?.status === 'bloqueada' ? 'bg-slate-50 hover:bg-emerald-50 text-slate-600 hover:text-emerald-700' : 'bg-slate-50 hover:bg-red-50 text-slate-600 hover:text-red-600'"
                        data-toggle-port :data-stock-id="sel?.stock_id"
                        :data-state="sel?.status === 'bloqueada' ? 'blocked' : 'open'"
                        :data-expirada="parseInt(sel?.periodo) <= 0 ? 'true' : 'false'">
                        <i class="fas text-xs" :class="sel?.status === 'bloqueada' ? 'fa-lock-open' : 'fa-ban'"></i>
                        <span x-text="sel?.status === 'bloqueada' ? 'Desbloquear' : 'Bloquear'"></span>
                    </button>
                </div>
                <button @click="showModal = false"
                    class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-sm font-bold text-slate-600 transition-colors">
                    Fechar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Container de Notificações Toast -->
<div id="toastContainer" class="fixed top-20 right-4 space-y-3" style="max-width: 400px; z-index: 9999;">
    <!-- Toasts serão injetados aqui -->
</div>

<script>
    function adminTransacoesPanel() {
        return {
            loading: false,
            filterStatus: 'all',
            showModal: false,
            sel: null,
            showPass: false,
            cards: @json($soldProxyCards),
            proxies: @json($soldProxies),

            get filteredProxies() {
                if (this.filterStatus === 'all') return this.proxies;
                return this.proxies.filter(p => {
                    if (this.filterStatus === 'ativa') return p.status === 'ativa' && parseInt(p.periodo) > 0;
                    if (this.filterStatus === 'bloqueada') return p.status === 'bloqueada';
                    if (this.filterStatus === 'substituida') return p.status === 'substituida';
                    if (this.filterStatus === 'expirada') return p.status === 'ativa' && parseInt(p.periodo) <= 0;
                    return true;
                });
            },

            openDetail(proxy) {
                this.sel = proxy;
                this.showPass = false;
                this.showModal = true;
            },

            initDateRange() {
                const self = this;
                moment.locale('pt-br');

                $('#transacoes-daterange').daterangepicker({
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
                }, function (start, end) {
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
                        `{{ route('admin.transacoes.data') }}?start_date=${startDate}&end_date=${endDate}`,
                        {
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            }
                        }
                    );

                    const data = await response.json();

                    if (data.soldProxyCards) this.cards = data.soldProxyCards;
                    if (data.soldProxies) this.proxies = data.soldProxies;
                } catch (err) {
                    console.error('Erro ao carregar dados de transações:', err);
                } finally {
                    this.loading = false;
                }
            }
        };
    }

    // Prevenir execução duplicada do script
    if (!window.transacoesScriptLoaded) {
        window.transacoesScriptLoaded = true;

        // ============================================
        // SISTEMA DE TOAST DE NOTIFICAÇÕES
        // ============================================
        if (typeof window.showToast === 'undefined') {
            window.showToast = function (message, type = 'success') {
                const toast = document.createElement('div');
                toast.className = 'transform transition-all duration-300 translate-x-full';

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

                if (toastContainer.parentNode !== document.body) {
                    document.body.appendChild(toastContainer);
                }

                toastContainer.appendChild(toast);

                setTimeout(() => {
                    toast.className = 'transform transition-all duration-300 translate-x-0';
                }, 10);

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
            window.getIpGeolocation = async function (ip) {
                try {
                    const response = await fetch(`/admin/ip-geolocation?ip=${encodeURIComponent(ip)}`);
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

            e.preventDefault();

            const stockId = toggleButton.dataset.stockId;
            const currentState = toggleButton.dataset.state;
            const icon = toggleButton.querySelector('i');
            const btnText = toggleButton.querySelector('span');

            const action = (currentState === 'blocked') ? 'desbloquear' : 'bloquear';
            const endpoint = (action === 'bloquear') ? '/admin/proxy/bloquear' : '/admin/proxy/desbloquear';
            const isExpirada = toggleButton.dataset.expirada === 'true';

            // Se proxy expirada, pedir nova data ANTES de qualquer fetch
            let novaExpiracao = null;
            if (action === 'desbloquear' && isExpirada) {
                const { value: dataEscolhida } = await Swal.fire({
                    title: 'Proxy Expirada!',
                    html: `
                    <p style="margin-bottom:12px">Defina uma nova data de expiração para desbloquear.</p>
                    <input type="date" id="novaExpiracaoTx" class="swal2-input" min="${new Date().toISOString().split('T')[0]}" style="width:80%">
                `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Desbloquear',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#3b82f6',
                    didOpen: () => {
                        document.querySelector('.swal2-container').style.zIndex = '99999';
                    },
                    preConfirm: () => {
                        const data = document.getElementById('novaExpiracaoTx').value;
                        if (!data) {
                            Swal.showValidationMessage('Por favor, selecione uma data');
                            return false;
                        }
                        const escolhida = new Date(data);
                        const hoje = new Date();
                        hoje.setHours(0, 0, 0, 0);
                        if (escolhida <= hoje) {
                            Swal.showValidationMessage('A data deve ser futura');
                            return false;
                        }
                        return data;
                    }
                });

                if (!dataEscolhida) return;
                novaExpiracao = dataEscolhida;
            }

            // Desabilitar botão durante requisição
            toggleButton.disabled = true;
            if (icon) icon.className = 'fas fa-spinner fa-spin text-xs';
            if (btnText) btnText.textContent = 'Aguarde...';

            try {
                const requestBody = { stock_id: stockId };
                if (novaExpiracao) requestBody.nova_expiracao = novaExpiracao;

                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify(requestBody)
                });

                const data = await response.json();

                if (data.success) {
                    const successMsg = novaExpiracao
                        ? `Proxy desbloqueada e data atualizada para ${new Date(novaExpiracao).toLocaleDateString('pt-BR')}!`
                        : (data.message || `Porta ${action === 'bloquear' ? 'bloqueada' : 'desbloqueada'} com sucesso!`);
                    showToast(successMsg, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast(data.error || 'Erro ao processar requisição', 'error');
                    toggleButton.disabled = false;
                    if (icon) icon.className = `fas ${currentState === 'blocked' ? 'fa-lock-open' : 'fa-ban'} text-xs`;
                    if (btnText) btnText.textContent = currentState === 'blocked' ? 'Desbloquear' : 'Bloquear';
                }
            } catch (error) {
                console.error('Erro:', error);
                showToast('Erro ao conectar com o servidor', 'error');
                toggleButton.disabled = false;
                if (icon) icon.className = `fas ${currentState === 'blocked' ? 'fa-lock-open' : 'fa-ban'} text-xs`;
                if (btnText) btnText.textContent = currentState === 'blocked' ? 'Desbloquear' : 'Bloquear';
            }
        });
    }
</script>

<style>
    /* Reutiliza o mesmo override do daterangepicker do financeiro */
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