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

    /* Modal grande das VPS: sobrepor layout (header/sidebar) e evitar cortes */
    .admin-modal.vps-modal {
        width: min(95vw, 1400px);
        max-width: 1400px;
        max-height: calc(100vh - 4rem);
        display: flex;
        flex-direction: column;
        padding: 0;
        overflow: hidden;
    }

    .admin-modal.vps-modal .vps-modal-header {
        padding: 2rem 2.5rem;
        background: #fff;
        border-bottom: 1px solid rgba(226, 232, 240, 0.8);
        flex-shrink: 0;
    }

    .admin-modal.vps-modal .vps-modal-body {
        flex: 1;
        overflow-y: auto;
        padding: 2.5rem;
        min-height: 0;
    }

    /* Garantir que a modal de VPS fique centralizada (apenas modais com data-vps-modal) */
    [data-vps-modal].admin-modal-overlay {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
    }

    /* Cards de proxy dentro da modal (mais espaçamento e foco consistente) */
    .vps-proxy-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1.25rem;
        padding: 1.25rem 1.4rem;
        border-radius: 22px;
        background: #f8fafc;
        border: 1px solid rgba(226, 232, 240, 0.9);
        transition: border-color 0.15s ease, box-shadow 0.15s ease, transform 0.15s ease;
    }

    .vps-proxy-card:hover {
        border-color: rgba(35, 54, 111, 0.35);
        box-shadow: 0 14px 40px rgba(15, 23, 42, 0.08);
        transform: translateY(-1px);
    }

    .vps-proxy-info p {
        margin: 0;
    }

    .vps-proxy-status {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.6rem;
        border-radius: 10px;
        font-size: 9px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.12em;
    }

    .vps-proxy-status[data-status="disponivel"] {
        background: rgba(16, 185, 129, 0.12);
        color: #047857;
    }

    .vps-proxy-status[data-status="vendida"] {
        background: rgba(245, 158, 11, 0.14);
        color: #b45309;
    }

    .vps-proxy-status[data-status="bloqueada"] {
        background: rgba(239, 68, 68, 0.14);
        color: #b91c1c;
    }

    .vps-proxy-status[data-status="uso_interno"] {
        background: rgba(99, 102, 241, 0.14);
        color: #4338ca;
    }

    .vps-proxy-actions {
        display: flex;
        flex-direction: column;
        gap: 0.6rem;
        flex-shrink: 0;
    }

    .vps-proxy-action-btn {
        min-width: 170px;
        height: 44px;
        padding: 0 0.9rem;
        border-radius: 16px;
        background: #fff;
        border: 1px solid rgba(226, 232, 240, 0.9);
        color: #64748b;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        transition: transform 0.12s ease, border-color 0.12s ease, color 0.12s ease, box-shadow 0.12s ease;
        user-select: none;
        font-size: 0.85rem;
        font-weight: 700;
    }

    .vps-proxy-action-btn:hover {
        color: #23366f;
        border-color: rgba(35, 54, 111, 0.35);
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.06);
    }

    .vps-proxy-action-btn:active {
        transform: translateY(1px);
    }

    .vps-proxy-action-btn i {
        font-size: 0.95rem;
    }

    .vps-proxy-action-btn:focus {
        outline: none;
    }

    .vps-proxy-action-btn:focus-visible {
        box-shadow: 0 0 0 4px rgba(68, 140, 203, 0.18);
        border-color: rgba(68, 140, 203, 0.75);
        color: #23366f;
    }

    .vps-proxy-action-btn.danger:hover {
        color: #ef4444;
        border-color: rgba(239, 68, 68, 0.55);
    }

    /* Modais de uso interno - z-index maior que modais de VPS */
    #usoInternoModal,
    #removerUsoInternoModal {
        z-index: 99999 !important;
        background-color: rgba(15, 23, 42, 0.75) !important; /* Backdrop mais escuro */
    }

    #usoInternoModal.active,
    #removerUsoInternoModal.active {
        backdrop-filter: blur(4px);
    }

    #usoInternoModal .admin-modal,
    #removerUsoInternoModal .admin-modal {
        z-index: 100000 !important;
        position: relative;
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
<div id="toastContainer" class="fixed top-20 right-4 space-y-3" style="max-width: 400px; z-index: 9999;">
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
                <div class="relative">
                    <input type="text" id="vps_valor_mask" 
                        class="form-input pl-10 @error('valor') border-red-500 @enderror" 
                        placeholder="0,00"
                        value="{{ old('valor') ? number_format(old('valor'), 2, ',', '.') : '' }}" required>
                    
                    <input type="hidden" name="valor" id="vps_valor_real" value="{{ old('valor') }}">
                </div>
                @error('valor')
                    <span class="text-xs text-red-500">{{ $message }}</span>
                @enderror
            </label>
            <label class="flex flex-col gap-2">
                <span class="text-slate-500 font-semibold">Valor de Renovação da VPS</span>
                <div class="relative">
                    <input type="text" id="vps_valor_renovacao_mask" 
                        class="form-input pl-10 @error('valor_renovacao') border-red-500 @enderror" 
                        placeholder="0,00"
                        value="{{ old('valor_renovacao') ? number_format(old('valor_renovacao'), 2, ',', '.') : '' }}" required>
                    
                    <input type="hidden" name="valor_renovacao" id="vps_valor_renovacao_real" value="{{ old('valor_renovacao') }}">
                </div>
                @error('valor_renovacao')
                    <span class="text-xs text-red-500">{{ $message }}</span>
                @enderror
            </label>
            <label class="flex flex-col gap-2">
                <span class="text-slate-500 font-semibold">País*</span>
                <x-ui.select name="pais" :value="old('pais')" placeholder="Selecione" :options="[
                    'Brasil' => 'Brasil',
                    'Estados Unidos' => 'Estados Unidos',
                    'Reino Unido' => 'Reino Unido',
                    'Alemanha' => 'Alemanha',
                    'França' => 'França',
                    'Itália' => 'Itália',
                    'Espanha' => 'Espanha',
                    'Portugal' => 'Portugal',
                    'Canadá' => 'Canadá',
                    'Austrália' => 'Austrália'
                ]" required>
                </x-ui.select>
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
                <x-ui.select name="periodo_dias" :value="old('periodo_dias')" placeholder="Selecione" :options="[
                    '30' => '30 dias',
                    '60' => '60 dias',
                    '90' => '90 dias',
                    '180' => '180 dias'
                ]" required>
                </x-ui.select>
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

                <label class="flex items-center gap-2 cursor-pointer ml-auto">
                    <input type="checkbox" name="vps_paga" value="1"
                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" checked>
                    <span class="text-slate-700 font-semibold">VPS paga (entrar como despesa)</span>
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



