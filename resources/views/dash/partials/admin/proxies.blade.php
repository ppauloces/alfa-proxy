<style>
    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    .animate-spin {
        animation: spin 1s linear infinite;
    }

    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    @keyframes pulse {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: .5;
        }
    }
</style>

<div class="flex flex-col gap-2 mb-8">
    <p class="text-sm uppercase tracking-[0.35em] text-slate-500">Módulo de geração</p>
    <h1 class="text-3xl font-bold text-slate-900">Farm de proxies & controle de estoque</h1>
    <p class="text-slate-500">Cadastre novas VPS, gere blocos de proxies e visualize o status de cada porta em tempo
        real.</p>
</div>

<!-- Painel de Monitoramento em Tempo Real -->
<div id="statusPanel" class="mb-6" style="display: none;">
    <div class="admin-card bg-gradient-to-r from-blue-50 to-indigo-50 border-blue-200">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center">
                    <i class="fas fa-sync-alt text-white animate-spin text-sm"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900">Geração de Proxies em Andamento</h3>
                    <p class="text-sm text-slate-600">Atualizando a cada 5 segundos</p>
                </div>
            </div>
            <span id="lastUpdate" class="text-xs text-slate-500"></span>
        </div>

        <div id="vpsStatusList" class="space-y-3">
            <!-- Lista de VPS será preenchida dinamicamente -->
        </div>
    </div>
</div>

<!-- Container de Notificações Toast -->
<div id="toastContainer" class="fixed top-4 right-4 z-50 space-y-3" style="max-width: 400px;">
    <!-- Toasts serão injetados aqui -->
</div>

