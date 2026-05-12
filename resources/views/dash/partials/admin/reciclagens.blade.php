<div x-data="adminReciclagensPanel()" class="space-y-8">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-sm uppercase tracking-[0.35em] text-slate-500">Painel master</p>
            <h1 class="text-3xl font-bold text-slate-900">Reciclagem de Proxies</h1>
            <p class="text-slate-500">Acompanhe o ciclo das proxies bloqueadas que retornam ao estoque automaticamente.</p>
        </div>

        <div class="flex items-center gap-3">
            <button type="button" @click="fetchData()" class="btn-secondary text-xs px-3 py-2">
                <i class="fas fa-sync-alt" :class="loading && 'fa-spin'"></i> Atualizar
            </button>
        </div>
    </div>

    {{-- Cards de resumo --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="admin-card">
            <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Total</p>
            <p class="text-2xl font-bold text-slate-900 mt-2" x-text="stats.total"></p>
            <p class="text-sm text-slate-400 mt-1">No pipeline</p>
        </div>
        <div class="admin-card">
            <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Aguardando</p>
            <p class="text-2xl font-bold text-slate-900 mt-2" x-text="stats.aguardando"></p>
            <p class="text-sm text-slate-500 mt-1"><i class="fas fa-hourglass-half mr-1"></i> Em carencia</p>
        </div>
        <div class="admin-card">
            <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Aviso enviado</p>
            <p class="text-2xl font-bold text-slate-900 mt-2" x-text="stats.avisado"></p>
            <p class="text-sm text-amber-500 mt-1"><i class="fas fa-envelope mr-1"></i> Cliente notificado</p>
        </div>
        <div class="admin-card">
            <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Pronta</p>
            <p class="text-2xl font-bold text-slate-900 mt-2" x-text="stats.pronta"></p>
            <p class="text-sm text-red-500 mt-1"><i class="fas fa-bolt mr-1"></i> Na proxima fila</p>
        </div>
        <div class="admin-card">
            <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Recicladas</p>
            <p class="text-2xl font-bold text-slate-900 mt-2" x-text="stats.reciclada"></p>
            <p class="text-sm text-emerald-500 mt-1"><i class="fas fa-recycle mr-1"></i> De volta ao estoque</p>
        </div>
    </div>

    {{-- Tabela --}}
    <div class="admin-card">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
            <h2 class="text-xl font-semibold text-slate-900">Proxies no pipeline</h2>
            <div class="flex items-center gap-3">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input type="text" x-model="search" placeholder="Buscar por IP, usuario..."
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
                :class="filterStatus === 'aguardando' ? 'bg-[#23366f] text-white shadow-lg shadow-blue-900/20' : 'text-slate-500 hover:bg-white'"
                @click="filterStatus = 'aguardando'">Aguardando</button>
            <button type="button" class="px-5 py-2 rounded-xl font-bold text-xs transition-all"
                :class="filterStatus === 'avisado' ? 'bg-[#23366f] text-white shadow-lg shadow-blue-900/20' : 'text-slate-500 hover:bg-white'"
                @click="filterStatus = 'avisado'">Aviso enviado</button>
            <button type="button" class="px-5 py-2 rounded-xl font-bold text-xs transition-all"
                :class="filterStatus === 'pronta' ? 'bg-[#23366f] text-white shadow-lg shadow-blue-900/20' : 'text-slate-500 hover:bg-white'"
                @click="filterStatus = 'pronta'">Prontas</button>
            <button type="button" class="px-5 py-2 rounded-xl font-bold text-xs transition-all"
                :class="filterStatus === 'reciclada' ? 'bg-[#23366f] text-white shadow-lg shadow-blue-900/20' : 'text-slate-500 hover:bg-white'"
                @click="filterStatus = 'reciclada'">Recicladas</button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100">
                        <th class="text-left py-3 px-3 text-xs uppercase tracking-wider text-slate-400 font-semibold">ID</th>
                        <th class="text-left py-3 px-3 text-xs uppercase tracking-wider text-slate-400 font-semibold">Proxy</th>
                        <th class="text-left py-3 px-3 text-xs uppercase tracking-wider text-slate-400 font-semibold">Cliente</th>
                        <th class="text-left py-3 px-3 text-xs uppercase tracking-wider text-slate-400 font-semibold">Pais / VPS</th>
                        <th class="text-left py-3 px-3 text-xs uppercase tracking-wider text-slate-400 font-semibold">Expirou em</th>
                        <th class="text-left py-3 px-3 text-xs uppercase tracking-wider text-slate-400 font-semibold">Reciclagem prevista</th>
                        <th class="text-left py-3 px-3 text-xs uppercase tracking-wider text-slate-400 font-semibold">Aviso</th>
                        <th class="text-left py-3 px-3 text-xs uppercase tracking-wider text-slate-400 font-semibold">Reciclada em</th>
                        <th class="text-left py-3 px-3 text-xs uppercase tracking-wider text-slate-400 font-semibold">Status</th>
                        <th class="text-center py-3 px-3 text-xs uppercase tracking-wider text-slate-400 font-semibold">Acao</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="item in filteredItems" :key="item.id">
                        <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                            <td class="py-3 px-3">
                                <span class="text-xs font-mono text-slate-500" x-text="'#' + item.id"></span>
                            </td>
                            <td class="py-3 px-3">
                                <div class="flex flex-col">
                                    <code class="text-xs bg-slate-100 px-2 py-1 rounded-lg font-mono text-slate-700" x-text="item.endereco"></code>
                                    <span class="text-[11px] text-slate-400 mt-1 font-mono" x-text="item.usuario_proxy"></span>
                                </div>
                            </td>
                            <td class="py-3 px-3">
                                <template x-if="item.cliente_email">
                                    <div class="flex flex-col">
                                        <span class="font-semibold text-slate-900 text-xs" x-text="item.cliente_username || '-'"></span>
                                        <span class="text-[11px] text-slate-400" x-text="item.cliente_email"></span>
                                    </div>
                                </template>
                                <template x-if="!item.cliente_email">
                                    <span class="text-xs text-slate-400 italic">- sem cliente -</span>
                                </template>
                            </td>
                            <td class="py-3 px-3">
                                <div class="flex flex-col">
                                    <span class="text-xs font-medium text-slate-700" x-text="item.pais"></span>
                                    <span class="text-[11px] text-slate-400" x-text="item.vps"></span>
                                </div>
                            </td>
                            <td class="py-3 px-3">
                                <template x-if="item.expiracao">
                                    <div class="flex flex-col">
                                        <span class="text-xs text-slate-700" x-text="item.expiracao"></span>
                                        <span class="text-[11px] text-slate-400"
                                            x-text="item.expirou_ha_dias !== null && item.expirou_ha_dias > 0 ? ('ha ' + item.expirou_ha_dias + ' dias') : ''"></span>
                                    </div>
                                </template>
                                <template x-if="!item.expiracao">
                                    <span class="text-xs text-slate-400">-</span>
                                </template>
                            </td>
                            <td class="py-3 px-3">
                                <template x-if="item.reciclavel_em && item.status !== 'reciclada'">
                                    <div class="flex flex-col">
                                        <span class="text-xs text-slate-700" x-text="item.reciclavel_em"></span>
                                        <span class="text-[11px]"
                                            :class="item.horas_para_reciclar === 0 ? 'text-red-500 font-bold' : 'text-amber-600'"
                                            x-text="item.horas_para_reciclar === 0 ? 'agora' : ('em ' + item.horas_para_reciclar + 'h')"></span>
                                    </div>
                                </template>
                                <template x-if="item.status === 'reciclada' || !item.reciclavel_em">
                                    <span class="text-xs text-slate-400">-</span>
                                </template>
                            </td>
                            <td class="py-3 px-3">
                                <template x-if="item.aviso_enviado_em">
                                    <div class="flex items-center gap-1 text-xs text-emerald-700">
                                        <i class="fas fa-check-circle"></i>
                                        <span x-text="item.aviso_enviado_em"></span>
                                    </div>
                                </template>
                                <template x-if="!item.aviso_enviado_em">
                                    <span class="text-xs text-slate-400">-</span>
                                </template>
                            </td>
                            <td class="py-3 px-3">
                                <template x-if="item.reciclada_em">
                                    <div class="flex items-center gap-1 text-xs text-emerald-700 font-semibold">
                                        <i class="fas fa-recycle"></i>
                                        <span x-text="item.reciclada_em"></span>
                                    </div>
                                </template>
                                <template x-if="!item.reciclada_em">
                                    <span class="text-xs text-slate-400">-</span>
                                </template>
                            </td>
                            <td class="py-3 px-3">
                                <template x-if="item.status === 'aguardando'">
                                    <span class="inline-flex items-center gap-1 text-xs font-bold text-slate-700 bg-slate-100 px-2.5 py-1 rounded-lg">
                                        <i class="fas fa-hourglass-half"></i> Aguardando
                                    </span>
                                </template>
                                <template x-if="item.status === 'avisado'">
                                    <span class="inline-flex items-center gap-1 text-xs font-bold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-lg">
                                        <i class="fas fa-envelope"></i> Aviso enviado
                                    </span>
                                </template>
                                <template x-if="item.status === 'pronta'">
                                    <span class="inline-flex items-center gap-1 text-xs font-bold text-red-700 bg-red-50 px-2.5 py-1 rounded-lg">
                                        <i class="fas fa-bolt"></i> Pronta
                                    </span>
                                </template>
                                <template x-if="item.status === 'reciclada'">
                                    <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg">
                                        <i class="fas fa-recycle"></i> Reciclada
                                    </span>
                                </template>
                            </td>
                            <td class="py-3 px-3 text-center">
                                <template x-if="item.status !== 'reciclada'">
                                    <button type="button"
                                        @click="reciclarAgora(item.id)"
                                        :disabled="item.reciclando"
                                        class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all disabled:opacity-40 disabled:cursor-not-allowed bg-emerald-500 text-white hover:bg-emerald-600">
                                        <i class="fas" :class="item.reciclando ? 'fa-spinner fa-spin' : 'fa-recycle'"></i>
                                        <span x-text="item.reciclando ? 'Reciclando...' : 'Reciclar'"></span>
                                    </button>
                                </template>
                                <template x-if="item.status === 'reciclada'">
                                    <span class="text-xs text-slate-300">-</span>
                                </template>
                            </td>
                        </tr>
                    </template>

                    <template x-if="filteredItems.length === 0">
                        <tr>
                            <td colspan="10" class="py-12 text-center">
                                <div class="flex flex-col items-center gap-2 text-slate-400">
                                    <i class="fas fa-recycle text-4xl text-emerald-300"></i>
                                    <p class="font-semibold">Nenhuma proxy no pipeline</p>
                                    <p class="text-sm">Nao ha proxies em reciclagem no momento.</p>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Toast --}}
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
function adminReciclagensPanel() {
    return {
        @php
            $defaultStats = ['total' => 0, 'aguardando' => 0, 'avisado' => 0, 'pronta' => 0, 'reciclada' => 0];
        @endphp
        items: @json($reciclagensItems ?? []),
        stats: @json($reciclagensStats ?? $defaultStats),
        search: '',
        filterStatus: 'all',
        loading: false,
        toast: { show: false, message: '', type: 'success' },

        get filteredItems() {
            const term = this.search.toLowerCase();
            return this.items.filter(item => {
                const matchSearch = term === '' ||
                    (item.endereco || '').toLowerCase().includes(term) ||
                    (item.usuario_proxy || '').toLowerCase().includes(term) ||
                    (item.cliente_email || '').toLowerCase().includes(term) ||
                    (item.cliente_username || '').toLowerCase().includes(term);

                const matchStatus = this.filterStatus === 'all' || item.status === this.filterStatus;

                return matchSearch && matchStatus;
            });
        },

        showToast(message, type = 'success') {
            this.toast = { show: true, message, type };
            setTimeout(() => this.toast.show = false, 4000);
        },

        async fetchData() {
            this.loading = true;
            try {
                const res = await fetch('{{ route("admin.reciclagens.data") }}');
                const data = await res.json();
                this.items = data.items;
                this.stats = data.stats;
            } catch (e) {
                this.showToast('Erro ao carregar dados', 'error');
            }
            this.loading = false;
        },

        async reciclarAgora(stockId) {
            const item = this.items.find(i => i.id === stockId);
            if (!item || item.reciclando) return;

            if (!confirm(`Reciclar manualmente a proxy ${item.endereco}?\n\nA porta sera desbloqueada, a senha sera regenerada e a proxy volta ao estoque livre.`)) return;

            item.reciclando = true;
            try {
                const res = await fetch('{{ route("admin.reciclagens.forcar") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ stock_id: stockId }),
                });

                const data = await res.json();

                if (data.success) {
                    this.showToast(data.message || 'Proxy reciclada com sucesso!');
                    await this.fetchData();
                } else {
                    this.showToast(data.error || 'Erro ao reciclar', 'error');
                    item.reciclando = false;
                }
            } catch (e) {
                this.showToast('Erro de rede ao reciclar', 'error');
                item.reciclando = false;
            }
        },
    };
}
</script>
@endpush
