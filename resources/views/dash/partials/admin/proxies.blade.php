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
                @foreach($generatedProxies as $proxy)
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
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="flex flex-col gap-4 mb-4">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-slate-900">Controle de estoque por VPS</h2>
        <span class="text-sm text-slate-500">Clique para expandir cada nó</span>
    </div>
</div>

<div class="space-y-4">
    @foreach($vpsFarm as $idx => $farm)
        <div class="vps-card">
            <button type="button" class="w-full text-left" data-admin-accordion="vps-{{ $idx }}">
                <div class="vps-header">
                    <div>
                        <p class="text-lg font-semibold text-slate-900">{{ $farm['apelido'] }}</p>
                        <p class="text-sm text-slate-500">{{ $farm['ip'] }} • {{ $farm['pais'] }} •
                            {{ $farm['hospedagem'] }}
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="badge-status"
                            data-status="{{ \Illuminate\Support\Str::slug($farm['status'], '-') }}">{{ $farm['status'] }}</span>
                        <i class="fas fa-chevron-down text-slate-400 text-sm"></i>
                    </div>
                </div>
                <div class="vps-meta mt-3">
                    <span><i class="fas fa-wallet"></i> {{ $farm['valor'] }}</span>
                    <span><i class="fas fa-calendar-alt"></i> {{ $farm['periodo'] }}</span>
                    <span><i class="fas fa-clock"></i> Contratada em {{ $farm['contratada'] }}</span>
                </div>
            </button>
            <div id="vps-{{ $idx }}" class="vps-body hidden">
                <div class="grid md:grid-cols-2 gap-3">
                    @foreach($farm['proxies'] as $pIdx => $proxy)
                        @php $statusId = "proxy-status-{$idx}-{$pIdx}"; @endphp
                        <div class="proxy-pill">
                            <div>
                                <p class="font-semibold text-slate-900">{{ $proxy['codigo'] }} • {{ $proxy['endpoint'] }}</p>
                                <span id="{{ $statusId }}" class="badge-status"
                                    data-status="{{ $proxy['status'] }}">{{ ucfirst($proxy['status']) }}</span>
                            </div>
                            <div class="flex flex-col gap-2 text-xs text-center">
                                <button type="button" class="btn-secondary text-xs px-3 py-2" data-action="test-proxy"><i
                                        class="fas fa-vial"></i> Testar</button>
                                <button type="button" class="btn-secondary text-xs px-3 py-2" data-toggle-port
                                    data-target="#{{ $statusId }}"
                                    data-state="{{ $proxy['status'] === 'bloqueada' ? 'blocked' : 'open' }}">
                                    <i class="fas fa-ban"></i>
                                    {{ $proxy['status'] === 'bloqueada' ? 'Desbloquear' : 'Bloquear' }}
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach
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
</script>