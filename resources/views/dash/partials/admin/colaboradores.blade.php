<div class="flex flex-col gap-2 mb-8">
    <p class="text-sm uppercase tracking-[0.35em] text-slate-500">Gestão de equipe</p>
    <h1 class="text-3xl font-bold text-slate-900">Colaboradores</h1>
    <p class="text-slate-500">Gerencie os administradores que operam o sistema.</p>
</div>

<div class="">
    <div class="admin-card lg:col-span-2">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-4">
            <h2 class="text-xl font-semibold text-slate-900">Administradores</h2>
        </div>

        {{-- Toolbar: busca + limite por página --}}
        <form method="GET" action="{{ url()->current() }}" class="mb-4">
            <input type="hidden" name="section" value="admin-colaboradores">
            <div class="flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
                <div class="flex-1">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <input
                            type="text"
                            name="collab_q"
                            value="{{ request('collab_q') }}"
                            placeholder="Buscar por nome ou e-mail..."
                            @input.debounce.400ms="$el.form.requestSubmit()"
                            class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-200 bg-white text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#23366f]/20"
                        />
                    </div>
                </div>

                <div class="flex items-center gap-2">
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
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Username</th>
                        <th>Cargo</th>
                        <th>Status</th>
                        <th>Cadastro</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($colaboradores ?? [] as $colab)
                        <tr>
                            <td class="font-semibold text-slate-900">{{ $colab->name ?? 'N/A' }}</td>
                            <td class="text-xs text-slate-500">{{ $colab->email }}</td>
                            <td class="text-xs text-slate-700 font-mono">{{ $colab->username }}</td>
                            <td class="text-xs">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 font-semibold ring-1 ring-inset bg-blue-100 text-blue-700 ring-blue-200">
                                    Admin
                                </span>
                            </td>
                            <td>
                                @if($colab->status)
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset bg-emerald-100 text-emerald-700 ring-emerald-200">
                                        <i class="fas fa-check-circle mr-1"></i> Ativo
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset bg-red-100 text-red-700 ring-red-200">
                                        <i class="fas fa-ban mr-1"></i> Inativo
                                    </span>
                                @endif
                            </td>
                            <td class="text-xs text-slate-500">{{ $colab->created_at->format('d/m/Y') }}</td>
                            <td class="relative">
                                <div x-data="{
                                        open: false,
                                        top: 0,
                                        left: 0,
                                        toggle() {
                                            if (this.open) { this.open = false; return }
                                            const r = this.$refs.btn.getBoundingClientRect()
                                            const w = 220
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
                                        class="fixed w-56 bg-white rounded-lg shadow-lg border border-slate-200 py-1 z-[9999]"
                                        :style="`top:${top}px;left:${left}px;`" style="display:none;">

                                        {{-- Toggle status --}}
                                        <form method="POST" action="{{ route('admin.colaboradores.toggle-status', $colab->id) }}" class="m-0">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 transition-colors flex items-center">
                                                @if($colab->status)
                                                    <i class="fas fa-ban mr-2 text-red-400"></i>
                                                    Desativar colaborador
                                                @else
                                                    <i class="fas fa-check mr-2 text-emerald-400"></i>
                                                    Ativar colaborador
                                                @endif
                                            </button>
                                        </form>

                                        {{-- Rebaixar para usuario --}}
                                        <form method="POST" action="{{ route('admin.colaboradores.cargo', $colab->id) }}" class="m-0">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="cargo" value="usuario">
                                            <button type="submit"
                                                class="w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 transition-colors flex items-center">
                                                <i class="fas fa-user mr-2 text-slate-400"></i>
                                                Rebaixar para usuário
                                            </button>
                                        </form>

                                        {{-- Promover para super --}}
                                        <form method="POST" action="{{ route('admin.colaboradores.cargo', $colab->id) }}" class="m-0">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="cargo" value="super">
                                            <button type="submit"
                                                class="w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 transition-colors flex items-center">
                                                <i class="fas fa-crown mr-2 text-amber-400"></i>
                                                Promover para master
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-slate-500 py-8">Nenhum colaborador encontrado</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginação --}}
        @if(isset($colaboradores) && method_exists($colaboradores, 'total'))
            <div class="mt-4 flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
                <div class="text-xs text-slate-500">
                    @if($colaboradores->total() > 0)
                        Mostrando <span class="font-semibold text-slate-700">{{ $colaboradores->firstItem() }}</span>
                        a <span class="font-semibold text-slate-700">{{ $colaboradores->lastItem() }}</span>
                        de <span class="font-semibold text-slate-700">{{ $colaboradores->total() }}</span> colaboradores
                    @else
                        Nenhum colaborador encontrado
                    @endif
                </div>
                <div class="text-sm">
                    {{ $colaboradores->appends(request()->except('collab_page'))->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
