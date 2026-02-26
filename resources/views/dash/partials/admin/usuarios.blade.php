<div class="flex flex-col gap-2 mb-8">
    <p class="text-sm uppercase tracking-[0.35em] text-slate-500">Clientes & colaboradores</p>
    <h1 class="text-3xl font-bold text-slate-900">Quem compra e quem opera</h1>
    <p class="text-slate-500">Leads ativos, gasto acumulado e o time que mantém tudo em produção.</p>
</div>

<div class="">
    <div class="admin-card lg:col-span-2">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-4">
            <h2 class="text-xl font-semibold text-slate-900">Clientes</h2>
        </div>

        {{-- Toolbar: busca + limite por página --}}
        <form method="GET" action="{{ url()->current() }}" class="mb-4">
            <input type="hidden" name="section" value="admin-usuarios">
            <div class="flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
                <div class="flex-1">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <input
                            type="text"
                            name="users_q"
                            value="{{ request('users_q') }}"
                            placeholder="Buscar por nome ou e-mail..."
                            @input.debounce.400ms="$el.form.requestSubmit()"
                            class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-200 bg-white text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#23366f]/20"
                        />
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Mostrar</label>
                    <select
                        name="users_per_page"
                        @change="$el.form.requestSubmit()"
                        class="px-3 py-2 rounded-xl border border-slate-200 bg-white text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#23366f]/20"
                    >
                        @foreach([10, 25, 50, 100] as $pp)
                            <option value="{{ $pp }}" @selected((int) request('users_per_page', 10) === $pp)>{{ $pp }}</option>
                        @endforeach
                    </select>

                    <button type="submit" class="btn-secondary text-xs px-3 py-2">
                        Filtrar
                    </button>
                </div>
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="admin-table text-sm min-w-full">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>E-mail</th>
                        <th>Cargo</th>
                        <th>Saldo</th>
                        <th>Gasto Total</th>
                        <th>Proxies</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clientLeads as $user)
                        <tr>
                            <td class="font-semibold text-slate-900">{{ $user['name'] }}</td>
                            <td class="text-xs text-slate-500">{{ $user['email'] }}</td>
                            @php
                                $cargo = strtolower((string) ($user['cargo'] ?? ''));
                                $cargoLabel = $cargo !== '' ? ucfirst($cargo) : 'N/A';
                                $cargoBadgeClass = match ($cargo) {
                                    'usuario' => 'bg-slate-100 text-slate-700 ring-slate-200',
                                    'revendedor' => 'bg-emerald-100 text-emerald-700 ring-emerald-200',
                                    'admin' => 'bg-blue-100 text-blue-700 ring-blue-200',
                                    'super' => 'bg-indigo-100 text-indigo-700 ring-indigo-200',
                                    default => 'bg-slate-100 text-slate-600 ring-slate-200',
                                };
                            @endphp
                            <td class="text-xs">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 font-semibold ring-1 ring-inset {{ $cargoBadgeClass }}">
                                    {{ $cargoLabel }}
                                </span>
                            </td>
                            <td class="font-semibold text-slate-900">{{ $user['saldo'] ?? 'R$ 0,00' }}</td>
                            <td>
                                <span class="text-xs text-slate-500">R$ {{ number_format(data_get($statsCompraProxy, $user->id.'.gasto', 0), 2, ',', '.') }}</span>
                            </td>
                            <td class="font-semibold text-slate-900">{{ data_get($statsCompraProxy, $user->id.'.proxies', 0) }}</td>

                            <td class="relative">
                                <div class="flex items-center gap-1.5">
                                    {{-- Ver Detalhes --}}
                                    <button
                                        type="button"
                                        data-user-id="{{ $user->id }}"
                                        data-user-name="{{ $user['name'] }}"
                                        data-open-user-detail
                                        class="w-8 h-8 rounded-lg bg-slate-50 hover:bg-[#23366f] text-slate-400 hover:text-white transition-all inline-flex items-center justify-center"
                                        title="Ver detalhes"
                                    >
                                        <i class="fas fa-eye text-xs"></i>
                                    </button>

                                    @if(Auth::user()->isSuperAdmin())
                                    <div x-data="{
                                            open: false,
                                            top: 0,
                                            left: 0,
                                            toggle() {
                                            if (this.open) { this.open = false; return }
                                            const r = this.$refs.btn.getBoundingClientRect()
                                            const w = 192
                                            this.top = r.bottom + 8
                                            this.left = r.right - w
                                            this.open = true
                                            }
                                        }">
                                        <button x-ref="btn" @click="toggle()" type="button"
                                            class="text-slate-400 hover:text-slate-600 transition-colors p-2">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>

                                        <div x-show="open" @click.outside="open = false"
                                            class="fixed w-48 bg-white rounded-lg shadow-lg border border-slate-200 py-1 z-[9999]"
                                            :style="`top:${top}px;left:${left}px;`" style="display:none;">

                                            <p class="px-4 py-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Alterar cargo</p>

                                            @if($cargo !== 'usuario')
                                            <form method="POST" action="/admin/usuarios/{{ $user['id'] }}/cargo" class="m-0">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="cargo" value="usuario">
                                                <button type="submit"
                                                    class="w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 transition-colors flex items-center">
                                                    <i class="fas fa-user mr-2 text-slate-400"></i>
                                                    Usuário comum
                                                </button>
                                            </form>
                                            @endif

                                            @if($cargo !== 'revendedor')
                                            <form method="POST" action="/admin/usuarios/{{ $user['id'] }}/cargo" class="m-0">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="cargo" value="revendedor">
                                                <button type="submit"
                                                    class="w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 transition-colors flex items-center">
                                                    <i class="fas fa-store mr-2 text-emerald-400"></i>
                                                    Revendedor
                                                </button>
                                            </form>
                                            @endif

                                            @if($cargo !== 'admin')
                                            <form method="POST" action="/admin/usuarios/{{ $user['id'] }}/cargo" class="m-0">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="cargo" value="admin">
                                                <button type="submit"
                                                    class="w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 transition-colors flex items-center">
                                                    <i class="fas fa-user-shield mr-2 text-blue-400"></i>
                                                    Admin
                                                </button>
                                            </form>
                                            @endif

                                            @if($cargo !== 'super')
                                            <form method="POST" action="/admin/usuarios/{{ $user['id'] }}/cargo" class="m-0">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="cargo" value="super">
                                                <button type="submit"
                                                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors flex items-center">
                                                    <i class="fas fa-crown mr-2 text-red-400"></i>
                                                    Super Admin
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-slate-500 py-8">Nenhum cliente encontrado</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginação --}}
        @if(method_exists($clientLeads, 'total'))
            <div class="mt-4 flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
                <div class="text-xs text-slate-500">
                    @if($clientLeads->total() > 0)
                        Mostrando <span class="font-semibold text-slate-700">{{ $clientLeads->firstItem() }}</span>
                        a <span class="font-semibold text-slate-700">{{ $clientLeads->lastItem() }}</span>
                        de <span class="font-semibold text-slate-700">{{ $clientLeads->total() }}</span> usuários
                    @else
                        Nenhum usuário encontrado
                    @endif
                </div>
                <div class="text-sm">
                    {{ $clientLeads->appends(request()->except('users_page'))->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

{{-- ============================================ --}}
{{-- MODAL DE DETALHES DO USUÁRIO --}}
{{-- ============================================ --}}
<div id="modalDetalhesUsuario" class="fixed inset-0 z-[9999] hidden">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" id="modalDetalhesBg"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col">

            {{-- Header --}}
            <div class="px-6 pt-6 pb-4 border-b border-slate-100 flex items-center justify-between flex-shrink-0">
                <div>
                    <p class="text-[10px] font-black text-[#448ccb] uppercase tracking-[0.3em]">Detalhes do cliente</p>
                    <h3 class="text-xl font-black text-slate-900 mt-1" id="detalheNomeUsuario">—</h3>
                </div>
                <button id="btnFecharDetalhes" class="w-9 h-9 rounded-xl bg-slate-50 hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-600 transition-colors">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            {{-- Loading --}}
            <div id="detalheLoading" class="flex-1 flex items-center justify-center py-16">
                <div class="flex flex-col items-center gap-3">
                    <i class="fas fa-spinner fa-spin text-2xl text-slate-300"></i>
                    <p class="text-sm text-slate-400 font-medium">Carregando...</p>
                </div>
            </div>

            {{-- Conteúdo --}}
            <div id="detalheConteudo" class="hidden flex-1 overflow-y-auto">

                {{-- Resumo --}}
                <div class="px-6 py-4 grid grid-cols-3 gap-3 border-b border-slate-100">
                    <div class="bg-slate-50 rounded-xl p-3 text-center">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Saldo</p>
                        <p class="text-base font-black text-slate-900" id="detalheSaldo">—</p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-3 text-center">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Proxies</p>
                        <p class="text-base font-black text-emerald-600" id="detalheQtdProxies">—</p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-3 text-center">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Cliente desde</p>
                        <p class="text-base font-black text-slate-900" id="detalheDesde">—</p>
                    </div>
                </div>

                {{-- Tabs --}}
                <div class="px-6 pt-4 flex gap-2 border-b border-slate-100">
                    <button id="tabProxies" data-tab="proxies" class="tab-btn px-4 py-2 rounded-t-xl text-xs font-bold transition-all border-b-2 border-[#23366f] text-[#23366f]">
                        <i class="fas fa-server mr-1"></i> Proxies
                    </button>
                    <button id="tabTransacoes" data-tab="transacoes" class="tab-btn px-4 py-2 rounded-t-xl text-xs font-bold transition-all border-b-2 border-transparent text-slate-400 hover:text-slate-700">
                        <i class="fas fa-receipt mr-1"></i> Transações
                    </button>
                </div>

                {{-- Aba Proxies --}}
                <div id="abaProxies" class="px-6 py-4">
                    <div id="listaProxies" class="space-y-2"></div>
                    <p id="semProxies" class="hidden text-center text-slate-400 text-sm py-8">Nenhuma proxy encontrada.</p>
                </div>

                {{-- Aba Transações --}}
                <div id="abaTransacoes" class="px-6 py-4 hidden">
                    <div id="listaTransacoes" class="space-y-2"></div>
                    <p id="semTransacoes" class="hidden text-center text-slate-400 text-sm py-8">Nenhuma transação encontrada.</p>
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 border-t border-slate-100 flex-shrink-0">
                <button id="btnFecharDetalhesBottom" class="w-full py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-sm font-bold text-slate-600 transition-colors">
                    Fechar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    if (window._usuariosDetalhesLoaded) return;
    window._usuariosDetalhesLoaded = true;

    /* ── helpers ── */
    function esc(str) {
        const d = document.createElement('div');
        d.textContent = str ?? '';
        return d.innerHTML;
    }

    const statusCfg = {
        ativa:       { text: 'Ativa',       bg: '#ecfdf5', color: '#059669', border: '#a7f3d0' },
        bloqueada:   { text: 'Bloqueada',   bg: '#fef2f2', color: '#dc2626', border: '#fecaca' },
        substituida: { text: 'Substituída', bg: '#fff7ed', color: '#ea580c', border: '#fed7aa' },
    };

    const tipoLabel = { compra_proxy: 'Compra', renovacao: 'Renovação', recarga: 'Recarga' };

    /* ── abrir / fechar modal ── */
    function fechar() {
        document.getElementById('modalDetalhesUsuario').classList.add('hidden');
        document.body.style.overflow = '';
    }

    document.getElementById('btnFecharDetalhes').addEventListener('click', fechar);
    document.getElementById('btnFecharDetalhesBottom').addEventListener('click', fechar);
    document.getElementById('modalDetalhesBg').addEventListener('click', fechar);
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') fechar(); });

    /* ── tabs ── */
    document.querySelectorAll('.tab-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const aba = btn.dataset.tab;
            ['proxies', 'transacoes'].forEach(function (a) {
                const isActive = a === aba;
                document.getElementById('aba' + capitalize(a)).classList.toggle('hidden', !isActive);
                const t = document.getElementById('tab' + capitalize(a));
                t.classList.toggle('border-[#23366f]', isActive);
                t.classList.toggle('text-[#23366f]', isActive);
                t.classList.toggle('border-transparent', !isActive);
                t.classList.toggle('text-slate-400', !isActive);
            });
        });
    });

    function capitalize(s) { return s.charAt(0).toUpperCase() + s.slice(1); }

    /* ── construir card de proxy via DOM ── */
    function criarCardProxy(p) {
        const wrap = document.createElement('div');
        wrap.className = 'flex items-center justify-between gap-3 p-3 rounded-xl border border-slate-100 bg-slate-50/50';

        const st    = statusCfg[p.status] || { text: p.status, bg: '#f8fafc', color: '#64748b', border: '#e2e8f0' };
        const dias  = parseInt(p.periodo, 10);
        const expTx = dias > 0 ? dias + 'd restantes' : (p.expiracao !== '—' ? 'Expirado' : '—');
        const expCl = dias <= 0 ? '#ef4444' : dias <= 3 ? '#d97706' : '#334155';

        /* lado esquerdo */
        const left = document.createElement('div');
        left.className = 'flex items-center gap-3 min-w-0';

        const addr = document.createElement('span');
        addr.className = 'font-mono text-xs font-bold text-slate-700 bg-white px-2 py-1 rounded-lg border border-slate-200 shrink-0';
        addr.textContent = p.endereco;

        const badge = document.createElement('span');
        badge.className = 'text-[10px] font-black uppercase px-2 py-0.5 rounded-full border';
        badge.style.cssText = `background:${st.bg};color:${st.color};border-color:${st.border}`;
        badge.textContent = st.text;

        left.appendChild(addr);
        left.appendChild(badge);

        if (p.motivo_uso) {
            const motivo = document.createElement('span');
            motivo.className = 'text-[10px] text-slate-400 font-medium hidden sm:block';
            motivo.textContent = p.motivo_uso;
            left.appendChild(motivo);
        }

        /* lado direito */
        const right = document.createElement('div');
        right.className = 'flex items-center gap-3 shrink-0';

        const expEl = document.createElement('span');
        expEl.className = 'text-xs font-bold';
        expEl.style.color = expCl;
        expEl.textContent = expTx;

        const socks5 = 'socks5://' + p.ip + ':' + p.porta + ':' + p.usuario + ':' + p.senha;
        const copyBtn = document.createElement('button');
        copyBtn.type = 'button';
        copyBtn.title = 'Copiar socks5';
        copyBtn.className = 'w-7 h-7 rounded-lg bg-white border border-slate-200 hover:border-slate-300 flex items-center justify-center text-slate-400 hover:text-slate-700 transition-colors';
        copyBtn.innerHTML = '<i class="fas fa-copy text-[10px]"></i>';
        copyBtn.addEventListener('click', function () {
            navigator.clipboard.writeText(socks5).then(function () {
                if (typeof window.showToast === 'function') window.showToast('Copiado!', 'success');
            });
        });

        right.appendChild(expEl);
        right.appendChild(copyBtn);

        wrap.appendChild(left);
        wrap.appendChild(right);
        return wrap;
    }

    /* ── construir card de transação via DOM ── */
    function criarCardTransacao(t) {
        const wrap = document.createElement('div');
        wrap.className = 'flex items-center justify-between gap-3 p-3 rounded-xl border border-slate-100 bg-slate-50/50';

        const left = document.createElement('div');
        left.className = 'flex items-center gap-3';

        const icon = document.createElement('div');
        icon.className = 'w-8 h-8 rounded-xl bg-emerald-100 flex items-center justify-center shrink-0';
        icon.innerHTML = '<i class="fas fa-check text-[10px] text-emerald-600"></i>';

        const info = document.createElement('div');

        const titulo = document.createElement('p');
        titulo.className = 'text-xs font-bold text-slate-900';
        let label = tipoLabel[t.tipo] || t.tipo;
        if (t.quantidade) label += ' · ' + t.quantidade + 'x';
        if (t.periodo)    label += ' · ' + t.periodo + 'd';
        titulo.textContent = label;

        const data = document.createElement('p');
        data.className = 'text-[10px] text-slate-400';
        data.textContent = t.data;

        info.appendChild(titulo);
        info.appendChild(data);
        left.appendChild(icon);
        left.appendChild(info);

        const valor = document.createElement('span');
        valor.className = 'text-sm font-black text-emerald-600';
        valor.textContent = 'R$ ' + t.valor;

        wrap.appendChild(left);
        wrap.appendChild(valor);
        return wrap;
    }

    /* ── abrir modal ── */
    document.addEventListener('click', async function (e) {
        const btn = e.target.closest('[data-open-user-detail]');
        if (!btn) return;

        const userId = btn.dataset.userId;
        const nome   = btn.dataset.userName;

        const modal    = document.getElementById('modalDetalhesUsuario');
        const loading  = document.getElementById('detalheLoading');
        const conteudo = document.getElementById('detalheConteudo');

        document.getElementById('detalheNomeUsuario').textContent = nome;
        loading.classList.remove('hidden');
        conteudo.classList.add('hidden');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        /* reset tabs */
        document.getElementById('tabProxies').click();

        try {
            const res  = await fetch('/admin/usuarios/' + encodeURIComponent(userId) + '/detalhes', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') || {}).content || ''
                }
            });
            const data = await res.json();

            document.getElementById('detalheSaldo').textContent      = 'R$ ' + data.user.saldo;
            document.getElementById('detalheQtdProxies').textContent = data.proxies.length;
            document.getElementById('detalheDesde').textContent      = data.user.created_at;

            /* proxies */
            const listaProxies = document.getElementById('listaProxies');
            const semProxies   = document.getElementById('semProxies');
            listaProxies.replaceChildren();
            if (data.proxies.length === 0) {
                semProxies.classList.remove('hidden');
            } else {
                semProxies.classList.add('hidden');
                data.proxies.forEach(function (p) { listaProxies.appendChild(criarCardProxy(p)); });
            }

            /* transações */
            const listaTxn = document.getElementById('listaTransacoes');
            const semTxn   = document.getElementById('semTransacoes');
            listaTxn.replaceChildren();
            if (data.transacoes.length === 0) {
                semTxn.classList.remove('hidden');
            } else {
                semTxn.classList.add('hidden');
                data.transacoes.forEach(function (t) { listaTxn.appendChild(criarCardTransacao(t)); });
            }

            loading.classList.add('hidden');
            conteudo.classList.remove('hidden');

        } catch (err) {
            console.error(err);
            loading.querySelector('p').textContent = 'Erro ao carregar detalhes.';
        }
    });
})();
</script>