<div class="flex flex-col gap-4 mb-4">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-slate-900">Controle de estoque por VPS</h2>
        <span class="text-sm text-slate-500">Clique em uma VPS para ver os detalhes</span>
    </div>

    {{-- Campo de pesquisa de proxies --}}
    <div class="admin-card bg-gradient-to-br from-blue-50 to-indigo-50 border-blue-200">
        <div class="flex items-center gap-3">
            <div class="flex-shrink-0">
                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-search text-blue-600"></i>
                </div>
            </div>
            <div class="flex-1">
                <label for="proxySearch" class="block text-sm font-medium text-slate-700 mb-1">
                    Pesquisar Proxy
                </label>
                <div class="relative">
                    <input
                        type="text"
                        id="proxySearch"
                        placeholder="Digite IP, porta, usuário ou senha..."
                        class="w-full px-4 py-2.5 pr-10 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        autocomplete="off"
                    >
                    <button
                        type="button"
                        id="clearProxySearch"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 hidden"
                        title="Limpar pesquisa"
                    >
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    Pesquise por IP, porta, usuário ou senha. Clique no resultado para abrir a VPS correspondente.
                </p>
            </div>
        </div>

        {{-- Resultados da pesquisa --}}
        <div id="proxySearchResults" class="hidden mt-4">
            <div class="border-t border-blue-200 pt-4">
                <p class="text-sm font-medium text-slate-700 mb-3">
                    <span id="searchResultCount">0</span> resultado(s) encontrado(s)
                </p>
                <div id="searchResultsList" class="space-y-2 max-h-96 overflow-y-auto">
                    {{-- Resultados serão inseridos aqui via JavaScript --}}
                </div>
            </div>
        </div>
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
                    $usoInterno = $farm->proxies->where('uso_interno', true)->count();
                    $disponiveis = $farm->proxies->where('bloqueada', false)->where('uso_interno', false)->where('disponibilidade', true)->count();
                    $vendidas = max(0, $totalProxies - $bloqueadas - $usoInterno - $disponiveis);
                @endphp

                <div class="admin-card w-full text-left hover:shadow-md transition-shadow relative">
                    <button type="button" class="w-full text-left" data-open-vps-modal="vpsModal-{{ $farm->id }}">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="text-lg font-semibold text-slate-900 truncate">{{ $farm->apelido }}</p>
                            <p class="text-sm text-slate-500 truncate">{{ $farm->ip }} &middot; {{ $farm->pais }} &middot;
                                {{ $farm->hospedagem }}</p>
                        </div>
                        <div class="flex items-center gap-3 flex-shrink-0">
                            <span class="badge-status"
                                data-status="{{ \Illuminate\Support\Str::slug($farm->status, '-') }}">{{ $farm->status }}</span>
                            <i class="fas fa-pen-to-square text-slate-400 text-sm"></i>
                        </div>
                    </div>

                    <div class="vps-meta mt-3">
                        <span><i class="fas fa-wallet"></i> {{ $farm->valor }}</span>
                        <span><i class="fas fa-calendar-alt"></i> {{ $farm->periodo }}</span>
                        <span><i class="fas fa-clock"></i> Contratada em {{ $farm->contratada }}</span>
                    </div>

                    <div class="mt-4 grid grid-cols-4 gap-2 text-xs">
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
                        <div class="bg-indigo-50 rounded-lg p-2 text-center">
                            <p class="text-indigo-600">Uso Interno</p>
                            <p class="font-bold text-indigo-900">{{ $usoInterno }}</p>
                        </div>
                    </div>
                    </button>

                    {{-- Botão de Editar VPS (fora do botão principal) --}}
                    <button type="button"
                        class="edit-vps-btn absolute top-3 right-3 w-8 h-8 rounded-lg bg-slate-100 text-slate-500 hover:text-[#448ccb] hover:bg-blue-50 transition-all flex items-center justify-center z-10"
                        data-vps-id="{{ $farm->id }}"
                        title="Editar VPS">
                        <i class="fas fa-cog text-sm"></i>
                    </button>
                </div>
            @endforeach
        </div>

        <!-- Modais por VPS -->
        @foreach($vpsFarm as $farm)
            <div id="vpsModal-{{ $farm->id }}" class="admin-modal-overlay hidden" data-vps-modal>
                <div class="admin-modal vps-modal">
                    <div class="vps-modal-header flex items-start justify-between gap-4">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <h3 id="vps-apelido-{{ $farm->id }}" class="text-xl lg:text-2xl font-black text-slate-900 truncate tracking-tight">
                                    {{ $farm->apelido }}</h3>
                                <button type="button"
                                    class="w-8 h-8 rounded-lg bg-slate-100 text-slate-400 hover:text-slate-900 hover:bg-slate-200 transition-all flex items-center justify-center"
                                    data-edit-apelido="{{ $farm->id }}"
                                    title="Editar apelido">
                                    <i class="fas fa-pen text-xs"></i>
                                </button>
                            </div>
                            <p class="text-xs lg:text-sm text-slate-400 font-medium truncate">
                                {{ $farm->ip }} &middot;
                                <span id="vps-pais-{{ $farm->id }}" class="inline-flex items-center gap-1 cursor-pointer hover:text-slate-600 transition-colors" data-edit-pais="{{ $farm->id }}" title="Clique para editar o país">
                                    {{ $farm->pais }}
                                    <i class="fas fa-pen text-[8px] opacity-50"></i>
                                </span>
                                &middot; {{ $farm->hospedagem }}
                            </p>
                            <div
                                class="vps-meta mt-3 flex flex-wrap gap-4 text-[10px] font-black uppercase tracking-widest text-slate-400">
                                <span class="flex items-center gap-1.5"><i class="fas fa-wallet text-[#448ccb]"></i>
                                    {{ $farm->valor }}</span>
                                <span class="flex items-center gap-1.5"><i class="fas fa-calendar-alt text-[#448ccb]"></i>
                                    {{ $farm->periodo }}</span>
                                <span class="hidden sm:flex items-center gap-1.5"><i class="fas fa-clock text-[#448ccb]"></i>
                                    {{ $farm->contratada }}</span>
                            </div>
                        </div>
                        <button type="button"
                            class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 hover:text-slate-900 transition-all flex items-center justify-center"
                            data-close-vps-modal>
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div class="vps-modal-body">
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-50">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                Proxies Ativas ({{ $farm->proxies->count() }})
                            </p>
                        </div>

                        @if($farm->proxies->count() === 0)
                            <div class="text-center py-20 text-slate-500">
                                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-network-wired text-2xl text-slate-300"></i>
                                </div>
                                <p class="font-medium">Nenhuma proxy gerada nesta VPS ainda</p>
                            </div>
                        @else
                            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4">
                                @foreach($farm->proxies as $proxy)
                                    @php
                                        $statusId = "proxy-status-{$farm->id}-{$proxy->id}";
                                        if ($proxy->bloqueada) {
                                            $proxyStatus = 'bloqueada';
                                        } elseif ($proxy->uso_interno) {
                                            $proxyStatus = 'uso_interno';
                                        } elseif ($proxy->disponibilidade) {
                                            $proxyStatus = 'disponivel';
                                        } else {
                                            $proxyStatus = 'vendida';
                                        }
                                        $proxyEndpoint = $farm->ip . ':' . $proxy->porta;
                                        $proxyCodigo = '#' . str_pad($proxy->id, 3, '0', STR_PAD_LEFT);
                                    @endphp
                                    <div class="vps-proxy-card" data-proxy-id="{{ $proxy->id }}">
                                        <div class="vps-proxy-info">
                                            <p class="text-sm font-black text-slate-900 mb-2">{{ $proxyCodigo }} &middot;
                                                {{ $proxyEndpoint }}</p>
                                            <span id="{{ $statusId }}" class="vps-proxy-status" data-status="{{ $proxyStatus }}">
                                                @if($proxyStatus === 'disponivel')
                                                    <span class="text-green-600 px-2 py-0.5 rounded">Disponível</span>
                                                @elseif($proxyStatus === 'bloqueada')
                                                    <span class="text-red-600 px-2 py-0.5 rounded">Bloqueada</span>
                                                @elseif($proxyStatus === 'uso_interno')
                                                    <span class="text-indigo-600 px-2 py-0.5 rounded">Uso Interno</span>
                                                @else
                                                    <span class="text-amber-600 px-2 py-0.5 rounded">Vendida</span>
                                                @endif
                                            </span>
                                            @if($proxy->uso_interno && $proxy->finalidade_interna)
                                                <p class="text-xs text-indigo-600 mt-1">
                                                    <i class="fas fa-briefcase"></i> {{ $proxy->finalidade_interna }}
                                                </p>
                                            @endif
                                        </div>
                                        <div class="vps-proxy-actions">
                                            <button type="button" class="vps-proxy-action-btn" data-action="copy-proxy"
                                                data-proxy-string="socks5://{{ $farm->ip }}:{{ $proxy->porta }}:{{ $proxy->usuario }}:{{ $proxy->senha }}">
                                                <i class="fas fa-copy"></i>
                                                <span>Copiar proxy</span>
                                            </button>
                                            <button type="button" class="vps-proxy-action-btn test-proxy-btn" data-action="test-proxy"
                                                data-ip="{{ $farm->ip }}" data-porta="{{ $proxy->porta }}"
                                                data-usuario="{{ $proxy->usuario }}" data-senha="{{ $proxy->senha }}">
                                                <i class="fas fa-vial"></i>
                                                <span>Testar proxy</span>
                                            </button>

                                            @if($proxy->uso_interno)
                                                {{-- Bot\u00e3o para REMOVER uso interno --}}
                                                <button type="button" class="vps-proxy-action-btn" data-action="remover-uso-interno"
                                                    data-stock-id="{{ $proxy->id }}" data-target="#{{ $statusId }}">
                                                    <i class="fas fa-undo"></i>
                                                    <span>Remover uso interno</span>
                                                </button>
                                            @elseif($proxy->disponibilidade && !$proxy->bloqueada)
                                                {{-- Bot\u00e3o para MARCAR como uso interno (apenas para proxies dispon\u00edveis e n\u00e3o bloqueadas) --}}
                                                <button type="button" class="vps-proxy-action-btn" data-action="marcar-uso-interno"
                                                    data-stock-id="{{ $proxy->id }}" data-target="#{{ $statusId }}">
                                                    <i class="fas fa-briefcase"></i>
                                                    <span>Uso interno</span>
                                                </button>
                                            @endif

                                            @if(!$proxy->bloqueada)
                                                {{-- Bot\u00e3o para BLOQUEAR (apenas para proxies n\u00e3o bloqueadas) --}}
                                                <button type="button" class="vps-proxy-action-btn danger" data-toggle-port
                                                    data-stock-id="{{ $proxy->id }}" data-target="#{{ $statusId }}"
                                                    data-state="open" data-ip="{{ $farm->ip }}"
                                                    data-porta="{{ $proxy->porta }}" data-usuario-ssh="{{ $farm->usuario_ssh ?? 'root' }}"
                                                    data-senha-ssh="{{ $farm->senha_ssh ?? '' }}">
                                                    <i class="fas fa-ban"></i>
                                                    <span data-btn-text>Bloquear</span>
                                                </button>
                                                @if($proxy->expiracao && \Carbon\Carbon::parse($proxy->expiracao)->isPast())
                                                    {{-- Botão para RENOVAR DATA (proxy expirada mas não bloqueada) --}}
                                                    <button type="button" class="vps-proxy-action-btn" data-toggle-port
                                                        data-stock-id="{{ $proxy->id }}" data-target="#{{ $statusId }}"
                                                        data-state="blocked" data-expirada="true" data-ip="{{ $farm->ip }}"
                                                        data-porta="{{ $proxy->porta }}" data-usuario-ssh="{{ $farm->usuario_ssh ?? 'root' }}"
                                                        data-senha-ssh="{{ $farm->senha_ssh ?? '' }}">
                                                        <i class="fas fa-calendar-plus"></i>
                                                        <span data-btn-text>Renovar data</span>
                                                    </button>
                                                @endif
                                            @else
                                                {{-- Bot\u00e3o para DESBLOQUEAR (apenas para proxies bloqueadas) --}}
                                                <button type="button" class="vps-proxy-action-btn danger" data-toggle-port
                                                    data-stock-id="{{ $proxy->id }}" data-target="#{{ $statusId }}"
                                                    data-state="blocked" data-expirada="{{ ($proxy->expiracao && \Carbon\Carbon::parse($proxy->expiracao)->isPast()) ? 'true' : 'false' }}"
                                                    data-ip="{{ $farm->ip }}"
                                                    data-porta="{{ $proxy->porta }}" data-usuario-ssh="{{ $farm->usuario_ssh ?? 'root' }}"
                                                    data-senha-ssh="{{ $farm->senha_ssh ?? '' }}">
                                                    <i class="fas fa-unlock"></i>
                                                    <span data-btn-text>Desbloquear</span>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach

        {{-- Modal de Edição de VPS --}}
        @foreach($vpsFarm as $farm)
            <div id="editVpsModal-{{ $farm->id }}" class="admin-modal-overlay hidden" data-edit-vps-modal>
                <div class="admin-modal" style="max-width: 600px;">
                    <div class="p-6">
                        {{-- Header --}}
                        <div class="flex items-start justify-between mb-6">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-server text-blue-600 text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-slate-900">Editar VPS</h3>
                                    <p class="text-sm text-slate-500 mt-1">{{ $farm->apelido }} - {{ $farm->ip }}</p>
                                </div>
                            </div>
                            <button type="button" class="text-slate-400 hover:text-slate-600 transition-colors" data-close-edit-vps-modal>
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>

                        {{-- Form --}}
                        <form id="editVpsForm-{{ $farm->id }}" onsubmit="submitEditVps(event, {{ $farm->id }})">
                            <div class="grid grid-cols-2 gap-4 mb-6">
                                {{-- Valor da VPS --}}
                                <label class="flex flex-col gap-2">
                                    <span class="text-sm text-slate-600 font-semibold">Valor da VPS (R$)</span>
                                    <input type="text"
                                        id="edit_valor_mask_{{ $farm->id }}"
                                        class="form-input"
                                        placeholder="0,00"
                                        value="{{ number_format((float)$farm->valor, 2, ',', '.') }}">
                                    <input type="hidden" name="valor" id="edit_valor_real_{{ $farm->id }}" value="{{ $farm->valor }}">
                                </label>

                                {{-- Valor de Renovação --}}
                                <label class="flex flex-col gap-2">
                                    <span class="text-sm text-slate-600 font-semibold">Valor de Renovação (R$)</span>
                                    <input type="text"
                                        id="edit_valor_renovacao_mask_{{ $farm->id }}"
                                        class="form-input"
                                        placeholder="0,00"
                                        value="{{ $farm->valor_renovacao ? number_format((float)$farm->valor_renovacao, 2, ',', '.') : '' }}">
                                    <input type="hidden" name="valor_renovacao" id="edit_valor_renovacao_real_{{ $farm->id }}" value="{{ $farm->valor_renovacao }}">
                                    <span class="text-xs text-slate-400">Deixe vazio para usar o valor da VPS</span>
                                </label>

                                {{-- Data de Contratação --}}
                                <label class="flex flex-col gap-2">
                                    <span class="text-sm text-slate-600 font-semibold">Data de Contratação</span>
                                    <input type="date"
                                        name="data_contratacao"
                                        class="form-input"
                                        value="{{ $farm->data_contratacao ? $farm->data_contratacao->format('Y-m-d') : '' }}">
                                </label>

                                {{-- Período Contratado --}}
                                <label class="flex flex-col gap-2">
                                    <span class="text-sm text-slate-600 font-semibold">Período Contratado</span>
                                    <select name="periodo_dias" class="form-input">
                                        <option value="30" {{ $farm->periodo_dias == 30 ? 'selected' : '' }}>30 dias</option>
                                        <option value="60" {{ $farm->periodo_dias == 60 ? 'selected' : '' }}>60 dias</option>
                                        <option value="90" {{ $farm->periodo_dias == 90 ? 'selected' : '' }}>90 dias</option>
                                        <option value="180" {{ $farm->periodo_dias == 180 ? 'selected' : '' }}>180 dias</option>
                                    </select>
                                </label>

                                {{-- Hospedagem --}}
                                <label class="flex flex-col gap-2">
                                    <span class="text-sm text-slate-600 font-semibold">Hospedagem</span>
                                    <input type="text"
                                        name="hospedagem"
                                        class="form-input"
                                        placeholder="OVH, Hetzner..."
                                        value="{{ $farm->hospedagem }}">
                                </label>

                                {{-- Status da VPS --}}
                                <label class="flex flex-col gap-2">
                                    <span class="text-sm text-slate-600 font-semibold">Status</span>
                                    <select name="status" class="form-input">
                                        <option value="Operacional" {{ $farm->status == 'Operacional' ? 'selected' : '' }}>Operacional</option>
                                        <option value="Desabilitada" {{ $farm->status == 'Desabilitada' ? 'selected' : '' }}>Desabilitada</option>
                                        <option value="Excluída" {{ $farm->status == 'Excluída' ? 'selected' : '' }}>Excluída</option>
                                    </select>
                                </label>
                            </div>

                            {{-- Informativo sobre status --}}
                            <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-xl">
                                <div class="flex gap-3">
                                    <i class="fas fa-info-circle text-amber-600 mt-0.5"></i>
                                    <div class="text-sm text-amber-800">
                                        <p class="font-semibold mb-1">Sobre o Status:</p>
                                        <ul class="text-xs space-y-1 text-amber-700">
                                            <li><strong>Operacional:</strong> VPS ativa, despesas de renovação são geradas automaticamente</li>
                                            <li><strong>Desabilitada:</strong> VPS inativa, despesas de renovação <strong>NÃO</strong> são geradas</li>
                                            <li><strong>Excluída:</strong> VPS removida do sistema</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            {{-- Ações --}}
                            <div class="flex gap-3">
                                <button type="button"
                                    data-close-edit-vps-modal
                                    class="flex-1 px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl transition-colors">
                                    <i class="fas fa-times"></i> Cancelar
                                </button>
                                <button type="submit"
                                    class="flex-1 px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-colors">
                                    <i class="fas fa-save"></i> Salvar Alterações
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>

