<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'AlfaProxy - Dashboard')</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/card/2.5.4/card.min.css">
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
    max-width: 1400px;
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
    text-decoration: none;
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
.submenu a,
.submenu button {
    color: #475569;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: color 0.2s ease;
    text-decoration: none;
    background: transparent;
    border: none;
    width: 100%;
    text-align: left;
    cursor: pointer;
    padding: 0;
    font: inherit;
}
.submenu a:hover,
.submenu button:hover { color: var(--sf-blue); }
.submenu a.active,
.submenu button.active { color: var(--sf-blue); font-weight: 600; }
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
    cursor: pointer;
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
.caret { transition: transform 0.2s ease; }
.caret.open { transform: rotate(180deg); }
@yield('styles')
</style>
</head>
<body class="antialiased">

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
                Saldo: R$ {{ number_format(Auth::user()->saldo ?? 0, 2, ',', '.') }}
            </span>

            <form action="{{ route('logout.perform') }}" method="GET" class="inline">
                <button type="submit" class="flex items-center gap-2 text-white bg-white/15 border border-white/25 rounded-xl px-4 py-2 backdrop-blur-sm hover:bg-white/25 transition-colors cursor-pointer">
                    <i class="fas fa-sign-out-alt"></i>
                    Sair
                </button>
            </form>
        @endauth'
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
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#4F8BFF] to-[#2055dd] flex items-center justify-center text-white font-semibold">
                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}
            </div>
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
            <button type="button" class="nav-pill" data-section-link="proxies">
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
                    <button type="button" data-section-link="perfil">Meu Perfil</button>
                    <button type="button" data-section-link="proxies">Ordens</button>
                    <button type="button" data-section-link="nova-compra">Nova compra</button>
                    <button type="button" data-section-link="transacoes">Historico</button>
                    <button type="button" data-section-link="saldo">Carteira</button>
                    <button type="button" data-section-link="cartoes">Meus Cartoes</button>
                    <span class="gap-2 flex items-center justify-between text-slate-400 text-sm">
                        Planos <span class="badge-pill">Em breve</span>
                    </span>
                    <span class="gap-2 flex items-center justify-between text-slate-400 text-sm">
                        Menu de afiliacao <span class="badge-pill">Em breve</span>
                    </span>
                </div>
            </div>
            <button type="button" class="nav-pill" data-section-link="suporte">
                <span class="flex items-center gap-3">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M12 9v6m-3-3h6" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M5 4h14v16H5z" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span class="nav-text">Tickets & Suporte</span>
                </span>
            </button>
            <button type="button" class="nav-pill" data-section-link="configuracoes">
                <span class="flex items-center gap-3">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M10 6h10M4 12h16M4 18h10" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span class="nav-text">Configuracoes</span>
                </span>
            </button>
        </div>

        @if(Auth::user()->isAdmin())
        <div class="space-y-2 mt-6 pt-6 border-t border-slate-200">
            <p class="sidebar-title text-xs uppercase tracking-[0.35em] text-slate-400 mb-4">Administracao</p>
            <button type="button" class="nav-pill" data-section-link="admin-dashboard">
                <span class="flex items-center gap-3">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span class="nav-text">Dashboard Admin</span>
                </span>
            </button>
            <div>
                <button type="button" class="nav-pill" data-toggle="submenu" data-target="adminSubmenu" aria-expanded="false">
                    <span class="flex items-center gap-3">
                        <svg viewBox="0 0 24 24" fill="none">
                            <path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span class="nav-text">Gerenciar</span>
                    </span>
                    <svg class="caret" viewBox="0 0 24 24" fill="none">
                        <path d="M6 9l6 6 6-6" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
                <div id="adminSubmenu" class="submenu hidden">
                    <button type="button" data-section-link="admin-usuarios">Usuarios</button>
                    <button type="button" data-section-link="admin-proxies">Proxies</button>
                    <button type="button" data-section-link="admin-transacoes">Transacoes</button>
                    <button type="button" data-section-link="admin-cupons">Cupons</button>
                </div>
            </div>
            <button type="button" class="nav-pill" data-section-link="admin-relatorios">
                <span class="flex items-center gap-3">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span class="nav-text">Relatorios</span>
                </span>
            </button>
        </div>
        @endif

    </aside>

    <section class="main-card space-y-8">
        @yield('content')
    </section>
</div>
</main>

<script>
const sidebar = document.querySelector('[data-sidebar]');
const dashboardGrid = document.querySelector('.dashboard-grid');
const toggleButtons = [document.getElementById('sidebarToggle')];

const updateToggleLabels = (collapsed) => {
    toggleButtons.forEach(btn => {
        if (!btn) return;
        btn.innerHTML = collapsed
            ? '<i class="fas fa-chevron-right text-xs"></i>'
            : '<i class="fas fa-chevron-left text-xs"></i> Recolher';
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

document.addEventListener('DOMContentLoaded', () => {
    const sectionLinks = Array.from(document.querySelectorAll('[data-section-link]'));
    const sections = Array.from(document.querySelectorAll('[data-section]'));

    if (!sectionLinks.length || !sections.length) {
        return;
    }

    const getInitialSection = () => {
        if (typeof window.initialDashSection === 'string' && window.initialDashSection.trim().length) {
            return window.initialDashSection;
        }
        const urlSection = new URL(window.location).searchParams.get('section');
        if (urlSection) {
            return urlSection;
        }
        return sections[0]?.dataset.section ?? null;
    };

    const setActiveSection = (sectionId) => {
        if (!sectionId) return;

        sections.forEach(section => {
            const isActive = section.dataset.section === sectionId;
            section.classList.toggle('hidden', !isActive);
            section.classList.toggle('active', isActive);
        });

        sectionLinks.forEach(link => {
            const isActive = link.dataset.sectionLink === sectionId;
            link.classList.toggle('active', isActive);

            if (isActive) {
                const parentSubmenu = link.closest('.submenu');
                if (parentSubmenu) {
                    parentSubmenu.classList.remove('hidden');
                    const toggleBtn = document.querySelector(`[data-target="${parentSubmenu.id}"]`);
                    toggleBtn?.setAttribute('aria-expanded', 'true');
                    toggleBtn?.querySelector('.caret')?.classList.add('open');
                }
            }
        });

        const url = new URL(window.location);
        url.searchParams.set('section', sectionId);
        window.history.replaceState({}, '', url);
    };

    sectionLinks.forEach(link => {
        link.addEventListener('click', (event) => {
            event.preventDefault();
            setActiveSection(link.dataset.sectionLink);
        });
    });

    setActiveSection(getInitialSection());
});

@yield('scripts')
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/card/2.5.4/card.min.js"></script>

</body>
</html>
