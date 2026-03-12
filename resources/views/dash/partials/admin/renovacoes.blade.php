<div x-data="adminRenovacoesPanel()" class="space-y-8">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-sm uppercase tracking-[0.35em] text-slate-500">Painel master</p>
            <h1 class="text-3xl font-bold text-slate-900">Renovações Automáticas</h1>
            <p class="text-slate-500">Gerencie cobranças de renovação de proxies via cartão de crédito.</p>
        </div>

        <div class="flex items-center gap-3">
            <button type="button" @click="fetchData()" class="btn-secondary text-xs px-3 py-2">
                <i class="fas fa-sync-alt" :class="loading && 'fa-spin'"></i> Atualizar
            </button>
            <button type="button"
                @click="cobrarLote()"
                :disabled="selectedIds.length === 0 || cobrandoLote"
                class="px-5 py-2.5 rounded-xl text-xs font-bold transition-all disabled:opacity-40 disabled:cursor-not-allowed"
                :class="selectedIds.length > 0 ? 'bg-[#23366f] text-white hover:bg-[#1a2a5c] shadow-lg shadow-blue-900/20' : 'bg-slate-200 text-slate-400'">
                <i class="fas fa-credit-card mr-1"></i>
                Cobrar Selecionados
                <span x-show="selectedIds.length > 0" x-text="'(' + selectedIds.length + ')'"></span>
            </button>
        </div>
    </div>

    {{-- Cards de resumo --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="admin-card">
            <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Pendentes</p>
            <p class="text-2xl font-bold text-slate-900 mt-2" x-text="stats.pendentes"></p>
            <p class="text-sm text-amber-500 mt-1"><i class="fas fa-clock mr-1"></i> Aguardando cobrança</p>
        </div>
        <div class="admin-card">
            <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Valor Total Pendente</p>
            <p class="text-2xl font-bold text-slate-900 mt-2" x-text="stats.valorPendente"></p>
            <p class="text-sm text-slate-400 mt-1">Soma das renovações</p>
        </div>
        <div class="admin-card">
            <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Cobradas Hoje</p>
            <p class="text-2xl font-bold text-slate-900 mt-2" x-text="stats.cobradasHoje"></p>
            <p class="text-sm text-emerald-500 mt-1"><i class="fas fa-check-circle mr-1"></i> Renovadas</p>
        </div>
        <div class="admin-card">
            <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Falhas Hoje</p>
            <p class="text-2xl font-bold text-slate-900 mt-2" x-text="stats.falhasHoje"></p>
            <p class="text-sm text-red-500 mt-1"><i class="fas fa-exclamation-circle mr-1"></i> Com erro</p>
        </div>
    </div>

    {{-- Tabela --}}
    <div class="admin-card">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
            <h2 class="text-xl font-semibold text-slate-900">Proxies para Renovação</h2>
            <div class="flex items-center gap-3">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input type="text" x-model="search" placeholder="Buscar por IP, usuário..."
                        class="pl-10 pr-4 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-4 focus:ring-[#448ccb]/20 focus:border-[#23366f] w-64">
                </div>
            </div>
        </div>

        {{-- Filtros --}}
        <div class="flex flex-wrap items-center gap-2 bg-slate-50 p-1.5 rounded-2xl w-fit mb-5">
            <button type="button" class="px-5 py-2 rounded-xl font-bold text-xs transition-all"
                :class="filterStatus === 'all' ? 'bg-[#23366f] text-white shadow-lg shadow-blue-900/20' : 'text-slate-500 hover:bg-white'"
                @click="filterStatus = 'all'">Todas</button>
            <button type="button" class="px-5 py-2 rounded-xl font-bold text-xs transition-all"
                :class="filterStatus === 'pendente' ? 'bg-[#23366f] text-white shadow-lg shadow-blue-900/20' : 'text-slate-500 hover:bg-white'"
                @click="filterStatus = 'pendente'">Pendentes</button>
            <button type="button" class="px-5 py-2 rounded-xl font-bold text-xs transition-all"
                :class="filterStatus === 'sem_cartao' ? 'bg-[#23366f] text-white shadow-lg shadow-blue-900/20' : 'text-slate-500 hover:bg-white'"
                @click="filterStatus = 'sem_cartao'">Sem Cartão</button>
            <button type="button" class="px-5 py-2 rounded-xl font-bold text-xs transition-all"
                :class="filterStatus === 'falha' ? 'bg-[#23366f] text-white shadow-lg shadow-blue-900/20' : 'text-slate-500 hover:bg-white'"
                @click="filterStatus = 'falha'">Com Falha</button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100">
                        <th class="text-left py-3 px-3 text-xs uppercase tracking-wider text-slate-400 font-semibold w-10">
                            <input type="checkbox" @change="toggleSelectAll($event)" :checked="allSelected"
                                class="rounded border-slate-300 text-[#23366f] focus:ring-[#448ccb]">
                        </th>
                        <th class="text-left py-3 px-3 text-xs uppercase tracking-wider text-slate-400 font-semibold">Usuário</th>
                        <th class="text-left py-3 px-3 text-xs uppercase tracking-wider text-slate-400 font-semibold">Proxy</th>
                        <th class="text-left py-3 px-3 text-xs uppercase tracking-wider text-slate-400 font-semibold">País</th>
                        <th class="text-left py-3 px-3 text-xs uppercase tracking-wider text-slate-400 font-semibold">Expiração</th>
                        <th class="text-left py-3 px-3 text-xs uppercase tracking-wider text-slate-400 font-semibold">Período</th>
                        <th class="text-left py-3 px-3 text-xs uppercase tracking-wider text-slate-400 font-semibold">Valor</th>
                        <th class="text-left py-3 px-3 text-xs uppercase tracking-wider text-slate-400 font-semibold">Cartão</th>
                        <th class="text-left py-3 px-3 text-xs uppercase tracking-wider text-slate-400 font-semibold">Status</th>
                        <th class="text-center py-3 px-3 text-xs uppercase tracking-wider text-slate-400 font-semibold">Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="item in filteredItems" :key="item.proxy_id">
                        <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                            <td class="py-3 px-3">
                                <input type="checkbox" :value="item.proxy_id"
                                    x-model.number="selectedIds"
                                    :disabled="!item.has_card"
                                    class="rounded border-slate-300 text-[#23366f] focus:ring-[#448ccb] disabled:opacity-30">
                            </td>
                            <td class="py-3 px-3">
                                <div class="flex flex-col">
                                    <span class="font-semibold text-slate-900" x-text="item.username"></span>
                                    <span class="text-xs text-slate-400" x-text="item.email"></span>
                                </div>
                            </td>
                            <td class="py-3 px-3">
                                <code class="text-xs bg-slate-100 px-2 py-1 rounded-lg font-mono" x-text="item.endereco"></code>
                            </td>
                            <td class="py-3 px-3">
                                <span class="text-xs font-medium" x-text="item.pais"></span>
                            </td>
                            <td class="py-3 px-3">
                                <span class="text-xs" :class="item.expirado ? 'text-red-600 font-bold' : 'text-amber-600 font-medium'" x-text="item.expiracao"></span>
                            </td>
                            <td class="py-3 px-3">
                                <span class="text-xs font-medium text-slate-600" x-text="item.periodo + ' dias'"></span>
                            </td>
                            <td class="py-3 px-3">
                                <span class="font-bold text-slate-900" x-text="'R$ ' + item.valor.toFixed(2).replace('.', ',')"></span>
                            </td>
                            <td class="py-3 px-3">
                                <template x-if="item.has_card">
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-700 bg-emerald-50 px-2 py-1 rounded-lg">
                                        <i class="fas fa-credit-card"></i>
                                        <span x-text="item.card_info"></span>
                                    </span>
                                </template>
                                <template x-if="!item.has_card">
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-red-600 bg-red-50 px-2 py-1 rounded-lg">
                                        <i class="fas fa-times-circle"></i> Sem cartão
                                    </span>
                                </template>
                            </td>
                            <td class="py-3 px-3">
                                <template x-if="item.status === 'pendente'">
                                    <span class="inline-flex items-center gap-1 text-xs font-bold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-lg">
                                        <i class="fas fa-clock"></i> Pendente
                                    </span>
                                </template>
                                <template x-if="item.status === 'falha'">
                                    <span class="inline-flex items-center gap-1 text-xs font-bold text-red-700 bg-red-50 px-2.5 py-1 rounded-lg">
                                        <i class="fas fa-exclamation-triangle"></i> Falha
                                    </span>
                                </template>
                                <template x-if="item.status === 'sem_cartao'">
                                    <span class="inline-flex items-center gap-1 text-xs font-bold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-lg">
                                        <i class="fas fa-ban"></i> Sem cartão
                                    </span>
                                </template>
                            </td>
                            <td class="py-3 px-3 text-center">
                                <button type="button"
                                    @click="cobrarIndividual(item.proxy_id)"
                                    :disabled="!item.has_card || item.cobrando"
                                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all disabled:opacity-40 disabled:cursor-not-allowed"
                                    :class="item.has_card ? 'bg-emerald-500 text-white hover:bg-emerald-600' : 'bg-slate-200 text-slate-400'">
                                    <i class="fas" :class="item.cobrando ? 'fa-spinner fa-spin' : 'fa-bolt'"></i>
                                    <span x-text="item.cobrando ? 'Cobrando...' : 'Cobrar'"></span>
                                </button>
                            </td>
                        </tr>
                    </template>

                    <template x-if="filteredItems.length === 0">
                        <tr>
                            <td colspan="10" class="py-12 text-center">
                                <div class="flex flex-col items-center gap-2 text-slate-400">
                                    <i class="fas fa-check-circle text-4xl text-emerald-300"></i>
                                    <p class="font-semibold">Nenhuma renovação pendente</p>
                                    <p class="text-sm">Todas as renovações estão em dia.</p>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Toast de feedback --}}
    <div x-show="toast.show" x-transition
        class="fixed bottom-6 right-6 z-50 px-6 py-4 rounded-2xl shadow-2xl text-white font-bold text-sm max-w-md"
        :class="toast.type === 'success' ? 'bg-emerald-500' : 'bg-red-500'"
        x-cloak>
        <div class="flex items-center gap-3">
            <i class="fas" :class="toast.type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'"></i>
            <span x-text="toast.message"></span>
        </div>
    </div>
