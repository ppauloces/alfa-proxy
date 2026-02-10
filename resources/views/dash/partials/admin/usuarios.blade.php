<div class="flex flex-col gap-2 mb-8">
    <p class="text-sm uppercase tracking-[0.35em] text-slate-500">Clientes & colaboradores</p>
    <h1 class="text-3xl font-bold text-slate-900">Quem compra e quem opera</h1>
    <p class="text-slate-500">Leads ativos, gasto acumulado e o time que mantém tudo em produção.</p>
</div>

<div class="">
    <div class="admin-card lg:col-span-2">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-4">
            <h2 class="text-xl font-semibold text-slate-900">Clientes</h2>
            <!-- <button type="button" class="btn-secondary text-xs px-3 py-2"><i class="fas fa-plus"></i> Novo lead</button> -->
        </div>

        {{-- Toolbar: busca + limite por página (mantém layout) --}}
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
                        @if(Auth::user()->isSuperAdmin())
                            <th>Ações</th>
                        @endif
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

                            @if(Auth::user()->isSuperAdmin())
                            <td class="relative">
                                <div x-data="{
                                        open: false,
                                        top: 0,
                                        left: 0,
                                        toggle() {
                                        if (this.open) { this.open = false; return }
                                        const r = this.$refs.btn.getBoundingClientRect()
                                        const w = 192 // w-48
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
                            </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ Auth::user()->isSuperAdmin() ? 7 : 6 }}" class="text-center text-slate-500 py-8">Nenhum cliente encontrado</td>
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