<div class="grid mb-10">
    <div class="admin-card lg:col-span-2">
        <h2 class="text-xl font-semibold text-slate-900 mb-4">Cadastrar VPS / Rodar farm</h2>
        <form class="grid md:grid-cols-3 gap-4 text-sm" action="{{ route('vps.cadastrar') }}" method="POST">
            @csrf
            <!-- 
            @if($errors->any())
                <div class="md:col-span-3">
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                        <p class="font-semibold mb-2">Erro ao cadastrar VPS:</p>
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif -->

            @if(session('success'))
                <div class="md:col-span-3">
                    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            <label class="flex flex-col gap-2">
                <span class="text-slate-500 font-semibold">Apelido da VPS</span>
                <input type="text" name="apelido" class="form-input" placeholder="BR-ALFA 01"
                    value="{{ old('apelido') }}">
                <span class="text-xs text-slate-400">Deixe em branco para gerar automaticamente</span>
            </label>
            <label class="flex flex-col gap-2">
                <span class="text-slate-500 font-semibold">IP da VPS*</span>
                <input type="text" name="ip" class="form-input @error('ip') border-red-500 @enderror"
                    placeholder="201.94.10.12" value="{{ old('ip') }}" required>
                @error('ip')
                    <span class="text-xs text-red-500">{{ $message }}</span>
                @enderror
            </label>
            <label class="flex flex-col gap-2">
                <span class="text-slate-500 font-semibold">Usuário (ex: root)*</span>
                <input type="text" name="usuario_ssh" class="form-input @error('usuario_ssh') border-red-500 @enderror"
                    placeholder="root" value="{{ old('usuario_ssh') }}" required>
                @error('usuario_ssh')
                    <span class="text-xs text-red-500">{{ $message }}</span>
                @enderror
            </label>
            <label class="flex flex-col gap-2">
                <span class="text-slate-500 font-semibold">Senha SSH*</span>
                <input type="password" name="senha_ssh" class="form-input @error('senha_ssh') border-red-500 @enderror"
                    placeholder="••••••••" required>
                @error('senha_ssh')
                    <span class="text-xs text-red-500">{{ $message }}</span>
                @enderror
            </label>
            <label class="flex flex-col gap-2">
                <span class="text-slate-500 font-semibold">Valor da VPS*</span>
                <input type="number" name="valor" step="0.01" min="0"
                    class="form-input @error('valor') border-red-500 @enderror" placeholder="120.00"
                    value="{{ old('valor') }}" required>
                @error('valor')
                    <span class="text-xs text-red-500">{{ $message }}</span>
                @enderror
            </label>
            <label class="flex flex-col gap-2">
                <span class="text-slate-500 font-semibold">País*</span>
                <select name="pais" class="form-select @error('pais') border-red-500 @enderror" required>
                    <option value="">Selecione</option>
                    <option value="Brasil" {{ old('pais') == 'Brasil' ? 'selected' : '' }}>Brasil</option>
                    <option value="Estados Unidos" {{ old('pais') == 'Estados Unidos' ? 'selected' : '' }}>Estados Unidos
                    </option>
                    <option value="Portugal" {{ old('pais') == 'Portugal' ? 'selected' : '' }}>Portugal</option>
                    <option value="Alemanha" {{ old('pais') == 'Alemanha' ? 'selected' : '' }}>Alemanha</option>
                </select>
                @error('pais')
                    <span class="text-xs text-red-500">{{ $message }}</span>
                @enderror
            </label>
            <label class="flex flex-col gap-2">
                <span class="text-slate-500 font-semibold">Hospedagem*</span>
                <input type="text" name="hospedagem" class="form-input @error('hospedagem') border-red-500 @enderror"
                    placeholder="OVH, Hetzner..." value="{{ old('hospedagem') }}" required>
                @error('hospedagem')
                    <span class="text-xs text-red-500">{{ $message }}</span>
                @enderror
            </label>
            <label class="flex flex-col gap-2">
                <span class="text-slate-500 font-semibold">Período contratado*</span>
                <select name="periodo_dias" class="form-select @error('periodo_dias') border-red-500 @enderror"
                    required>
                    <option value="">Selecione</option>
                    <option value="30" {{ old('periodo_dias') == '30' ? 'selected' : '' }}>30 dias</option>
                    <option value="60" {{ old('periodo_dias') == '60' ? 'selected' : '' }}>60 dias</option>
                    <option value="90" {{ old('periodo_dias') == '90' ? 'selected' : '' }}>90 dias</option>
                    <option value="180" {{ old('periodo_dias') == '180' ? 'selected' : '' }}>180 dias</option>
                </select>
                @error('periodo_dias')
                    <span class="text-xs text-red-500">{{ $message }}</span>
                @enderror
            </label>
            <label class="flex flex-col gap-2">
                <span class="text-slate-500 font-semibold">Data da contratação*</span>
                <input type="date" name="data_contratacao"
                    class="form-input @error('data_contratacao') border-red-500 @enderror"
                    value="{{ old('data_contratacao', now()->toDateString()) }}" required>
                @error('data_contratacao')
                    <span class="text-xs text-red-500">{{ $message }}</span>
                @enderror
            </label>

            <div class="md:col-span-3 flex items-center gap-3 mb-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="rodar_script" value="1"
                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" checked>
                    <span class="text-slate-700 font-semibold">Rodar script de geração de proxies</span>
                </label>
            </div>

            <div class="md:col-span-3 flex flex-wrap gap-3 mt-6">
                <button type="submit" class="btn-secondary">
                    <i class="fas fa-floppy-disk"></i> Cadastrar VPS
                </button>
            </div>
        </form>

    </div>

    <!-- <div class="admin-card">
        <h2 class="text-xl font-semibold text-slate-900 mb-4">Fila do farm</h2>
        <div class="space-y-3 text-sm">
            <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50">
                <span class="font-semibold text-slate-900">BR-ALFA 01</span>
                <span class="badge-status" data-status="disponivel">Rodando (32%)</span>
            </div>
            <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50">
                <span class="font-semibold text-slate-900">US-NODE 03</span>
                <span class="badge-status" data-status="bloqueada">SSH inválido</span>
            </div>
            <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50">
                <span class="font-semibold text-slate-900">EU-SCALA 02</span>
                <span class="badge-status" data-status="vendida">Fila concluída</span>
            </div>
        </div>
    </div> -->
</div>