</div>

@push('scripts')
<script>
function adminRenovacoesPanel() {
    return {
        @php
            $defaultStats = ['pendentes' => 0, 'valorPendente' => 'R$ 0,00', 'cobradasHoje' => 0, 'falhasHoje' => 0];
        @endphp
        items: @json($renovacoesPendentes ?? []),
        stats: @json($renovacoesStats ?? $defaultStats),
        selectedIds: [],
        search: '',
        filterStatus: 'all',
        loading: false,
        cobrandoLote: false,
        toast: { show: false, message: '', type: 'success' },

        get filteredItems() {
            return this.items.filter(item => {
                const matchSearch = this.search === '' ||
                    item.username.toLowerCase().includes(this.search.toLowerCase()) ||
                    item.email.toLowerCase().includes(this.search.toLowerCase()) ||
                    item.endereco.includes(this.search);

                const matchStatus = this.filterStatus === 'all' || item.status === this.filterStatus;

                return matchSearch && matchStatus;
            });
        },

        get allSelected() {
            const selectable = this.filteredItems.filter(i => i.has_card);
            return selectable.length > 0 && selectable.every(i => this.selectedIds.includes(i.proxy_id));
        },

        toggleSelectAll(e) {
            const selectable = this.filteredItems.filter(i => i.has_card).map(i => i.proxy_id);
            if (e.target.checked) {
                this.selectedIds = [...new Set([...this.selectedIds, ...selectable])];
            } else {
                this.selectedIds = this.selectedIds.filter(id => !selectable.includes(id));
            }
        },

        showToast(message, type = 'success') {
            this.toast = { show: true, message, type };
            setTimeout(() => this.toast.show = false, 4000);
        },

        async fetchData() {
            this.loading = true;
            try {
                const res = await fetch('{{ route("admin.renovacoes.data") }}');
                const data = await res.json();
                this.items = data.items;
                this.stats = data.stats;
                this.selectedIds = [];
            } catch (e) {
                this.showToast('Erro ao carregar dados', 'error');
            }
            this.loading = false;
        },

        async cobrarIndividual(proxyId) {
            const item = this.items.find(i => i.proxy_id === proxyId);
            if (!item || item.cobrando) return;

            if (!confirm(`Cobrar renovação do proxy ${item.endereco} (R$ ${item.valor.toFixed(2).replace('.', ',')})?`)) return;

            item.cobrando = true;
            try {
                const res = await fetch('{{ route("admin.renovacoes.cobrar") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ proxy_ids: [proxyId] }),
                });

                const data = await res.json();

                if (data.success) {
                    this.showToast(data.message || 'Cobrança realizada com sucesso!');
                    await this.fetchData();
                } else {
                    this.showToast(data.message || 'Erro na cobrança', 'error');
                    item.cobrando = false;
                }
            } catch (e) {
                this.showToast('Erro ao processar cobrança', 'error');
                item.cobrando = false;
            }
        },

        async cobrarLote() {
            if (this.selectedIds.length === 0) return;

            const total = this.items
                .filter(i => this.selectedIds.includes(i.proxy_id))
                .reduce((sum, i) => sum + i.valor, 0);

            if (!confirm(`Cobrar ${this.selectedIds.length} proxy(s) totalizando R$ ${total.toFixed(2).replace('.', ',')}? As cobranças serão agrupadas por usuário.`)) return;

            this.cobrandoLote = true;
            try {
                const res = await fetch('{{ route("admin.renovacoes.cobrar") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ proxy_ids: this.selectedIds }),
                });

                const data = await res.json();

                if (data.success) {
                    this.showToast(data.message || 'Cobranças realizadas com sucesso!');
                    await this.fetchData();
                } else {
                    this.showToast(data.message || 'Erro nas cobranças', 'error');
                }
            } catch (e) {
                this.showToast('Erro ao processar cobranças em lote', 'error');
            }
            this.cobrandoLote = false;
        },
    };
}
</script>
@endpush