<div class="admin-card mt-10">
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
<!-- Modal de Loading -->
<div id="loadingModal" class="admin-modal-overlay hidden">
    <div class="admin-modal" style="max-width: 400px;">
        <div class="text-center py-10">
            <div class="mb-8">
                <div class="relative inline-block">
                    <div class="w-20 h-20 rounded-full border-4 border-slate-100 border-t-[#23366f] animate-spin"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <i class="fas fa-network-wired text-[#23366f] text-2xl animate-pulse"></i>
                    </div>
                </div>
            </div>
            <h3 class="text-2xl font-black text-slate-900 mb-2 tracking-tight">Gerando Proxies</h3>
            <p class="text-sm text-slate-400 font-medium mb-8" id="loadingMessage">Aguarde enquanto as proxies estão
                sendo geradas...</p>

            <div class="bg-slate-50 rounded-2xl p-4 flex items-center justify-center gap-3">
                <div class="flex gap-1">
                    <div class="w-1.5 h-1.5 rounded-full bg-[#448ccb] animate-bounce [animation-delay:-0.3s]"></div>
                    <div class="w-1.5 h-1.5 rounded-full bg-[#448ccb] animate-bounce [animation-delay:-0.15s]"></div>
                    <div class="w-1.5 h-1.5 rounded-full bg-[#448ccb] animate-bounce"></div>
                </div>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Conectando com a
                    VPS</span>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Uso Interno -->
<div id="usoInternoModal" class="admin-modal-overlay hidden">
    <div class="admin-modal" style="max-width: 500px;">
        <div class="p-6">
            <!-- Header -->
            <div class="flex items-start justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-briefcase text-indigo-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-slate-900">Marcar como Uso Interno</h3>
                        <p class="text-sm text-slate-500 mt-1">Esta proxy ficará indisponível para venda</p>
                    </div>
                </div>
                <button type="button" class="text-slate-400 hover:text-slate-600 transition-colors" onclick="closeUsoInternoModal()">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Form -->
            <form id="usoInternoForm" onsubmit="submitUsoInterno(event)">
                <div class="mb-6">
                    <label for="finalidadeInput" class="block text-sm font-semibold text-slate-700 mb-2">
                        Finalidade do uso interno *
                    </label>
                    <input
                        type="text"
                        id="finalidadeInput"
                        class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all outline-none"
                        placeholder="Ex: Testes, Marketing, Monitoramento..."
                        required
                        autocomplete="off"
                    >
                    <p class="text-xs text-slate-500 mt-2">
                        <i class="fas fa-info-circle"></i> Descreva para que esta proxy será utilizada internamente
                    </p>
                </div>

                <!-- Sugestões rápidas -->
                <div class="mb-6">
                    <p class="text-xs font-semibold text-slate-600 mb-2">Sugestões rápidas:</p>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" onclick="setFinalidade('Testes')"
                            class="px-3 py-1.5 text-xs font-medium bg-slate-100 hover:bg-indigo-100 hover:text-indigo-700 text-slate-700 rounded-lg transition-colors">
                            <i class="fas fa-flask"></i> Testes
                        </button>
                        <button type="button" onclick="setFinalidade('Marketing')"
                            class="px-3 py-1.5 text-xs font-medium bg-slate-100 hover:bg-indigo-100 hover:text-indigo-700 text-slate-700 rounded-lg transition-colors">
                            <i class="fas fa-bullhorn"></i> Marketing
                        </button>
                        <button type="button" onclick="setFinalidade('Monitoramento')"
                            class="px-3 py-1.5 text-xs font-medium bg-slate-100 hover:bg-indigo-100 hover:text-indigo-700 text-slate-700 rounded-lg transition-colors">
                            <i class="fas fa-chart-line"></i> Monitoramento
                        </button>
                        <button type="button" onclick="setFinalidade('Desenvolvimento')"
                            class="px-3 py-1.5 text-xs font-medium bg-slate-100 hover:bg-indigo-100 hover:text-indigo-700 text-slate-700 rounded-lg transition-colors">
                            <i class="fas fa-code"></i> Desenvolvimento
                        </button>
                    </div>
                </div>

                <!-- Alerta informativo -->
                <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-xl">
                    <div class="flex gap-3">
                        <i class="fas fa-exclamation-triangle text-amber-600 mt-0.5"></i>
                        <div class="text-sm text-amber-800">
                            <p class="font-semibold mb-1">Atenção:</p>
                            <ul class="text-xs space-y-1 text-amber-700">
                                <li>• A proxy será <strong>removida do estoque disponível</strong></li>
                                <li>• Clientes <strong>não poderão comprá-la</strong></li>
                                <li>• Apenas administradores verão esta proxy</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Ações -->
                <div class="flex gap-3">
                    <button
                        type="button"
                        onclick="closeUsoInternoModal()"
                        class="flex-1 px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl transition-colors">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button
                        type="submit"
                        class="flex-1 px-4 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition-colors">
                        <i class="fas fa-check"></i> Confirmar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Confirmação - Remover Uso Interno -->