<div class="admin-card mb-10">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-semibold text-slate-900">Proxies recém geradas</h2>
        <button type="button" class="btn-secondary text-xs px-3 py-2"><i class="fas fa-copy"></i> Exportar
            lista</button>
    </div>
    <div class="overflow-x-auto">
        <table class="admin-table text-sm min-w-full">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Endereço</th>
                    <th>Usuário</th>
                    <th>Senha</th>
                    <th>VPS</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($generatedProxies as $proxy)
                    <tr>
                        <td>{{ $proxy['numero'] }}</td>
                        <td class="font-mono text-xs">{{ $proxy['endereco'] }}</td>
                        <td>{{ $proxy['user'] }}</td>
                        <td class="font-mono text-xs">{{ $proxy['senha'] }}</td>
                        <td>{{ $proxy['vps'] }}</td>
                        <td><span class="badge-status"
                                data-status="{{ \Illuminate\Support\Str::slug(strtolower($proxy['status'])) }}">{{ $proxy['status'] }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-8 text-slate-500">
                            <i class="fas fa-inbox text-4xl mb-3 text-slate-300"></i>
                            <p>Nenhuma proxy gerada ainda</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="flex flex-col gap-4 mb-4">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-slate-900">Controle de estoque por VPS</h2>
        <span class="text-sm text-slate-500">Clique em uma VPS para ver os detalhes</span>
    </div>
</div>

<div class="space-y-4">
    @if($vpsFarm->count() === 0)
        <div class="admin-card bg-slate-50">
            <div class="text-center py-12">
                <i class="fas fa-server text-slate-300 text-6xl mb-4"></i>
                <h3 class="text-xl font-semibold text-slate-700 mb-2">Nenhuma VPS cadastrada</h3>
                <p class="text-slate-500 mb-6">Cadastre sua primeira VPS acima para começar a gerar proxies.</p>
            </div>
        </div>
    @endif

    @if($vpsFarm->count() > 0)
        <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach($vpsFarm as $farm)
                @php
                    $totalProxies = $farm->proxies->count();
                    $bloqueadas = $farm->proxies->where('bloqueada', true)->count();
                    $disponiveis = $farm->proxies->where('bloqueada', false)->where('disponibilidade', true)->count();
                    $vendidas = max(0, $totalProxies - $bloqueadas - $disponiveis);
                @endphp

                <button type="button"
                    class="admin-card w-full text-left hover:shadow-md transition-shadow"
                    data-open-vps-modal="vpsModal-{{ $farm->id }}">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="text-lg font-semibold text-slate-900 truncate">{{ $farm->apelido }}</p>
                            <p class="text-sm text-slate-500 truncate">{{ $farm->ip }} &middot; {{ $farm->pais }} &middot; {{ $farm->hospedagem }}</p>
                        </div>
                        <div class="flex items-center gap-3 flex-shrink-0">
                            <span class="badge-status"
                                data-status="{{ \Illuminate\Support\Str::slug($farm->status, '-') }}">{{ $farm->status }}</span>
                            <i class="fas fa-arrow-up-right-from-square text-slate-400 text-sm"></i>
                        </div>
                    </div>

                    <div class="vps-meta mt-3">
                        <span><i class="fas fa-wallet"></i> {{ $farm->valor }}</span>
                        <span><i class="fas fa-calendar-alt"></i> {{ $farm->periodo }}</span>
                        <span><i class="fas fa-clock"></i> Contratada em {{ $farm->contratada }}</span>
                    </div>

                    <div class="mt-4 grid grid-cols-3 gap-2 text-xs">
                        <div class="bg-slate-50 rounded-lg p-2 text-center">
                            <p class="text-slate-500">Disponíveis</p>
                            <p class="font-bold text-slate-900">{{ $disponiveis }}</p>
                        </div>
                        <div class="bg-slate-50 rounded-lg p-2 text-center">
                            <p class="text-slate-500">Bloqueadas</p>
                            <p class="font-bold text-slate-900">{{ $bloqueadas }}</p>
                        </div>
                        <div class="bg-slate-50 rounded-lg p-2 text-center">
                            <p class="text-slate-500">Vendidas</p>
                            <p class="font-bold text-slate-900">{{ $vendidas }}</p>
                        </div>
                    </div>
                </button>
            @endforeach
        </div>

        <!-- Modais por VPS -->
        @foreach($vpsFarm as $farm)
            <div id="vpsModal-{{ $farm->id }}" class="admin-modal-overlay hidden" data-vps-modal>
                <div class="admin-modal w-full" style="max-width: 1200px; width: 95%;">
                    <div class="flex items-start justify-between gap-4 mb-4">
                        <div class="min-w-0">
                            <h3 class="text-2xl font-semibold text-slate-900 truncate">{{ $farm->apelido }}</h3>
                            <p class="text-sm text-slate-500 truncate">{{ $farm->ip }} &middot; {{ $farm->pais }} &middot; {{ $farm->hospedagem }}</p>
                            <div class="vps-meta mt-2">
                                <span><i class="fas fa-wallet"></i> {{ $farm->valor }}</span>
                                <span><i class="fas fa-calendar-alt"></i> {{ $farm->periodo }}</span>
                                <span><i class="fas fa-clock"></i> Contratada em {{ $farm->contratada }}</span>
                            </div>
                        </div>
                        <button type="button" class="btn-secondary text-xs px-3 py-2" data-close-vps-modal>
                            <i class="fas fa-times"></i> Fechar
                        </button>
                    </div>

                    <div class="border-t border-slate-200 pt-4">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-sm font-semibold text-slate-700">
                                Proxies ({{ $farm->proxies->count() }})
                            </p>
                        </div>

                        @if($farm->proxies->count() === 0)
                            <div class="text-center py-10 text-slate-500">
                                <i class="fas fa-network-wired text-4xl mb-3 text-slate-300"></i>
                                <p>Nenhuma proxy gerada nesta VPS ainda</p>
                            </div>
                        @else
                            <div class="max-h-[70vh] overflow-y-auto pr-1">
                                <div class="grid md:grid-cols-2 gap-3">
                                    @foreach($farm->proxies as $proxy)
                                        @php
                                            $statusId = "proxy-status-{$farm->id}-{$proxy->id}";
                                            // Determinar status baseado no campo bloqueada do banco de dados
                                            if ($proxy->bloqueada) {
                                                $proxyStatus = 'bloqueada';
                                            } elseif ($proxy->disponibilidade) {
                                                $proxyStatus = 'disponivel';
                                            } else {
                                                $proxyStatus = 'vendida';
                                            }
                                            $proxyEndpoint = $farm->ip . ':' . $proxy->porta;
                                            $proxyCodigo = '#' . str_pad($proxy->id, 3, '0', STR_PAD_LEFT);
                                        @endphp
                                        <div class="proxy-pill">
                                            <div>
                                                <p class="font-semibold text-slate-900">{{ $proxyCodigo }} &middot; {{ $proxyEndpoint }}</p>
                                                <span id="{{ $statusId }}" class="badge-status"
                                                    data-status="{{ $proxyStatus }}">{{ ucfirst($proxyStatus) }}</span>
                                            </div>
                                            <div class="flex flex-col gap-2 text-xs text-center">
                                                <button type="button" class="btn-secondary text-xs px-3 py-2" data-action="test-proxy"><i
                                                        class="fas fa-vial"></i> Testar</button>
                                                <button type="button" class="btn-secondary text-xs px-3 py-2" data-toggle-port
                                                    data-stock-id="{{ $proxy->id }}"
                                                    data-target="#{{ $statusId }}"
                                                    data-state="{{ $proxy->bloqueada ? 'blocked' : 'open' }}">
                                                    <i class="fas {{ $proxy->bloqueada ? 'fa-unlock' : 'fa-ban' }}"></i>
                                                    {{ $proxy->bloqueada ? 'Desbloquear' : 'Bloquear' }}
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>
<!-- Modal de Loading -->
<div id="loadingModal" class="admin-modal-overlay hidden">
    <div class="admin-modal" style="max-width: 400px;">
        <div class="text-center">
            <div class="mb-6">
                <div class="inline-block animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-blue-600">
                </div>
            </div>
            <h3 class="text-xl font-semibold text-slate-900 mb-2">Gerando Proxies</h3>
            <p class="text-sm text-slate-500 mb-4" id="loadingMessage">Aguarde enquanto as proxies estão sendo
                geradas...</p>
            <div class="flex items-center justify-center gap-2 text-xs text-slate-400">
                <span class="animate-pulse">●</span>
                <span>Conectando com a VPS</span>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.querySelector('form[action="{{ route("vps.cadastrar") }}"]');
        const checkbox = document.getElementById('rodar_script');
        const quantidadeLabel = document.getElementById('quantidade_label');
        const loadingModal = document.getElementById('loadingModal');
        const loadingMessage = document.getElementById('loadingMessage');

        // Mostrar/ocultar campo de quantidade
        if (checkbox && quantidadeLabel) {
            checkbox.addEventListener('change', function () {
                quantidadeLabel.style.display = this.checked ? 'flex' : 'none';
            });
        }

        // Interceptar submit do formulário
        if (form) {
            form.addEventListener('submit', function (e) {
                const rodarScript = checkbox && checkbox.checked;

                // Se o checkbox estiver marcado, fazer requisição AJAX
                if (rodarScript) {
                    e.preventDefault();

                    // Mostrar modal de loading
                    loadingModal.classList.remove('hidden');
                    loadingModal.classList.add('active');

                    // Criar FormData
                    const formData = new FormData(form);

                    // Atualizar mensagem de loading
                    const steps = [
                        'Conectando com a VPS...',
                        'Configurando servidor proxy...',
                        'Gerando credenciais...',
                        'Criando portas...',
                        'Finalizando configuração...'
                    ];

                    let stepIndex = 0;
                    const stepInterval = setInterval(() => {
                        if (stepIndex < steps.length) {
                            loadingMessage.textContent = steps[stepIndex];
                            stepIndex++;
                        }
                    }, 2000);

                    // Fazer requisição AJAX
                    fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || formData.get('_token')
                        }
                    })
                        .then(response => {
                            clearInterval(stepInterval);

                            if (response.ok) {
                                return response.json();
                            }
                            throw new Error('Erro na requisição');
                        })
                        .then(data => {
                            loadingMessage.textContent = 'Proxies geradas com sucesso!';

                            // Aguardar um pouco antes de redirecionar
                            setTimeout(() => {
                                if (data.redirect) {
                                    window.location.href = data.redirect;
                                } else {
                                    window.location.reload();
                                }
                            }, 1000);
                        })
                        .catch(error => {
                            clearInterval(stepInterval);
                            loadingMessage.textContent = 'Erro ao gerar proxies: ' + error.message;

                            setTimeout(() => {
                                loadingModal.classList.add('hidden');
                                loadingModal.classList.remove('active');
                                alert('Erro ao processar. Tente novamente.');
                            }, 3000);
                        });
                }
                // Se não estiver marcado, deixar o formulário submeter normalmente
            });
        }
    });

    // ============================================
    // MODAIS DE VPS (cards -> modal grande)
    // ============================================
    function openVpsModal(modal) {
        modal.classList.remove('hidden');
        modal.classList.add('active');

        const dialog = modal.querySelector('.admin-modal');
        if (dialog) dialog.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeVpsModal(modal) {
        modal.classList.add('hidden');
        modal.classList.remove('active');

        const dialog = modal.querySelector('.admin-modal');
        if (dialog) dialog.classList.remove('active');
        document.body.style.overflow = '';
    }

    document.addEventListener('click', function (e) {
        const openBtn = e.target.closest('[data-open-vps-modal]');
        if (openBtn) {
            const modalId = openBtn.dataset.openVpsModal;
            const modal = document.getElementById(modalId);
            if (modal) openVpsModal(modal);
            return;
        }

        const closeBtn = e.target.closest('[data-close-vps-modal]');
        if (closeBtn) {
            const modal = closeBtn.closest('[data-vps-modal]');
            if (modal) closeVpsModal(modal);
            return;
        }

        // Clique fora (overlay)
        if (e.target && e.target.matches('[data-vps-modal]')) {
            closeVpsModal(e.target);
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Escape') return;
        const openModals = Array.from(document.querySelectorAll('[data-vps-modal]')).filter(m => !m.classList.contains('hidden'));
        const lastModal = openModals[openModals.length - 1];
        if (lastModal) closeVpsModal(lastModal);
    });

    // ============================================
    // SISTEMA DE MONITORAMENTO EM TEMPO REAL
    // ============================================

    let pollingInterval = null;
    let vpsCompletadas = new Set(); // Rastrear VPS já notificadas

    // Função para mostrar toast de notificação
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `transform transition-all duration-300 translate-x-full`;

        const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
        const icon = type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle';

        toast.innerHTML = `
            <div class="${bgColor} text-white px-6 py-4 rounded-lg shadow-lg flex items-center gap-3">
                <i class="fas ${icon} text-xl"></i>
                <span class="font-medium">${message}</span>
            </div>
        `;

        document.getElementById('toastContainer').appendChild(toast);

        // Animar entrada
        setTimeout(() => {
            toast.className = 'transform transition-all duration-300 translate-x-0';
        }, 10);

        // Remover após 5 segundos
        setTimeout(() => {
            toast.className = 'transform transition-all duration-300 translate-x-full';
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    }

    // Função para atualizar status das VPS
    async function atualizarStatusVPS() {
        try {
            const response = await fetch('/api/vps/status-geracao');
            const data = await response.json();

            if (!data.success) return;

            const statusPanel = document.getElementById('statusPanel');
            const vpsStatusList = document.getElementById('vpsStatusList');
            const lastUpdate = document.getElementById('lastUpdate');

            // Se não tem VPS em processamento, esconder painel
            if (!data.tem_processamento_ativo) {
                statusPanel.style.display = 'none';
                if (pollingInterval) {
                    clearInterval(pollingInterval);
                    pollingInterval = null;
                }
                return;
            }

            // Mostrar painel
            statusPanel.style.display = 'block';

            // Atualizar timestamp
            lastUpdate.textContent = 'Atualizado agora';

            // Limpar lista
            vpsStatusList.innerHTML = '';

            // Renderizar cada VPS
            data.vps.forEach(vps => {
                const vpsCard = document.createElement('div');
                vpsCard.className = 'bg-white rounded-xl p-4 border border-slate-200';

                // Verificar se acabou de completar (notificar)
                if (vps.status === 'completed' && !vpsCompletadas.has(vps.id)) {
                    vpsCompletadas.add(vps.id);
                    showToast(`✅ VPS ${vps.apelido} concluída! ${vps.proxies_geradas} proxies geradas.`, 'success');

                    // Reproduzir som de notificação (opcional)
                    if ('Audio' in window) {
                        try {
                            const audio = new Audio('/sounds/notification.mp3');
                            audio.volume = 0.3;
                            audio.play().catch(() => {}); // Ignorar erro se não tiver áudio
                        } catch(e) {}
                    }
                }

                // Verificar se falhou (notificar)
                if (vps.status === 'failed' && !vpsCompletadas.has(vps.id)) {
                    vpsCompletadas.add(vps.id);
                    showToast(`❌ Erro na VPS ${vps.apelido}: ${vps.erro}`, 'error');
                }

                //Barra de progresso (simulada para "processing")
                let progressHTML = '';
                if (vps.status === 'processing') {
                    progressHTML = `
                        <div class="mt-3">
                            <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden">
                                <div class="bg-blue-500 h-2 rounded-full animate-pulse" style="width: 100%; animation: progress 2s ease-in-out infinite;"></div>
                            </div>
                        </div>
                    `;
                }

                vpsCard.innerHTML = `
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="font-bold text-slate-900">${vps.apelido}</span>
                                <span class="text-xs px-2 py-1 rounded-full ${vps.badge_class}">${vps.badge_text}</span>
                            </div>
                            <p class="text-sm text-slate-600">${vps.ip}</p>
                            ${vps.proxies_geradas > 0 ? `<p class="text-xs text-green-600 mt-1"><i class="fas fa-check"></i> ${vps.proxies_geradas} proxies geradas</p>` : ''}
                            ${vps.erro ? `<p class="text-xs text-red-600 mt-1"><i class="fas fa-exclamation-triangle"></i> ${vps.erro}</p>` : ''}
                            <p class="text-xs text-slate-400 mt-1">${vps.ultima_atualizacao}</p>
                        </div>
                        ${vps.status === 'processing' ? '<i class="fas fa-cog fa-spin text-2xl text-blue-500"></i>' : ''}
                        ${vps.status === 'completed' ? '<i class="fas fa-check-circle text-2xl text-green-500"></i>' : ''}
                        ${vps.status === 'failed' ? '<i class="fas fa-times-circle text-2xl text-red-500"></i>' : ''}
                        ${vps.status === 'pending' ? '<i class="fas fa-clock text-2xl text-yellow-500"></i>' : ''}
                    </div>
                    ${progressHTML}
                `;

                vpsStatusList.appendChild(vpsCard);
            });

        } catch (error) {
            console.error('Erro ao atualizar status:', error);
        }
    }

    // Iniciar polling quando carregar a página
    document.addEventListener('DOMContentLoaded', function() {
        // Primeira verificação imediata
        atualizarStatusVPS();

        // Polling a cada 5 segundos
        pollingInterval = setInterval(atualizarStatusVPS, 5000);
    });

    // Parar polling quando sair da página
    window.addEventListener('beforeunload', function() {
        if (pollingInterval) {
            clearInterval(pollingInterval);
        }
    });

    // ============================================
    // BLOQUEIO/DESBLOQUEIO DE PORTAS
    // ============================================

    document.addEventListener('click', async function(e) {
        const toggleButton = e.target.closest('[data-toggle-port]');
        if (!toggleButton) return;

        e.preventDefault();

        const stockId = toggleButton.dataset.stockId;
        const targetStatus = toggleButton.querySelector('.badge-status') || document.querySelector(toggleButton.dataset.target);
        const currentState = toggleButton.dataset.state; // 'blocked' or 'open'
        const icon = toggleButton.querySelector('i');
        const btnText = toggleButton.childNodes[toggleButton.childNodes.length - 1];

        // Determinar ação (se está bloqueada, desbloquear; se está aberta, bloquear)
        const action = currentState === 'blocked' ? 'bloquear' : 'desbloquear';
        const endpoint = action === 'desbloquear' ? '/admin/proxy/bloquear' : '/admin/proxy/bloquear';
       
        console.log(action, endpoint);
        // Desabilitar botão durante requisição
        toggleButton.disabled = true;
        icon.className = 'fas fa-spinner fa-spin';
        btnText.textContent = ' Processando...';

        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({ stock_id: stockId })
            });

            const data = await response.json();

            if (data.success) {
                // Atualizar estado visual
                if (action === 'bloquear') {
                    if (targetStatus) {
                        targetStatus.dataset.status = 'bloqueada';
                        targetStatus.textContent = 'Bloqueada';
                    }
                    toggleButton.dataset.state = 'blocked';
                    icon.className = 'fas fa-unlock';
                    btnText.textContent = ' Desbloquear';
                } else {
                    if (targetStatus) {
                        targetStatus.dataset.status = 'disponivel';
                        targetStatus.textContent = 'Disponivel';
                    }
                    toggleButton.dataset.state = 'open';
                    icon.className = 'fas fa-ban';
                    btnText.textContent = ' Bloquear';
                }

                // Mostrar notificação de sucesso
                showToast(data.message || `Porta ${action === 'bloquear' ? 'bloqueada' : 'desbloqueada'} com sucesso!`, 'success');
            } else {
                showToast(data.error || 'Erro ao processar requisição', 'error');
                icon.className = 'fas fa-ban';
                btnText.textContent = action === 'bloquear' ? ' Bloquear' : ' Desbloquear';
            }
        } catch (error) {
            console.error('Erro:', error);
            showToast('Erro ao conectar com o servidor', 'error');
            icon.className = 'fas fa-ban';
            btnText.textContent = action === 'bloquear' ? ' Bloquear' : ' Desbloquear';
        } finally {
            toggleButton.disabled = false;
        }
    });
</script>

<style>
    @keyframes progress {
        0% { transform: translateX(-100%); }
        50% { transform: translateX(0); }
        100% { transform: translateX(100%); }
    }
</style>
