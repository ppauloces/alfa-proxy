<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>AlfaProxy - Proxies</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@vite(['resources/css/app.css'])
<style>
:root {
    --sf-dark: #0f172a;
    --sf-blue: #2055dd;
    --sf-blue-light: #4F8BFF;
    --sf-gray: #94a3b8;
}
body {
    font-family: 'Onest', 'Inter', system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
    background: linear-gradient(to right, #438ccb, #316fab, #306da8, #3066a0, #2a508a, #233a72);
    min-height: 100vh;
    margin: 0;
    color: var(--sf-dark);
}
.grid-overlay {
    position: fixed;
    inset: 0;
    pointer-events: none;
    background-image: linear-gradient(rgba(255,255,255,0.04) 1px, transparent 1px),
                      linear-gradient(90deg, rgba(255,255,255,0.04) 1px, transparent 1px);
    background-size: 60px 60px;
    opacity: 0.4;
}
header nav a {
    transition: color 0.3s ease, background 0.3s ease;
}
.dashboard-shell {
    position: relative;
    z-index: 1;
    min-height: 100vh;
    padding: 140px 1.5rem 2.5rem;
    display: flex;
    justify-content: center;
}
.dashboard-grid {
    width: 100%;
    max-width: 1200px;
    display: grid;
    gap: 2rem;
}
@media (min-width: 1024px) {
    .dashboard-grid {
        grid-template-columns: 260px minmax(0, 1fr);
        align-items: start;
    }
    .dashboard-grid.collapsed {
        grid-template-columns: 92px minmax(0, 1fr);
    }
}
.sidebar-card {
    background: rgba(255,255,255,0.96);
    border-radius: 32px;
    border: 1px solid rgba(226,232,240,0.9);
    padding: 1.5rem;
    box-shadow: 0 24px 80px rgba(15,23,42,0.1);
    transition: width 0.3s ease, padding 0.3s ease;
}
.sidebar-card.collapsed {
    width: 92px;
    padding: 1.25rem 1rem;
}
.sidebar-card.collapsed .account-info,
.sidebar-card.collapsed .nav-text,
.sidebar-card.collapsed .submenu,
.sidebar-card.collapsed .sidebar-footer,
.sidebar-card.collapsed .sidebar-title {
    display: none;
}
.sidebar-card.collapsed .nav-pill {
    justify-content: center;
}
.sidebar-card.collapsed .nav-pill svg {
    margin: 0;
}
.nav-pill {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    width: 100%;
    padding: 0.85rem 1rem;
    border-radius: 999px;
    border: none;
    background: transparent;
    color: #1e293b;
    font-size: 0.95rem;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.25s ease, color 0.25s ease;
}
.nav-pill svg {
    width: 20px;
    height: 20px;
    stroke: currentColor;
    stroke-width: 1.6;
}
.nav-pill:hover { background: rgba(148,163,184,0.18); }
.nav-pill.active {
    background: linear-gradient(120deg, var(--sf-blue-light), var(--sf-blue));
    color: #fff;
    box-shadow: 0 12px 30px rgba(32,85,221,0.25);
}
.nav-pill.active svg { color: #fff; }
.submenu {
    margin-top: 0.75rem;
    margin-left: 1.5rem;
    border-left: 1px solid rgba(148,163,184,0.35);
    padding-left: 1rem;
    display: grid;
    gap: 0.65rem;
}
.submenu a {
    color: #475569;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: color 0.2s ease;
}
.submenu a:hover { color: var(--sf-blue); }
.badge-pill {
    font-size: 0.7rem;
    padding: 0.15rem 0.55rem;
    border-radius: 999px;
    background: rgba(79,139,255,0.15);
    color: var(--sf-blue);
    font-weight: 500;
}
.sidebar-toggle {
    width: 100%;
    border-radius: 999px;
    border: 1px solid rgba(226,232,240,0.9);
    padding: 0.5rem 0.75rem;
    font-size: 0.8rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.4rem;
    color: #475569;
    background: #fff;
}
.sidebar-toggle:hover {
    border-color: var(--sf-blue);
    color: var(--sf-blue);
}
.main-card {
    background: rgba(248,250,252,0.96);
    border-radius: 32px;
    border: 1px solid rgba(226,232,240,0.8);
    padding: 2rem;
    box-shadow: 0 24px 80px rgba(15,23,42,0.1);
}
.tab-btn {
    padding: 0.6rem 1.4rem;
    border-radius: 999px;
    border: 1px solid transparent;
    background: rgba(255,255,255,0.7);
    color: #0f172a;
    font-weight: 600;
    transition: all 0.2s ease;
}
.tab-btn.active {
    background: linear-gradient(120deg, var(--sf-blue-light), var(--sf-blue));
    color: #fff;
    box-shadow: 0 12px 30px rgba(32,85,221,0.25);
}
.proxy-card {
    background: #fff;
    border-radius: 28px;
    border: 1px solid rgba(226,232,240,0.9);
    padding: 1.5rem;
    box-shadow: 0 20px 60px rgba(15,23,42,0.08);
}
.proxy-table { width: 100%; border-collapse: collapse; }
.proxy-table th {
    text-align: left;
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.15em;
    color: #94a3b8;
    padding-bottom: 0.65rem;
}
.proxy-table td {
    padding: 0.85rem 0;
    border-top: 1px solid rgba(226,232,240,0.85);
    font-size: 0.9rem;
}
.address-chip {
    font-family: 'JetBrains Mono', 'Fira Code', monospace;
    background: rgba(148,163,184,0.15);
    padding: 0.3rem 0.55rem;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.8rem;
}
.action-btn {
    border: 1px solid rgba(148,163,184,0.4);
    border-radius: 999px;
    padding: 0.35rem 0.9rem;
    font-size: 0.75rem;
    font-weight: 600;
    background: #fff;
    color: #0f172a;
    transition: all 0.2s ease;
}
.action-btn:hover {
    border-color: var(--sf-blue);
    color: var(--sf-blue);
}
.badge {
    padding: 0.3rem 0.65rem;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 600;
}
.badge-amber { background: rgba(251,191,36,0.18); color: #b45309; }
.badge-gray { background: rgba(148,163,184,0.2); color: #475569; }
.switch { position: relative; width: 42px; height: 24px; }
.switch input { opacity: 0; width: 0; height: 0; }
.slider {
    position: absolute;
    cursor: pointer;
    inset: 0;
    background-color: rgba(148,163,184,0.4);
    transition: 0.2s;
    border-radius: 999px;
}
.slider:before {
    content: "";
    position: absolute;
    height: 18px;
    width: 18px;
    left: 3px;
    top: 3px;
    background-color: white;
    transition: 0.2s;
    border-radius: 50%;
    box-shadow: 0 2px 6px rgba(15,23,42,0.15);
}
.switch input:checked + .slider { background-color: var(--sf-blue); }
.switch input:checked + .slider:before { transform: translateX(18px); }
.caret { transition: transform 0.2s ease; }
.caret.open { transform: rotate(180deg); }
    </style>
</head>
<body class="antialiased">
@php
$proxyGroups = $proxyGroups ?? [
    'proxy' => [
        [
            'ip' => '191.96.12.45',
            'port' => '8080',
            'user' => 'alpha_user',
            'password' => 'R3dh##92',
            'country' => 'Brasil',
            'country_code' => 'BR',
            'purchased_at' => '2024-10-02 14:30',
            'expires_at' => '2024-11-02',
            'remaining' => '21 dias',
            'auto_renew' => true,
        ],
        [
            'ip' => '89.187.180.14',
            'port' => '6500',
            'user' => 'stack_ops',
            'password' => 'alpha##44',
            'country' => 'Estados Unidos',
            'country_code' => 'US',
            'purchased_at' => '2024-09-20 08:12',
            'expires_at' => '2024-10-20',
            'remaining' => '8 dias',
            'auto_renew' => false,
        ],
    ],
];
$proxyGroups = array_intersect_key($proxyGroups, ['proxy' => true]);
if (empty($proxyGroups)) {
    $proxyGroups = ['proxy' => []];
}
@endphp

<div class="grid-overlay"></div>
<header class="absolute top-0 left-0 right-0 z-20 bg-transparent">
<div class="max-w-7xl mx-auto px-6 py-6 flex justify-between items-center gap-8">
    <div class="flex items-center">
        <img src="{!! asset('images/logoproxy.webp') !!}" alt="Logo AlfaProxy" class="h-12 w-auto">
    </div>
    <nav class="hidden md:flex items-center bg-white/15 backdrop-blur-xl border border-white/20 rounded-full px-6 py-3 shadow-lg shadow-black/5">
        <a href="{{ route('inicial') }}" class="px-6 py-2 rounded-full text-white hover:text-[#2055dd] transition-all hover:bg-white/15">Inicio</a>
        <a href="{{ route('inicial') }}" class="px-6 py-2 rounded-full text-white hover:text-[#2055dd] transition-all hover:bg-white/15">Planos</a>
        <a href="{{ route('inicial') }}" class="px-6 py-2 rounded-full text-white hover:text-[#2055dd] transition-all hover:bg-white/15">API</a>
        <a href="{{ route('duvidas.show') }}" class="px-6 py-2 rounded-full text-white hover:text-[#2055dd] transition-all hover:bg-white/15">Suporte</a>
    </nav>
    <div class="flex items-center gap-3">
        @auth
            <span class="flex items-center gap-2 text-white bg-white/15 border border-white/25 rounded-xl px-4 py-2 backdrop-blur-sm">
                <i class="fas fa-wallet"></i>
                Saldo: R$ {{ Auth::user()->saldo ?? '0,00' }}
            </span>
        @endauth
        <button class="md:hidden text-white text-2xl">
            <i class="fas fa-bars"></i>
        </button>
    </div>
</div>
</header>

<main class="dashboard-shell">
<div class="dashboard-grid">
    <aside class="sidebar-card" data-sidebar>
        <div class="flex items-center gap-3 mb-6">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#4F8BFF] to-[#2055dd] flex items-center justify-center text-white font-semibold">AP</div>
            <div class="account-info">
                <p class="text-sm text-slate-500">Conta logada</p>
                <p class="text-lg font-semibold text-slate-800">{{ Auth::user()->name ?? 'Usuario' }}</p>
            </div>
        </div>
        <button type="button" class="sidebar-toggle mb-4" id="sidebarToggle">
            <i class="fas fa-chevron-left text-xs"></i>
            Recolher
        </button>
        <p class="sidebar-title text-xs uppercase tracking-[0.35em] text-slate-400 mb-4">Navegacao</p>
        <div class="space-y-2">
            <button type="button" class="nav-pill active">
                <span class="flex items-center gap-3">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M4 7h16M4 12h16M4 17h10" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span class="nav-text">Proxies</span>
                </span>
            </button>
            <div>
                <button type="button" class="nav-pill" data-toggle="submenu" data-target="profileSubmenu" aria-expanded="false">
                    <span class="flex items-center gap-3">
                        <svg viewBox="0 0 24 24" fill="none">
                            <path d="M12 12a5 5 0 100-10 5 5 0 000 10z" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M4 21v-1a7 7 0 017-7h2a7 7 0 017 7v1" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span class="nav-text">Meu perfil</span>
                    </span>
                    <svg class="caret" viewBox="0 0 24 24" fill="none">
                        <path d="M6 9l6 6 6-6" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
                <div id="profileSubmenu" class="submenu hidden">
                    <a href="#">Ordens</a>
                    <a href="#">Nova compra</a>
                    <a href="#">Historico</a>
                    <a href="#">Carteira</a>
                    <a href="#" class="gap-2">Planos <span class="badge-pill">Em breve</span></a>
                    <a href="#" class="gap-2">Menu de afiliacao <span class="badge-pill">Em breve</span></a>
                </div>
            </div>
            <button type="button" class="nav-pill">
                <span class="flex items-center gap-3">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M12 9v6m-3-3h6" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M5 4h14v16H5z" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span class="nav-text">Tickets & Suporte</span>
                </span>
            </button>
            <button type="button" class="nav-pill">
                <span class="flex items-center gap-3">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M10 6h10M4 12h16M4 18h10" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span class="nav-text">Configuracoes</span>
                </span>
            </button>
        </div>
        <div class="sidebar-footer mt-10 p-4 rounded-2xl bg-gradient-to-br from-[#4F8BFF] to-[#2055dd] text-white shadow-lg shadow-blue-900/20">
            <p class="text-sm uppercase tracking-[0.2em] text-white/80">Upgrade</p>
            <p class="text-xl font-semibold mt-2">Plano Alfa+</p>
            <p class="text-sm text-white/80 mt-1">Mais IPs dedicados e suporte prioritario.</p>
            <a href="#" class="mt-4 inline-flex items-center gap-2 text-sm font-semibold">
                Ver planos
                <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>
    </aside>
    <section class="main-card space-y-8">
        <div class="flex flex-col gap-2">
            <p class="text-sm uppercase tracking-[0.35em] text-slate-500">Proxies ativos</p>
            <div class="flex flex-wrap items-center gap-4 justify-between">
                <h1 class="text-3xl font-bold text-slate-900">Gerencie seus IPs</h1>
                <div class="flex flex-wrap gap-3">
                    <button id="collapseSidebarBtn" class="px-4 py-2 rounded-2xl border border-slate-200 text-sm font-semibold text-slate-600 hover:border-slate-400 transition-colors">
                        Ocultar sidebar
                    </button>
                    <button class="px-5 py-2 rounded-2xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition-colors">
                        Comprar novos proxies
                    </button>
                </div>
            </div>
            <p class="text-slate-500 max-w-2xl">Veja o que falta para cada contratacao expirar, teste as rotas e controle a renovacao automatica.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            @foreach ($proxyGroups as $group => $proxies)
                <button type="button" class="tab-btn {{ $loop->first ? 'active' : '' }}" data-tab="{{ $group }}">
                    {{ \Illuminate\Support\Str::headline($group) }}
                    <span class="text-xs font-normal opacity-70">({{ count($proxies) }})</span>
                </button>
            @endforeach
        </div>
        @foreach ($proxyGroups as $group => $proxies)
            <div class="proxy-card {{ $loop->first ? '' : 'hidden' }}" data-tab-panel="{{ $group }}">
                <div class="flex flex-wrap justify-between items-center gap-4 mb-6">
                    <div>
                        <h2 class="text-xl font-semibold text-slate-900">{{ \Illuminate\Support\Str::headline($group) }}</h2>
                        <p class="text-sm text-slate-500">
                            @if(count($proxies))
                                {{ count($proxies) }} proxies listados abaixo.
                            @else
                                Nenhum proxy neste tipo ainda.
                            @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-slate-500">
                        <i class="fas fa-shield-alt text-[#4F8BFF]"></i>
                        Monitoramento ativo
                    </div>
                </div>
                @if(count($proxies))
                    <div class="overflow-x-auto">
                        <table class="proxy-table">
                            <thead>
                                <tr>
                                    <th>Endereco / acoes</th>
                                    <th>Pais</th>
                                    <th>Compra</th>
                                    <th>Expiracao</th>
                                    <th>Periodo</th>
                                    <th>Auto renovacao</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($proxies as $proxy)
                                    <tr>
                                        <td class="space-y-2">
                                            <div class="address-chip">
                                                {{ $proxy['ip'] }}:{{ $proxy['port'] }} | {{ $proxy['user'] }} | {{ $proxy['password'] }}
                                            </div>
                                            <div class="flex flex-wrap gap-2">
                                                <button class="action-btn">
                                                    <i class="fas fa-vial text-xs"></i>
                                                    Testar proxy
                                                </button>
                                                <button class="action-btn">
                                                    <i class="fas fa-copy text-xs"></i>
                                                    Copiar
                                                </button>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="flex items-center gap-2">
                                                <span class="text-xl">{{ $proxy['country_code'] ?? '🌐' }}</span>
                                                <span class="text-sm font-semibold text-slate-700">{{ $proxy['country'] }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-sm font-semibold text-slate-700">{{ \Carbon\Carbon::parse($proxy['purchased_at'])->format('d/m/Y') }}</p>
                                            <p class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($proxy['purchased_at'])->format('H:i') }}</p>
                                        </td>
                                        <td>
                                            <p class="text-sm font-semibold text-slate-700">{{ \Carbon\Carbon::parse($proxy['expires_at'])->format('d/m/Y') }}</p>
                                            <span class="badge badge-amber">Renovar</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-gray">{{ $proxy['remaining'] }}</span>
                                        </td>
                                        <td>
                                            <label class="switch">
                                                <input type="checkbox" {{ $proxy['auto_renew'] ? 'checked' : '' }}>
                                                <span class="slider"></span>
                                            </label>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="border border-dashed border-slate-200 rounded-2xl p-10 text-center">
                        <p class="text-lg font-semibold text-slate-700 mb-2">Nenhum proxy cadastrado neste tipo.</p>
                        <p class="text-sm text-slate-500 mb-4">Contrate um novo proxy para visualizar aqui.</p>
                        <a href="#" class="inline-flex items-center gap-2 px-5 py-2 rounded-2xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition-colors">
                            Ver planos disponiveis
                            <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                @endif
            </div>
        @endforeach
    </section>
</div>
</main>

<script>
const sidebar = document.querySelector('[data-sidebar]');
const dashboardGrid = document.querySelector('.dashboard-grid');
const toggleButtons = [document.getElementById('sidebarToggle'), document.getElementById('collapseSidebarBtn')];

const updateToggleLabels = (collapsed) => {
    toggleButtons.forEach(btn => {
        if (!btn) return;
        btn.innerHTML = collapsed
            ? '<i class="fas fa-chevron-right text-xs"></i> '
            : '<i class="fas fa-chevron-left text-xs"></i> Ocultar sidebar';
    });
};

const toggleSidebar = () => {
    const collapsed = sidebar.classList.toggle('collapsed');
    dashboardGrid.classList.toggle('collapsed', collapsed);
    updateToggleLabels(collapsed);
};
toggleButtons.forEach(btn => btn?.addEventListener('click', toggleSidebar));

updateToggleLabels(false);

document.querySelectorAll('[data-toggle="submenu"]').forEach(button => {
    button.addEventListener('click', () => {
        const target = document.getElementById(button.dataset.target);
        const caret = button.querySelector('.caret');
        target?.classList.toggle('hidden');
        caret?.classList.toggle('open');
        const expanded = button.getAttribute('aria-expanded') === 'true';
        button.setAttribute('aria-expanded', (!expanded).toString());
    });
});

const tabBtns = document.querySelectorAll('[data-tab]');
const tabPanels = document.querySelectorAll('[data-tab-panel]');

tabBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        const target = btn.dataset.tab;
        tabBtns.forEach(b => b.classList.remove('active'));
        tabPanels.forEach(panel => panel.classList.toggle('hidden', panel.dataset.tabPanel !== target));
        btn.classList.add('active');
    });
});
</script>
</body>
</html>