<div id="removerUsoInternoModal" class="admin-modal-overlay hidden">
    <div class="admin-modal" style="max-width: 450px;">
        <div class="p-6">
            <!-- Header -->
            <div class="flex items-start gap-4 mb-6">
                <div class="w-12 h-12 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-undo text-amber-600 text-xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Remover Uso Interno</h3>
                    <p class="text-sm text-slate-600">Esta proxy voltará ao estoque disponível para venda aos clientes.</p>
                </div>
            </div>

            <!-- Informações -->
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-xl">
                <div class="flex gap-3">
                    <i class="fas fa-info-circle text-blue-600 mt-0.5"></i>
                    <div class="text-sm text-blue-800">
                        <p class="font-semibold mb-1">O que acontecerá:</p>
                        <ul class="text-xs space-y-1 text-blue-700">
                            <li>✓ A proxy voltará para o <strong>estoque disponível</strong></li>
                            <li>✓ Clientes <strong>poderão comprá-la</strong> normalmente</li>
                            <li>✓ A finalidade interna será <strong>removida</strong></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Ações -->
            <div class="flex gap-3">
                <button
                    type="button"
                    onclick="closeRemoverUsoInternoModal()"
                    class="flex-1 px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl transition-colors">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button
                    type="button"
                    onclick="confirmarRemoverUsoInterno()"
                    class="flex-1 px-4 py-3 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded-xl transition-colors">
                    <i class="fas fa-check"></i> Confirmar
                </button>
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
    const vpsModalPortalInfo = new WeakMap();

    function lockPageScroll() {
        const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
        document.body.style.overflow = 'hidden';
        if (scrollbarWidth > 0) {
            document.body.style.paddingRight = `${scrollbarWidth}px`;
        }
    }

    function unlockPageScroll() {
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
    }

    function openVpsModal(modal) {
        if (!vpsModalPortalInfo.has(modal)) {
            vpsModalPortalInfo.set(modal, {
                parent: modal.parentNode,
                nextSibling: modal.nextSibling,
            });
        }
        // Portal para fora da dashboard-shell (evita flash de z-index do header/sidebar)
        if (modal.parentNode !== document.body) {
            document.body.appendChild(modal);
        }

        modal.classList.remove('hidden');
        modal.classList.add('active');

        const dialog = modal.querySelector('.admin-modal');
        if (dialog) dialog.classList.add('active');
        lockPageScroll();
    }

    function closeVpsModal(modal) {
        modal.classList.add('hidden');
        modal.classList.remove('active');

        const dialog = modal.querySelector('.admin-modal');
        if (dialog) dialog.classList.remove('active');
        unlockPageScroll();

        const portal = vpsModalPortalInfo.get(modal);
        if (portal?.parent) {
            portal.parent.insertBefore(modal, portal.nextSibling);
        }
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

        // Garantir que o container fique acima da modal (fora do stacking context da dashboard-shell)
        if (toastContainer.parentNode !== document.body) {
            document.body.appendChild(toastContainer);
        }

        toastContainer.appendChild(toast);

        // Animar entrada
        setTimeout(() => {
            toast.className = 'transform transition-all duration-300 translate-x-0';
        }, 10);

        // Remover após 8 segundos (mais tempo para ler geolocalização)
        setTimeout(() => {
            toast.className = 'transform transition-all duration-300 translate-x-full';
            setTimeout(() => toast.remove(), 300);
        }, 8000);
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
                            audio.play().catch(() => { }); // Ignorar erro se não tiver áudio
                        } catch (e) { }
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
    document.addEventListener('DOMContentLoaded', function () {
        // Primeira verificação imediata
        atualizarStatusVPS();

        // Polling a cada 5 segundos
        pollingInterval = setInterval(atualizarStatusVPS, 5000);
    });

    // Parar polling quando sair da página
    window.addEventListener('beforeunload', function () {
        if (pollingInterval) {
            clearInterval(pollingInterval);
        }
    });

    // ============================================
    // TESTAR PROXY
    // ============================================

    // Função para obter geolocalização do IP
    async function getIpGeolocation(ip) {
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
    }

    document.addEventListener('click', async function (e) {
        const testButton = e.target.closest('[data-action="test-proxy"]');
        if (!testButton) return;

        e.preventDefault();

        // Salva a classe original do botão e o HTML original
        if (!testButton.dataset.restoreClass) {
            testButton.dataset.restoreClass = testButton.className || '';
            testButton.dataset.defaultHtml = testButton.innerHTML || '';
        }

        const defaultHTML = testButton.dataset.defaultHtml;
        const restoreClass = testButton.dataset.restoreClass;

        // Verificar se o botão tem texto (não é apenas ícone)
        const hasText = testButton.textContent.trim().length > 0;

        const ip = testButton.dataset.ip;
        const porta = testButton.dataset.porta;
        const usuario = testButton.dataset.usuario;
        const senha = testButton.dataset.senha;
        const ip_visto_pelo_servidor = testButton.dataset.ip_visto_pelo_servidor;

        // Animação de carregamento
        testButton.disabled = true;
        testButton.className = restoreClass;
        testButton.classList.add('opacity-80', 'cursor-wait');

        // Se tem texto, mostrar "Testando...", senão apenas o spinner
        if (hasText) {
            testButton.innerHTML = `
                <span class="flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4 text-[#23366f]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-[#23366f] font-bold">Testando...</span>
                </span>
            `;
        } else {
            testButton.innerHTML = `
                <svg class="animate-spin h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            `;
        }

        try {
            const response = await fetch('{{ route("proxies.testar") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({
                    ip: ip,
                    porta: parseInt(porta),
                    usuario: usuario,
                    senha: senha,
                    ip_visto_pelo_servidor: ip_visto_pelo_servidor
                })
            });

            console.log(response);

            const data = await response.json();


            if (response.ok && data.status === 'online') {
                // Buscar geolocalização do IP
                const geoData = await getIpGeolocation(data.ip_visto_pelo_servidor || ip);

                // Sucesso
                testButton.className = restoreClass;
                testButton.classList.add('bg-green-500', 'text-white', 'border-green-500');

                // Se tem texto, mostrar "Online", senão apenas ícone de check
                if (hasText) {
                    testButton.innerHTML = '<i class="fas fa-check"></i> Online';
                } else {
                    testButton.innerHTML = '<i class="fas fa-check text-xs"></i>';
                }

                // Toast com geolocalização
                let toastMessage = `<div class="space-y-1">`;
                toastMessage += `<div class="font-bold text-base mb-2">Proxy online!</div>`;
                toastMessage += `<div><strong>IP:</strong> ${data.ip_visto_pelo_servidor || 'N/A'}</div>`;
                toastMessage += `<div><strong>Latência:</strong> ${data.latencia_ms || 'N/A'}ms</div>`;

                if (geoData) {
                    toastMessage += `<div class="flex items-center gap-2">`;
                    toastMessage += `<strong>Local:</strong> ${geoData.city}, ${geoData.country}`;
                    if (geoData.flag) {
                        toastMessage += ` <img src="${geoData.flag}" alt="${geoData.country}" class="inline-block w-5 h-4 rounded">`;
                    }
                    toastMessage += `</div>`;
                }
                toastMessage += `</div>`;

                showToast(toastMessage, 'success');
            } else {
                // Erro
                testButton.className = restoreClass;
                testButton.classList.add('bg-red-500', 'text-white', 'border-red-500');

                // Se tem texto, mostrar "Offline", senão apenas ícone de X
                if (hasText) {
                    testButton.innerHTML = '<i class="fas fa-times"></i> Offline';
                } else {
                    testButton.innerHTML = '<i class="fas fa-times text-xs"></i>';
                }

                showToast(`❌ Proxy offline: ${data.mensagem || data.error || 'Não foi possível conectar'}`, 'error');
            }

            // Restaurar botão ao padrão escolhido após 1s
            setTimeout(() => {
                testButton.className = restoreClass;
                testButton.innerHTML = defaultHTML;
                testButton.disabled = false;
            }, 1000);

        } catch (error) {
            console.error('Erro ao testar proxy:', error);
            showToast('Erro ao conectar com o servidor de testes', 'error');

            testButton.className = restoreClass;
            testButton.innerHTML = defaultHTML;
            testButton.disabled = false;
        }
    });

    // ============================================
    // BLOQUEIO/DESBLOQUEIO DE PORTAS
    // ============================================

    document.addEventListener('click', async function (e) {
        const toggleButton = e.target.closest('[data-toggle-port]');
        if (!toggleButton) return;

        e.preventDefault();

        const stockId = toggleButton.dataset.stockId;
        const targetStatus = toggleButton.querySelector('.badge-status') || document.querySelector(toggleButton.dataset.target);
        const currentState = toggleButton.dataset.state; // 'blocked' or 'open'
        const icon = toggleButton.querySelector('i');
        const btnText = toggleButton.querySelector('[data-btn-text]') || toggleButton.childNodes[toggleButton.childNodes.length - 1];

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
                    <input type="date" id="novaExpiracao" class="swal2-input" min="${new Date().toISOString().split('T')[0]}" style="width:80%">
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
                    const data = document.getElementById('novaExpiracao').value;
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

            if (!dataEscolhida) return; // Usuário cancelou

            novaExpiracao = dataEscolhida;
        }

        // Desabilitar botão durante requisição
        toggleButton.disabled = true;
        icon.className = 'fas fa-spinner fa-spin';
        // Só mostrar texto se o botão tiver texto (não apenas ícone)
        if (btnText && btnText.textContent.trim()) {
            btnText.textContent = 'Processando...';
        }

        try {
            const requestBody = { stock_id: stockId };
            if (novaExpiracao) {
                requestBody.nova_expiracao = novaExpiracao;
            }

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
                if (action === 'bloquear') {
                    if (targetStatus) {
                        targetStatus.dataset.status = 'bloqueada';
                        targetStatus.textContent = 'Bloqueada';
                    }
                    toggleButton.dataset.state = 'blocked';
                    icon.className = 'fas fa-unlock';
                    if (btnText && btnText.textContent.trim()) {
                        btnText.textContent = ' Desbloquear';
                    }
                } else {
                    if (targetStatus) {
                        targetStatus.dataset.status = 'disponivel';
                        targetStatus.textContent = 'Disponível';
                    }
                    toggleButton.dataset.state = 'open';
                    icon.className = 'fas fa-ban';
                    if (btnText && btnText.textContent.trim()) {
                        btnText.textContent = ' Bloquear';
                    }
                }

                // Mostrar notificação de sucesso
                const successMsg = novaExpiracao
                    ? `Proxy desbloqueada e data de expiração atualizada para ${new Date(novaExpiracao).toLocaleDateString('pt-BR')}!`
                    : (data.message || `Porta ${action === 'bloquear' ? 'bloqueada' : 'desbloqueada'} com sucesso!`);
                showToast(successMsg, 'success');

                // Recarregar a página após 1.5 segundos para atualizar as informações
                if (novaExpiracao) {
                    setTimeout(() => location.reload(), 1500);
                }
            } else {
                showToast(data.error || 'Erro ao processar requisição', 'error');
                icon.className = action === 'bloquear' ? 'fas fa-ban' : 'fas fa-unlock';
                if (btnText && btnText.textContent.trim()) {
                    btnText.textContent = action === 'bloquear' ? 'Bloquear' : 'Desbloquear';
                }
            }
        } catch (error) {
            console.error('Erro:', error);
            showToast('Erro ao conectar com o servidor', 'error');
            icon.className = action === 'bloquear' ? 'fas fa-ban' : 'fas fa-unlock';
            if (btnText && btnText.textContent.trim()) {
                btnText.textContent = action === 'bloquear' ? 'Bloquear' : 'Desbloquear';
            }
        } finally {
            toggleButton.disabled = false;
        }
    });

    // ============================================
    // COPIAR PROXY
    // ============================================

    // Função auxiliar para copiar texto (com fallback para ambientes não-HTTPS)
    const copyToClipboardSafe = (text) => {
    // Tentar Clipboard API moderna (HTTPS/localhost)
    if (navigator.clipboard?.writeText && window.isSecureContext) {
        return navigator.clipboard.writeText(text);
    }

    // Fallback (HTTP)
    return new Promise((resolve, reject) => {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.left = '-999999px';
        textArea.style.top = '-999999px';
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();

        try {
            const successful = document.execCommand('copy');
            textArea.remove();
            successful ? resolve() : reject(new Error('Comando copy falhou'));
        } catch (err) {
            textArea.remove();
            reject(err);
        }
    });
};

    document.addEventListener('click', async function (e) {
        const copyButton = e.target.closest('[data-action="copy-proxy"]');
        if (!copyButton) return;

        e.preventDefault();

        const proxyString = copyButton.dataset.proxyString;
        const icon = copyButton.querySelector('i');
        const text = copyButton.querySelector('span');

        // Salvar conteúdo original
        const originalIconClass = icon.className;
        const originalText = text.textContent;

        try {
            // Copiar para clipboard (com fallback)
            await copyToClipboardSafe(proxyString);

            // Feedback visual de sucesso
            icon.className = 'fas fa-check';
            text.textContent = 'Copiado!';
            copyButton.classList.add('bg-green-50', 'border-green-200');

            // Toast de sucesso
            showToast(`Proxy copiado: ${proxyString}`, 'success');

            // Restaurar botão após 2 segundos
            setTimeout(() => {
                icon.className = originalIconClass;
                text.textContent = originalText;
                copyButton.classList.remove('bg-green-50', 'border-green-200');
            }, 2000);

        } catch (error) {
            console.error('Erro ao copiar proxy:', error);

            // Feedback visual de erro
            icon.className = 'fas fa-times';
            text.textContent = 'Erro ao copiar';
            copyButton.classList.add('bg-red-50', 'border-red-200');

            // Toast de erro
            showToast('Erro ao copiar proxy para área de transferência', 'error');

            // Restaurar botão após 2 segundos
            setTimeout(() => {
                icon.className = originalIconClass;
                text.textContent = originalText;
                copyButton.classList.remove('bg-red-50', 'border-red-200');
            }, 2000);
        }
    });

    // ============================================
    // MARCAR/REMOVER USO INTERNO
    // ============================================

    // Variáveis globais para o modal de uso interno
    let currentStockId = null;
    let currentTargetStatus = null;
    let currentMarcarButton = null;

    // Função para abrir modal de uso interno
    function openUsoInternoModal(stockId, targetStatus, button) {
        currentStockId = stockId;
        currentTargetStatus = targetStatus;
        currentMarcarButton = button;

        const modal = document.getElementById('usoInternoModal');
        const input = document.getElementById('finalidadeInput');

        // Limpar input
        input.value = '';

        // Portal: mover modal para o body (garantir que fique acima de tudo)
        if (modal.parentNode !== document.body) {
            document.body.appendChild(modal);
        }

        // Mostrar modal
        modal.classList.remove('hidden');
        modal.classList.add('active');

        // Focar no input
        setTimeout(() => input.focus(), 100);

        // Bloquear scroll da página
        document.body.style.overflow = 'hidden';
    }

    // Função para fechar modal de uso interno
    function closeUsoInternoModal() {
        const modal = document.getElementById('usoInternoModal');
        modal.classList.add('hidden');
        modal.classList.remove('active');

        // Desbloquear scroll da página
        document.body.style.overflow = '';

        // Limpar variáveis
        currentStockId = null;
        currentTargetStatus = null;
        currentMarcarButton = null;
    }

    // Função para definir finalidade (botões de sugestão)
    function setFinalidade(valor) {
        document.getElementById('finalidadeInput').value = valor;
    }

    // Função para submeter o formulário de uso interno
    async function submitUsoInterno(event) {
        event.preventDefault();

        const finalidade = document.getElementById('finalidadeInput').value.trim();

        if (!finalidade) {
            showToast('Finalidade não informada. Operação cancelada.', 'error');
            return;
        }

        // IMPORTANTE: Armazenar valores ANTES de fechar a modal
        // pois closeUsoInternoModal() limpa as variáveis globais
        const stockId = currentStockId;
        const targetStatus = currentTargetStatus;
        const marcarButton = currentMarcarButton;

        // Fechar modal
        closeUsoInternoModal();

        // Verificar se temos o stock_id
        if (!stockId) {
            showToast('Erro: ID da proxy não encontrado', 'error');
            return;
        }

        // Desabilitar botão durante requisição
        if (marcarButton) {
            marcarButton.disabled = true;
            const icon = marcarButton.querySelector('i');
            const text = marcarButton.querySelector('span');
            const originalIconClass = icon.className;
            const originalText = text.textContent;

            icon.className = 'fas fa-spinner fa-spin';
            text.textContent = 'Processando...';

            try {
                const response = await fetch('/admin/proxy/uso-interno', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify({
                        stock_id: stockId,
                        finalidade_interna: finalidade
                    })
                });

                const data = await response.json();

                if (data.success) {
                    // Atualizar status visual
                    if (targetStatus) {
                        targetStatus.dataset.status = 'uso_interno';
                        targetStatus.innerHTML = '<span class="text-indigo-600 px-2 py-0.5 rounded">Uso Interno</span>';
                    }

                    showToast(`Proxy marcada como uso interno: ${finalidade}`, 'success');

                    // Recarregar página após 1.5s para atualizar contadores e botões
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    showToast(data.error || 'Erro ao marcar proxy como uso interno', 'error');
                    icon.className = originalIconClass;
                    text.textContent = originalText;
                    marcarButton.disabled = false;
                }
            } catch (error) {
                console.error('Erro ao enviar requisição:', error);
                showToast('Erro ao conectar com o servidor', 'error');
                icon.className = originalIconClass;
                text.textContent = originalText;
                marcarButton.disabled = false;
            }
        } else {
            showToast('Erro: Botão não encontrado', 'error');
        }
    }

    // Fechar modal ao clicar fora
    document.getElementById('usoInternoModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeUsoInternoModal();
        }
    });

    // Fechar modal com ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (!document.getElementById('usoInternoModal').classList.contains('hidden')) {
                closeUsoInternoModal();
            }
            if (!document.getElementById('removerUsoInternoModal').classList.contains('hidden')) {
                closeRemoverUsoInternoModal();
            }
        }
    });

    // ============================================
    // REMOVER USO INTERNO - MODAL
    // ============================================

    let currentRemoverStockId = null;
    let currentRemoverTargetStatus = null;
    let currentRemoverButton = null;

    function openRemoverUsoInternoModal(stockId, targetStatus, button) {
        currentRemoverStockId = stockId;
        currentRemoverTargetStatus = targetStatus;
        currentRemoverButton = button;

        const modal = document.getElementById('removerUsoInternoModal');

        // Portal: mover modal para o body (garantir que fique acima de tudo)
        if (modal.parentNode !== document.body) {
            document.body.appendChild(modal);
        }

        modal.classList.remove('hidden');
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeRemoverUsoInternoModal() {
        const modal = document.getElementById('removerUsoInternoModal');
        modal.classList.add('hidden');
        modal.classList.remove('active');
        document.body.style.overflow = '';

        currentRemoverStockId = null;
        currentRemoverTargetStatus = null;
        currentRemoverButton = null;
    }

    async function confirmarRemoverUsoInterno() {
        // IMPORTANTE: Armazenar valores ANTES de fechar a modal
        const stockId = currentRemoverStockId;
        const targetStatus = currentRemoverTargetStatus;
        const removerButton = currentRemoverButton;

        // Fechar modal
        closeRemoverUsoInternoModal();

        if (!removerButton) return;

        // Desabilitar botão durante requisição
        removerButton.disabled = true;
        const icon = removerButton.querySelector('i');
        const text = removerButton.querySelector('span');
        const originalIconClass = icon.className;
        const originalText = text.textContent;

        icon.className = 'fas fa-spinner fa-spin';
        text.textContent = 'Processando...';

        try {
            const response = await fetch('/admin/proxy/remover-uso-interno', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({ stock_id: stockId })
            });

            const data = await response.json();

            if (data.success) {
                // Atualizar status visual
                if (targetStatus) {
                    targetStatus.dataset.status = 'disponivel';
                    targetStatus.innerHTML = '<span class="text-green-600 px-2 py-0.5 rounded">Disponível</span>';
                }

                showToast('Proxy removida do uso interno e voltou ao estoque', 'success');

                // Recarregar página após 1.5s
                setTimeout(() => window.location.reload(), 1500);
            } else {
                showToast(data.error || 'Erro ao remover uso interno', 'error');
                icon.className = originalIconClass;
                text.textContent = originalText;
                removerButton.disabled = false;
            }
        } catch (error) {
            console.error('Erro:', error);
            showToast('Erro ao conectar com o servidor', 'error');
            icon.className = originalIconClass;
            text.textContent = originalText;
            removerButton.disabled = false;
        }
    }

    // Fechar modal ao clicar fora
    document.getElementById('removerUsoInternoModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeRemoverUsoInternoModal();
        }
    });

    document.addEventListener('click', async function(e) {
        // MARCAR COMO USO INTERNO
        const marcarButton = e.target.closest('[data-action="marcar-uso-interno"]');
        if (marcarButton) {
            e.preventDefault();

            const stockId = marcarButton.dataset.stockId;
            const targetStatus = document.querySelector(marcarButton.dataset.target);

            // Abrir modal customizado ao invés de prompt
            openUsoInternoModal(stockId, targetStatus, marcarButton);
        }

        // REMOVER USO INTERNO
        const removerButton = e.target.closest('[data-action="remover-uso-interno"]');
        if (removerButton) {
            e.preventDefault();

            const stockId = removerButton.dataset.stockId;
            const targetStatus = document.querySelector(removerButton.dataset.target);

            // Abrir modal de confirmação ao invés de confirm()
            openRemoverUsoInternoModal(stockId, targetStatus, removerButton);
        }
    });

    //MASCARA DE VALOR

        //MASCARA DE VALOR

        document.addEventListener('DOMContentLoaded', function() {
    const inputMask = document.getElementById('vps_valor_mask');
    const inputMaskRenovacao = document.getElementById('vps_valor_renovacao_mask');
    const inputReal = document.getElementById('vps_valor_real');
    const inputRealRenovacao = document.getElementById('vps_valor_renovacao_real');

    // Máscara para o campo de valor da VPS
    if (inputMask && inputReal) {
        inputMask.addEventListener('input', function(e) {
            // Remove tudo que não é dígito
            let value = e.target.value.replace(/\D/g, '');

            if (value === '') {
                inputReal.value = '';
                e.target.value = '';
                return;
            }

            // Transforma em decimal (ex: 1500 -> 15.00)
            const floatValue = (parseInt(value) / 100).toFixed(2);

            // Atualiza o input hidden (valor que o PHP vai ler)
            inputReal.value = floatValue;

            // Formata para exibição (ex: 1.500,00)
            e.target.value = new Intl.NumberFormat('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(floatValue);
        });
    }

    // Máscara para o campo de valor de renovação da VPS
    if (inputMaskRenovacao && inputRealRenovacao) {
        inputMaskRenovacao.addEventListener('input', function(e) {
            // Remove tudo que não é dígito
            let value = e.target.value.replace(/\D/g, '');

            if (value === '') {
                inputRealRenovacao.value = '';
                e.target.value = '';
                return;
            }

            // Transforma em decimal (ex: 1500 -> 15.00)
            const floatValue = (parseInt(value) / 100).toFixed(2);

            // Atualiza o input hidden (valor que o PHP vai ler)
            inputRealRenovacao.value = floatValue;

            // Formata para exibição (ex: 1.500,00)
            e.target.value = new Intl.NumberFormat('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(floatValue);
        });
    }
});

    // ============================================
    // EDITAR APELIDO DA VPS
    // ============================================

    document.addEventListener('click', async function(e) {
        const editButton = e.target.closest('[data-edit-apelido]');
        if (!editButton) return;

        e.preventDefault();

        const vpsId = editButton.dataset.editApelido;
        const apelidoElement = document.getElementById(`vps-apelido-${vpsId}`);
        const currentApelido = apelidoElement.textContent.trim();

        // Criar input para edição inline
        const input = document.createElement('input');
        input.type = 'text';
        input.value = currentApelido;
        input.className = 'text-xl lg:text-2xl font-black text-slate-900 tracking-tight border-2 border-blue-500 rounded-lg px-3 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500';
        input.style.maxWidth = '100%';

        // Substituir h3 por input
        apelidoElement.replaceWith(input);
        input.focus();
        input.select();

        // Função para salvar
        const saveApelido = async () => {
            const novoApelido = input.value.trim();

            // Se não mudou, apenas restaurar
            if (novoApelido === currentApelido || novoApelido === '') {
                const h3 = document.createElement('h3');
                h3.id = `vps-apelido-${vpsId}`;
                h3.className = 'text-xl lg:text-2xl font-black text-slate-900 truncate tracking-tight';
                h3.textContent = currentApelido;
                input.replaceWith(h3);
                return;
            }

            // Desabilitar input durante salvamento
            input.disabled = true;
            input.className = input.className.replace('border-blue-500', 'border-gray-300');

            try {
                const response = await fetch('{{ route("vps.atualizar-apelido") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify({
                        vps_id: vpsId,
                        apelido: novoApelido
                    })
                });

                const data = await response.json();

                if (data.success) {
                    // Restaurar h3 com novo apelido
                    const h3 = document.createElement('h3');
                    h3.id = `vps-apelido-${vpsId}`;
                    h3.className = 'text-xl lg:text-2xl font-black text-slate-900 truncate tracking-tight';
                    h3.textContent = data.apelido;
                    input.replaceWith(h3);

                    // Mostrar notificação de sucesso
                    showToast('Apelido atualizado com sucesso!', 'success');

                    // Atualizar também no card da lista (se existir)
                    const cardApelido = document.querySelector(`[data-open-vps-modal="vpsModal-${vpsId}"] p.text-lg`);
                    if (cardApelido) {
                        cardApelido.textContent = data.apelido;
                    }
                } else {
                    // Restaurar valor anterior
                    const h3 = document.createElement('h3');
                    h3.id = `vps-apelido-${vpsId}`;
                    h3.className = 'text-xl lg:text-2xl font-black text-slate-900 truncate tracking-tight';
                    h3.textContent = currentApelido;
                    input.replaceWith(h3);

                    showToast(data.error || 'Erro ao atualizar apelido', 'error');
                }
            } catch (error) {
                console.error('Erro ao atualizar apelido:', error);

                // Restaurar valor anterior
                const h3 = document.createElement('h3');
                h3.id = `vps-apelido-${vpsId}`;
                h3.className = 'text-xl lg:text-2xl font-black text-slate-900 truncate tracking-tight';
                h3.textContent = currentApelido;
                input.replaceWith(h3);

                showToast('Erro ao conectar com o servidor', 'error');
            }
        };

        // Salvar ao pressionar Enter
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                saveApelido();
            } else if (e.key === 'Escape') {
                // Cancelar edição
                const h3 = document.createElement('h3');
                h3.id = `vps-apelido-${vpsId}`;
                h3.className = 'text-xl lg:text-2xl font-black text-slate-900 truncate tracking-tight';
                h3.textContent = currentApelido;
                input.replaceWith(h3);
            }
        });

        // Salvar ao perder o foco
        input.addEventListener('blur', saveApelido);
    });

    // ============================================
    // EDITAR PAÍS DA VPS
    // ============================================

    document.addEventListener('click', async function(e) {
        const editButton = e.target.closest('[data-edit-pais]');
        if (!editButton) return;

        e.preventDefault();

        const vpsId = editButton.dataset.editPais;
        const paisElement = document.getElementById(`vps-pais-${vpsId}`);
        const currentPais = paisElement.textContent.trim();

        // Lista de países disponíveis
        const paises = [
            'Brasil',
            'Estados Unidos',
            'Reino Unido',
            'Alemanha',
            'França',
            'Itália',
            'Espanha',
            'Portugal',
            'Canadá',
            'Austrália'
        ];

        // Criar select para edição inline
        const select = document.createElement('select');
        select.className = 'text-xs lg:text-sm text-slate-600 font-medium border-2 border-blue-500 rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white';

        // Adicionar opções ao select
        paises.forEach(pais => {
            const option = document.createElement('option');
            option.value = pais;
            option.textContent = pais;
            if (pais === currentPais) {
                option.selected = true;
            }
            select.appendChild(option);
        });

        // Substituir span por select
        paisElement.replaceWith(select);
        select.focus();

        // Função para salvar
        const savePais = async () => {
            const novoPais = select.value.trim();

            // Se não mudou, apenas restaurar
            if (novoPais === currentPais) {
                const span = document.createElement('span');
                span.id = `vps-pais-${vpsId}`;
                span.className = 'inline-flex items-center gap-1 cursor-pointer hover:text-slate-600 transition-colors';
                span.setAttribute('data-edit-pais', vpsId);
                span.setAttribute('title', 'Clique para editar o país');
                span.innerHTML = `${currentPais} <i class="fas fa-pen text-[8px] opacity-50"></i>`;
                select.replaceWith(span);
                return;
            }

            // Desabilitar select durante salvamento
            select.disabled = true;
            select.className = select.className.replace('border-blue-500', 'border-gray-300');

            try {
                const response = await fetch('{{ route("vps.atualizar-pais") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify({
                        vps_id: vpsId,
                        pais: novoPais
                    })
                });

                const data = await response.json();

                if (data.success) {
                    // Restaurar span com novo país
                    const span = document.createElement('span');
                    span.id = `vps-pais-${vpsId}`;
                    span.className = 'inline-flex items-center gap-1 cursor-pointer hover:text-slate-600 transition-colors';
                    span.setAttribute('data-edit-pais', vpsId);
                    span.setAttribute('title', 'Clique para editar o país');
                    span.innerHTML = `${data.pais} <i class="fas fa-pen text-[8px] opacity-50"></i>`;
                    select.replaceWith(span);

                    // Mostrar notificação de sucesso
                    showToast('País atualizado com sucesso!', 'success');

                    // Atualizar também no card da lista (se existir)
                    const cardPais = document.querySelector(`[data-open-vps-modal="vpsModal-${vpsId}"] .text-slate-400`);
                    if (cardPais) {
                        // Atualizar apenas o país, mantendo os outros elementos
                        const cardText = cardPais.textContent;
                        if (cardText.includes('·')) {
                            const parts = cardText.split('·').map(part => part.trim());
                            parts[0] = data.pais; // Assumindo que o país é a primeira parte
                            cardPais.textContent = parts.join(' · ');
                        }
                    }
                } else {
                    // Restaurar valor anterior
                    const span = document.createElement('span');
                    span.id = `vps-pais-${vpsId}`;
                    span.className = 'inline-flex items-center gap-1 cursor-pointer hover:text-slate-600 transition-colors';
                    span.setAttribute('data-edit-pais', vpsId);
                    span.setAttribute('title', 'Clique para editar o país');
                    span.innerHTML = `${currentPais} <i class="fas fa-pen text-[8px] opacity-50"></i>`;
                    select.replaceWith(span);

                    showToast(data.error || 'Erro ao atualizar país', 'error');
                }
            } catch (error) {
                console.error('Erro ao atualizar país:', error);

                // Restaurar valor anterior
                const span = document.createElement('span');
                span.id = `vps-pais-${vpsId}`;
                span.className = 'inline-flex items-center gap-1 cursor-pointer hover:text-slate-600 transition-colors';
                span.setAttribute('data-edit-pais', vpsId);
                span.setAttribute('title', 'Clique para editar o país');
                span.innerHTML = `${currentPais} <i class="fas fa-pen text-[8px] opacity-50"></i>`;
                select.replaceWith(span);

                showToast('Erro ao conectar com o servidor', 'error');
            }
        };

        // Salvar ao mudar a seleção
        select.addEventListener('change', savePais);

        // Cancelar edição ao pressionar Escape
        select.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const span = document.createElement('span');
                span.id = `vps-pais-${vpsId}`;
                span.className = 'inline-flex items-center gap-1 cursor-pointer hover:text-slate-600 transition-colors';
                span.setAttribute('data-edit-pais', vpsId);
                span.setAttribute('title', 'Clique para editar o país');
                span.innerHTML = `${currentPais} <i class="fas fa-pen text-[8px] opacity-50"></i>`;
                select.replaceWith(span);
            }
        });

        // Salvar ao perder o foco
        select.addEventListener('blur', savePais);
    });

    // ============================================
    // MODAL DE EDIÇÃO DE VPS
    // ============================================

    // Abrir modal de edição de VPS
    document.addEventListener('click', function(e) {
        // Botão de edição de VPS
        const editBtn = e.target.closest('.edit-vps-btn');
        if (editBtn) {
            e.preventDefault();
            e.stopPropagation();
            const vpsId = editBtn.dataset.vpsId;
            const modal = document.getElementById(`editVpsModal-${vpsId}`);
            if (modal) {
                // Portal para o body
                if (modal.parentNode !== document.body) {
                    document.body.appendChild(modal);
                }
                modal.classList.remove('hidden');
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';

                // Inicializar máscaras de valor para este modal
                initEditVpsMasks(`editVpsModal-${vpsId}`);
            }
            return;
        }

        // Fechar modal de edição de VPS
        const closeBtn = e.target.closest('[data-close-edit-vps-modal]');
        if (closeBtn) {
            const modal = closeBtn.closest('[data-edit-vps-modal]');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('active');
                document.body.style.overflow = '';
            }
            return;
        }

        // Fechar ao clicar no overlay
        if (e.target && e.target.matches('[data-edit-vps-modal]')) {
            e.target.classList.add('hidden');
            e.target.classList.remove('active');
            document.body.style.overflow = '';
        }
    });

    // Fechar com ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const openModals = document.querySelectorAll('[data-edit-vps-modal]:not(.hidden)');
            openModals.forEach(modal => {
                modal.classList.add('hidden');
                modal.classList.remove('active');
                document.body.style.overflow = '';
            });
        }
    });

    // Inicializar máscaras de valor para o modal de edição
    function initEditVpsMasks(modalId) {
        const vpsId = modalId.replace('editVpsModal-', '');

        const valorMask = document.getElementById(`edit_valor_mask_${vpsId}`);
        const valorReal = document.getElementById(`edit_valor_real_${vpsId}`);
        const renovacaoMask = document.getElementById(`edit_valor_renovacao_mask_${vpsId}`);
        const renovacaoReal = document.getElementById(`edit_valor_renovacao_real_${vpsId}`);

        // Máscara para valor
        if (valorMask && valorReal && !valorMask.dataset.maskInitialized) {
            valorMask.dataset.maskInitialized = 'true';
            valorMask.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value === '') {
                    valorReal.value = '';
                    e.target.value = '';
                    return;
                }
                const floatValue = (parseInt(value) / 100).toFixed(2);
                valorReal.value = floatValue;
                e.target.value = new Intl.NumberFormat('pt-BR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(floatValue);
            });
        }

        // Máscara para valor de renovação
        if (renovacaoMask && renovacaoReal && !renovacaoMask.dataset.maskInitialized) {
            renovacaoMask.dataset.maskInitialized = 'true';
            renovacaoMask.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value === '') {
                    renovacaoReal.value = '';
                    e.target.value = '';
                    return;
                }
                const floatValue = (parseInt(value) / 100).toFixed(2);
                renovacaoReal.value = floatValue;
                e.target.value = new Intl.NumberFormat('pt-BR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(floatValue);
            });
        }
    }

    // Submeter formulário de edição de VPS
    async function submitEditVps(event, vpsId) {
        event.preventDefault();

        const form = document.getElementById(`editVpsForm-${vpsId}`);
        const modal = document.getElementById(`editVpsModal-${vpsId}`);
        const submitBtn = form.querySelector('button[type="submit"]');

        // Coletar dados do formulário
        const formData = {
            vps_id: vpsId,
            valor: document.getElementById(`edit_valor_real_${vpsId}`).value,
            valor_renovacao: document.getElementById(`edit_valor_renovacao_real_${vpsId}`).value || null,
            data_contratacao: form.querySelector('[name="data_contratacao"]').value,
            periodo_dias: form.querySelector('[name="periodo_dias"]').value,
            hospedagem: form.querySelector('[name="hospedagem"]').value,
            status: form.querySelector('[name="status"]').value
        };

        // Desabilitar botão durante o envio
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Salvando...';

        try {
            const response = await fetch('{{ route("vps.atualizar") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify(formData)
            });

            const data = await response.json();

            if (data.success) {
                showToast('VPS atualizada com sucesso!', 'success');

                // Fechar modal
                modal.classList.add('hidden');
                modal.classList.remove('active');
                document.body.style.overflow = '';

                // Recarregar página após 1s para atualizar os dados
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showToast(data.error || 'Erro ao atualizar VPS', 'error');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-save"></i> Salvar Alterações';
            }
        } catch (error) {
            console.error('Erro ao atualizar VPS:', error);
            showToast('Erro ao conectar com o servidor', 'error');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-save"></i> Salvar Alterações';
        }
    }

    // ============================================
    // PESQUISA DE PROXIES
    // ============================================

    // Construir índice de proxies a partir do HTML (usando data attributes nos cards de proxy)
    function buildProxyIndex() {
        const index = [];

        @foreach($vpsFarm as $farm)
            @foreach($farm->proxies as $proxy)
                index.push({
                    id: {{ $proxy->id }},
                    vpsId: {{ $farm->id }},
                    vpsApelido: "{{ $farm->apelido }}",
                    ip: "{{ $proxy->ip }}",
                    porta: "{{ $proxy->porta }}",
                    usuario: "{{ $proxy->usuario }}",
                    senha: "{{ $proxy->senha }}",
                    status: "{{ $proxy->bloqueada ? 'bloqueada' : ($proxy->uso_interno ? 'uso_interno' : ($proxy->disponibilidade ? 'disponivel' : 'vendida')) }}",
                    bloqueada: {{ $proxy->bloqueada ? 'true' : 'false' }},
                    expiracao: "{{ $proxy->expiracao ? $proxy->expiracao->format('d/m/Y') : 'N/A' }}"
                });
            @endforeach
        @endforeach

        return index;
    }

    const proxyIndex = buildProxyIndex();

    // Elementos
    const proxySearchInput = document.getElementById('proxySearch');
    const clearSearchBtn = document.getElementById('clearProxySearch');
    const searchResults = document.getElementById('proxySearchResults');
    const searchResultsList = document.getElementById('searchResultsList');
    const searchResultCount = document.getElementById('searchResultCount');

    // Debounce para melhorar performance
    let searchTimeout;

    proxySearchInput?.addEventListener('input', function(e) {
        const query = e.target.value.trim();

        // Mostrar/ocultar botão de limpar
        if (query) {
            clearSearchBtn.classList.remove('hidden');
        } else {
            clearSearchBtn.classList.add('hidden');
            searchResults.classList.add('hidden');
            return;
        }

        // Debounce
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            performSearch(query);
        }, 300);
    });

    // Limpar pesquisa
    clearSearchBtn?.addEventListener('click', function() {
        proxySearchInput.value = '';
        clearSearchBtn.classList.add('hidden');
        searchResults.classList.add('hidden');
    });

    function performSearch(query) {
        if (!query || query.length < 2) {
            searchResults.classList.add('hidden');
            return;
        }

        const queryLower = query.toLowerCase();

        // Filtrar proxies
        const results = proxyIndex.filter(proxy => {
            return proxy.ip.toLowerCase().includes(queryLower) ||
                   proxy.porta.toString().includes(queryLower) ||
                   proxy.usuario.toLowerCase().includes(queryLower) ||
                   proxy.senha.toLowerCase().includes(queryLower);
        });

        // Atualizar UI
        searchResultCount.textContent = results.length;

        if (results.length === 0) {
            searchResultsList.innerHTML = `
                <div class="text-center py-6 text-slate-500">
                    <i class="fas fa-search text-3xl mb-2"></i>
                    <p>Nenhuma proxy encontrada para "${escapeHtml(query)}"</p>
                </div>
            `;
        } else {
            searchResultsList.innerHTML = results.map(proxy => {
                const statusInfo = getProxyStatusInfo(proxy.status);

                return `
                    <button
                        type="button"
                        class="w-full text-left px-4 py-3 rounded-lg bg-white border border-slate-200 hover:border-blue-400 hover:shadow-md transition-all proxy-search-result"
                        data-vps-id="${proxy.vpsId}"
                        data-proxy-id="${proxy.id}"
                    >
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-sm font-bold text-slate-900">${escapeHtml(proxy.ip)}:${escapeHtml(proxy.porta)}</span>
                                    <span class="px-2 py-0.5 rounded text-xs font-semibold ${statusInfo.class}">
                                        ${statusInfo.text}
                                    </span>
                                </div>
                                <div class="text-xs text-slate-500">
                                    <span class="font-medium">Usuário:</span> ${escapeHtml(proxy.usuario)} •
                                    <span class="font-medium">Senha:</span> ${escapeHtml(proxy.senha)}
                                </div>
                                <div class="text-xs text-slate-400 mt-1">
                                    <i class="fas fa-server mr-1"></i> ${escapeHtml(proxy.vpsApelido)}
                                    ${proxy.expiracao !== 'N/A' ? `<span class="ml-2"><i class="fas fa-calendar mr-1"></i>${proxy.expiracao}</span>` : ''}
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <i class="fas fa-arrow-right text-blue-500"></i>
                            </div>
                        </div>
                    </button>
                `;
            }).join('');

            // Adicionar event listeners aos resultados
            document.querySelectorAll('.proxy-search-result').forEach(btn => {
                btn.addEventListener('click', function() {
                    const vpsId = this.dataset.vpsId;
                    const proxyId = this.dataset.proxyId;
                    openVpsModalAndFocusProxy(vpsId, proxyId);
                });
            });
        }

        searchResults.classList.remove('hidden');
    }

    function getProxyStatusInfo(status) {
        switch(status) {
            case 'disponivel':
                return { text: 'Disponível', class: 'bg-green-100 text-green-800' };
            case 'bloqueada':
                return { text: 'Bloqueada', class: 'bg-red-100 text-red-800' };
            case 'uso_interno':
                return { text: 'Uso Interno', class: 'bg-indigo-100 text-indigo-800' };
            case 'vendida':
                return { text: 'Vendida', class: 'bg-blue-100 text-blue-800' };
            default:
                return { text: 'N/A', class: 'bg-slate-100 text-slate-800' };
        }
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function openVpsModalAndFocusProxy(vpsId, proxyId) {
        // Fechar modal de pesquisa (se houver alguma aberta)
        searchResults.classList.add('hidden');

        // Abrir modal da VPS
        const modalId = `vpsModal-${vpsId}`;
        const modal = document.getElementById(modalId);

        if (!modal) {
            showToast('Erro: Modal da VPS não encontrada', 'error');
            return;
        }

        // Abrir modal
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        // Aguardar um pouco para a modal renderizar
        setTimeout(() => {
            // Encontrar o card da proxy dentro da modal
            const proxyCard = modal.querySelector(`[data-proxy-id="${proxyId}"]`);

            if (proxyCard) {
                // Scroll até o card
                proxyCard.scrollIntoView({ behavior: 'smooth', block: 'center' });

                // Highlight temporário
                proxyCard.style.transition = 'all 0.3s ease';
                proxyCard.style.backgroundColor = '#dbeafe'; // blue-100
                proxyCard.style.borderColor = '#3b82f6'; // blue-500
                proxyCard.style.boxShadow = '0 0 0 3px rgba(59, 130, 246, 0.3)';

                // Remover highlight após 2 segundos
                setTimeout(() => {
                    proxyCard.style.backgroundColor = '';
                    proxyCard.style.borderColor = '';
                    proxyCard.style.boxShadow = '';
                }, 2000);

                showToast('Proxy encontrada!', 'success');
            } else {
                showToast('Proxy encontrada na VPS. Procure visualmente pelo IP/porta.', 'info');
            }
        }, 300);
    }
</script>

<style>
    @keyframes progress {
        0% {
            transform: translateX(-100%);
        }

        50% {
            transform: translateX(0);
        }

        100% {
            transform: translateX(100%);
        }
    }

    /* ===================================
       RESPONSIVIDADE MOBILE & TABLET
    =================================== */

    /* Mobile - Até 640px */
    @media (max-width: 640px) {
        /* Modais VPS */
        .admin-modal.vps-modal {
            width: 95vw !important;
            max-height: calc(100vh - 2rem) !important;
        }

        .admin-modal.vps-modal .vps-modal-header {
            padding: 1.25rem !important;
        }

        .admin-modal.vps-modal .vps-modal-body {
            padding: 1.25rem !important;
        }

        /* Cards de proxy */
        .vps-proxy-card {
            flex-direction: column !important;
            gap: 1rem !important;
            padding: 1rem !important;
        }

        .vps-proxy-actions {
            width: 100% !important;
        }

        .vps-proxy-action-btn {
            min-width: 100% !important;
            font-size: 0.75rem !important;
            height: 40px !important;
        }

        /* Grid de cards VPS */
        .grid.sm\:grid-cols-2.xl\:grid-cols-3 {
            grid-template-columns: 1fr !important;
        }

        /* Grid dentro da modal */
        .grid.grid-cols-1.lg\:grid-cols-2.xl\:grid-cols-3 {
            grid-template-columns: 1fr !important;
        }

        /* Formulário de cadastro */
        .grid.md\:grid-cols-3 {
            grid-template-columns: 1fr !important;
        }

        /* Admin cards */
        .admin-card {
            padding: 1rem !important;
        }

        /* Títulos */
        h1 {
            font-size: 1.5rem !important;
        }

        h2 {
            font-size: 1.125rem !important;
        }

        h3 {
            font-size: 1rem !important;
        }

        /* Stats grid mobile */
        .grid.grid-cols-4 {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 0.5rem !important;
        }

        /* Toast container mobile */
        #toastContainer {
            right: 1rem !important;
            max-width: calc(100vw - 2rem) !important;
        }

        /* VPS meta mobile */
        .vps-meta {
            font-size: 9px !important;
        }

        /* Badges mobile */
        .badge-status {
            font-size: 9px !important;
            padding: 0.2rem 0.5rem !important;
        }

        /* Tabela admin mobile */
        .admin-table {
            display: block;
            overflow-x: auto;
            white-space: nowrap;
        }

        .admin-table th,
        .admin-table td {
            padding: 0.5rem !important;
            font-size: 0.75rem !important;
        }

        /* Form inputs mobile */
        .form-input {
            font-size: 0.875rem !important;
        }

        /* Modais menores mobile */
        #usoInternoModal .admin-modal,
        #removerUsoInternoModal .admin-modal,
        #loadingModal .admin-modal {
            max-width: 95vw !important;
            padding: 1.25rem !important;
        }

        /* Status panel mobile */
        #statusPanel .admin-card {
            padding: 1rem !important;
        }
    }

    /* Tablet - 641px até 1024px */
    @media (min-width: 641px) and (max-width: 1024px) {
        .admin-modal.vps-modal {
            width: 90vw !important;
        }

        .grid.xl\:grid-cols-3 {
            grid-template-columns: repeat(2, 1fr) !important;
        }

        .grid.grid-cols-1.lg\:grid-cols-2.xl\:grid-cols-3 {
            grid-template-columns: repeat(2, 1fr) !important;
        }

        .vps-proxy-card {
            flex-direction: column !important;
        }

        .vps-proxy-actions {
            width: 100% !important;
        }

        .vps-proxy-action-btn {
            min-width: 100% !important;
        }
    }

    /* Ajustes gerais para telas pequenas */
    @media (max-width: 768px) {
        /* Esconder colunas menos importantes */
        .admin-table th:nth-child(3),
        .admin-table td:nth-child(3) {
            display: none;
        }

        /* Checkboxes em coluna */
        .flex.items-center.gap-3.mb-4 {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 0.75rem !important;
        }

        .flex.items-center.gap-3.mb-4 label:last-child {
            margin-left: 0 !important;
        }
    }

    /* Touch devices */
    @media (hover: none) and (pointer: coarse) {
        .vps-proxy-action-btn,
        button,
        .admin-card button {
            min-height: 44px !important;
        }

        .form-input,
        select {
            min-height: 44px !important;
        }
    }
</style>