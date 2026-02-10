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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.30.1/min/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.30.1/locale/pt-br.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Meta Pixel Code -->
    <script>
        !function (f, b, e, v, n, t, s) {
            if (f.fbq) return; n = f.fbq = function () {
                n.callMethod ?
                    n.callMethod.apply(n, arguments) : n.queue.push(arguments)
            };
            if (!f._fbq) f._fbq = n; n.push = n; n.loaded = !0; n.version = '2.0';
            n.queue = []; t = b.createElement(e); t.async = !0;
            t.src = v; s = b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t, s)
        }(window, document, 'script',
            'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '1162827325630978');
        fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
            src="https://www.facebook.com/tr?id=1162827325630978&ev=PageView&noscript=1" /></noscript>
    <!-- End Meta Pixel Code -->
    @vite(['resources/css/app.css'])
    <style>
        :root {
            --sf-dark: #0f172a;
            --sf-blue: #23366f;
            --sf-blue-light: #448ccb;
            --sf-gray: #94a3b8;
        }

        body {
            font-family: 'Onest', 'Inter', system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f8fafc;
            min-height: 100vh;
            margin: 0;
            color: var(--sf-dark);
        }



        .grid-overlay {
            position: fixed;
            inset: 0;
            pointer-events: none;
            background-image: linear-gradient(rgba(255, 255, 255, 0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.04) 1px, transparent 1px);
            background-size: 60px 60px;
            opacity: 0.4;
        }

        header nav a {
            transition: color 0.3s ease, background 0.3s ease;
        }

        .dashboard-shell {
            position: relative;
            min-height: 100vh;
            padding: 100px 0 2.5rem;
            display: flex;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: #f8fafc;
        }

        @media (min-width: 1024px) {
            .dashboard-shell {
                padding-left: 260px;
            }

            .sidebar-collapsed .dashboard-shell {
                padding-left: 80px;
            }
        }

        .dashboard-grid {
            width: 100%;
            margin: 0;
            display: block;
        }

        .sidebar-card {
            background: #fff;
            width: 260px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 50;
            border-right: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 0;
            padding: 1.25rem 0.75rem;
            box-shadow: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
        }

        @media (max-width: 1023px) {
            .sidebar-card {
                transform: translateX(-100%);
            }

            .sidebar-card.active {
                transform: translateX(0);
            }
        }

        .sidebar-card.collapsed {
            width: 80px;
            padding: 1.25rem 0.5rem;
        }

        .sidebar-card.collapsed .account-info,
        .sidebar-card.collapsed .nav-text,
        .sidebar-card.collapsed .sidebar-footer,
        .sidebar-card.collapsed .sidebar-title {
            display: none;
        }

        .sidebar-card.collapsed .nav-pill {
            justify-content: center;
            padding: 0.85rem;
        }

        .sidebar-card.collapsed .nav-pill i {
            margin: 0;
            font-size: 1.1rem;
        }

        .nav-pill {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            width: 100%;
            padding: 0.5rem 0.75rem;
            border-radius: 8px;
            border: none;
            background: transparent;
            color: #64748b;
            font-size: 0.8125rem;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: color 0.2s ease, background 0.2s ease;
        }

        .nav-pill i {
            font-size: 0.875rem;
            transition: color 0.2s ease;
        }

        .nav-pill:hover {
            background: #f8fafc;
            color: #0f172a;
        }

        .nav-pill.active {
            background: #f1f5f9;
            color: var(--sf-blue);
            font-weight: 600;
        }

        .nav-pill.active i {
            color: var(--sf-blue);
        }

        .sidebar-toggle {
            width: 100%;
            border-radius: 8px;
            border: none;
            padding: 0.5rem;
            font-size: 0.75rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            color: #94a3b8;
            background: transparent;
            cursor: pointer;
            transition: color 0.2s ease, background 0.2s ease;
        }

        .sidebar-toggle:hover {
            background: #f8fafc;
            color: #475569;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #cbd5e1;
        }

        .main-card {
            background: transparent;
            border-radius: 0;
            border: none;
            padding: 2.5rem 3rem;
            box-shadow: none;
        }

        .caret {
            transition: transform 0.2s ease;
        }

        .caret.open {
            transform: rotate(180deg);
        }

        /* Estilos Globais para Modais */
        .admin-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(8px);
            display: none;
            z-index: 9998;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            animation: fadeIn 0.3s ease;
        }

        .admin-modal-overlay.active {
            display: flex;
        }

        .admin-modal {
            background: #fff;
            border-radius: 32px;
            padding: 2.5rem;
            box-shadow: 0 50px 100px rgba(15, 23, 42, 0.25);
            z-index: 9999;
            width: 100%;
            max-width: 600px;
            max-height: calc(100vh - 4rem);
            overflow-y: auto;
            position: relative;
            animation: modalSlide 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* ===================================
           RESPONSIVIDADE MOBILE & TABLET
        =================================== */

        /* Mobile - Até 640px */
        @media (max-width: 640px) {
            header .max-w-full {
                padding: 0 0.75rem !important;
                gap: 0.5rem !important;
            }

            header img {
                height: 1.5rem !important;
            }

            header nav {
                display: none !important;
            }

            header .flex.items-center.gap-2\.5 {
                gap: 0.375rem !important;
            }

            .dashboard-shell {
                padding: 80px 0 1.5rem !important;
            }

            .main-card {
                padding: 1.5rem 1rem !important;
            }

            .sidebar-card {
                width: 280px !important;
            }

            .admin-modal {
                padding: 1.5rem !important;
                border-radius: 24px !important;
            }

            .admin-modal-overlay {
                padding: 1rem !important;
            }

            /* Botões mobile */
            button {
                font-size: 0.875rem !important;
            }

            /* Badges mobile */
            .badge,
            .badge-amber,
            .badge-success,
            .badge-pending {
                font-size: 0.65rem !important;
                padding: 0.25rem 0.5rem !important;
            }
        }

        /* Tablet - 641px até 1024px */
        @media (min-width: 641px) and (max-width: 1024px) {
            .dashboard-shell {
                padding: 100px 0 2rem !important;
                padding-left: 0 !important;
            }

            .main-card {
                padding: 2rem 1.5rem !important;
            }

            header {
                left: 0 !important;
            }
        }

        /* Ajustes gerais para telas pequenas */
        @media (max-width: 768px) {

            .grid.md\:grid-cols-2,
            .grid.md\:grid-cols-3 {
                grid-template-columns: 1fr !important;
            }

            .lg\:col-span-2 {
                grid-column: span 1 !important;
            }
        }

        /* Landscape mobile */
        @media (max-height: 768px) and (orientation: landscape) {
            .dashboard-shell {
                padding-top: 70px !important;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes modalSlide {
            from {
                transform: translateY(20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        header {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 40;
            right: 0 !important;
        }

        @media (min-width: 1024px) {
            body:not(.sidebar-collapsed) header {
                left: 260px;
            }

            body.sidebar-collapsed header {
                left: 80px;
            }
        }

        @yield('styles')
    </style>
</head>

<body class="antialiased">

    <div class="grid-overlay"></div>

    <header class="fixed top-0 left-0 right-0 z-40 bg-white border-b border-slate-200/80">
        <div class="max-w-full mx-auto px-6 sm:px-8 py-0 flex justify-between items-center gap-6">
            <!-- LOGO -->
            <a href="{{ route('dash.show') }}"
                class="flex items-center shrink-0 py-3.5 hover:opacity-80 transition-opacity">
                <img src="{{ asset('images/logoproxy1.webp') }}" alt="AlfaProxy"
                    class="h-6 sm:h-7 lg:h-8 w-auto object-contain">
            </a>

            <div class="hidden xl:block w-[1px] h-7 bg-slate-200 shrink-0"></div>

            <nav class="hidden xl:flex items-center gap-1 flex-1">
                <a href="{{ route('inicial') }}"
                    class="px-4 py-1.5 rounded-lg text-slate-500 text-[13px] font-medium hover:text-slate-900 hover:bg-slate-50 transition-all">Inicio</a>
                <a href="{{ route('inicial') }}"
                    class="px-4 py-1.5 rounded-lg text-slate-500 text-[13px] font-medium hover:text-slate-900 hover:bg-slate-50 transition-all">Planos</a>
                <a href="{{ route('inicial') }}"
                    class="px-4 py-1.5 rounded-lg text-slate-500 text-[13px] font-medium hover:text-slate-900 hover:bg-slate-50 transition-all">API</a>
                <a href="{{ route('duvidas.show') }}"
                    class="px-4 py-1.5 rounded-lg text-slate-500 text-[13px] font-medium hover:text-slate-900 hover:bg-slate-50 transition-all">Suporte</a>
            </nav>

            <div class="flex items-center flex-1 xl:flex-none justify-end">
                <div class="xl:hidden flex-1"></div>
            </div>

            <div class="flex items-center gap-2.5">
                @auth
                    <div class="h-6 w-[1px] bg-slate-200 hidden sm:block"></div>

                    <form action="{{ route('logout.perform') }}" method="GET" class="inline">
                        <button type="submit"
                            class="p-2 rounded-lg text-slate-400 hover:text-red-500 hover:bg-red-50 transition-all cursor-pointer">
                            <i class="fas fa-sign-out-alt text-sm"></i>
                        </button>
                    </form>
                @endauth
                <button class="lg:hidden p-2 rounded-lg text-slate-500 hover:bg-slate-50 transition-all"
                    id="mobileMenuToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </header>

    <main class="dashboard-shell">
        <div class="dashboard-grid">
            <aside class="sidebar-card" data-sidebar>
                <div class="flex items-center gap-3 mb-6 px-2 pb-5 border-b border-slate-100">
                    <div class="relative">
                        <div
                            class="w-9 h-9 rounded-lg {{ Auth::user()->isRevendedor() ? 'bg-amber-500' : 'bg-[#23366f]' }} flex items-center justify-center text-white text-xs font-semibold">
                            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}{{ strtoupper(substr(explode(' ', Auth::user()->name ?? 'U')[1] ?? '', 0, 1)) }}
                        </div>
                        <div
                            class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-green-500 border-2 border-white rounded-full">
                        </div>
                    </div>
                    <div class="account-info min-w-0">
                        <p class="text-[13px] font-semibold text-slate-800 truncate max-w-[150px]">
                            {{ Auth::user()->name ?? 'Usuario' }}
                        </p>
                        <p class="text-[11px] text-slate-400 font-medium leading-tight">
                            {{ Auth::user()->isRevendedor() ? 'Revendedor' : 'Cliente' }}
                        </p>
                    </div>
                </div>

                <div class="space-y-5 flex-1 overflow-y-auto custom-scrollbar px-1">
                    {{-- Seção Geral --}}
                    <div>
                        <p
                            class="sidebar-title text-[11px] uppercase tracking-wider text-slate-400 font-medium mb-1.5 px-3">
                            Menu Principal</p>
                        <div class="space-y-0.5">
                            <button type="button" class="nav-pill" data-section-link="proxies">
                                <span class="flex items-center gap-3">
                                    <i class="fas fa-layer-group w-5 text-center"></i>
                                    <span class="nav-text">Meus Proxies</span>
                                </span>
                            </button>
                            <button type="button" class="nav-pill" data-section-link="nova-compra">
                                <span class="flex items-center gap-3">
                                    <i class="fas fa-shopping-cart w-5 text-center"></i>
                                    <span class="nav-text">Nova Compra</span>
                                </span>
                            </button>
                        </div>
                    </div>

                    {{-- Seção Financeiro --}}
                    <div>
                        <p
                            class="sidebar-title text-[11px] uppercase tracking-wider text-slate-400 font-medium mb-1.5 px-3">
                            Financeiro</p>
                        <div class="space-y-0.5">
                            <div class="nav-pill opacity-60 cursor-not-allowed">
                                <span class="flex items-center gap-3">
                                    <i class="fas fa-wallet w-5 text-center text-slate-400"></i>
                                    <span class="nav-text text-slate-400">Carteira / Saldo</span>
                                </span>
                                <span
                                    class="text-[9px] bg-slate-100 text-slate-400 px-1.5 py-0.5 rounded font-medium">Em
                                    breve</span>
                            </div>
                            <button type="button" class="nav-pill" data-section-link="transacoes">
                                <span class="flex items-center gap-3">
                                    <i class="fas fa-history w-5 text-center"></i>
                                    <span class="nav-text">Extrato</span>
                                </span>
                            </button>
                            <button type="button" class="nav-pill" data-section-link="cartoes">
                                <span class="flex items-center gap-3">
                                    <i class="fas fa-credit-card w-5 text-center text-slate-400"></i>
                                    <span class="nav-text">Meus Cartões</span>

                                </span>
                            </button>
                        </div>
                    </div>

                    {{-- Seção Suporte e Conta --}}
                    <div>
                        <p
                            class="sidebar-title text-[11px] uppercase tracking-wider text-slate-400 font-medium mb-1.5 px-3">
                            Atendimento & Conta</p>
                        <div class="space-y-0.5">
                            <button type="button" class="nav-pill" data-section-link="suporte">
                                <span class="flex items-center gap-3">
                                    <i class="fas fa-headset w-5 text-center"></i>
                                    <span class="nav-text">Suporte</span>
                                </span>
                            </button>
                            <button type="button" class="nav-pill" data-section-link="perfil">
                                <span class="flex items-center gap-3">
                                    <i class="fas fa-user-circle w-5 text-center"></i>
                                    <span class="nav-text">Meu Perfil</span>
                                </span>
                            </button>
                            <!-- <button type="button" class="nav-pill" data-section-link="configuracoes">
                                <span class="flex items-center gap-3">
                                    <i class="fas fa-cog w-5 text-center"></i>
                                    <span class="nav-text">Configurações</span>
                                </span>
                            </button> -->
                        </div>
                    </div>

                    @if(Auth::user()->isAdmin())
                        {{-- Seção Admin --}}
                        <div class="pt-4 border-t border-slate-100">
                            <p
                                class="sidebar-title text-[11px] uppercase tracking-wider text-red-400/80 font-medium mb-1.5 px-3">
                                Administração</p>
                            <div class="space-y-0.5">
                                <button type="button" class="nav-pill" data-section-link="admin-usuarios">
                                    <span class="flex items-center gap-3">
                                        <i class="fas fa-users w-5 text-center"></i>
                                        <span class="nav-text">Usuários</span>
                                    </span>
                                </button>
                                <button type="button" class="nav-pill" data-section-link="admin-proxies">
                                    <span class="flex items-center gap-3">
                                        <i class="fas fa-server w-5 text-center"></i>
                                        <span class="nav-text">VPS</span>
                                    </span>
                                </button>
                                <button type="button" class="nav-pill" data-section-link="admin-historico-vps">
                                    <span class="flex items-center gap-3">
                                        <i class="fas fa-history w-5 text-center"></i>
                                        <span class="nav-text">Histórico de VPS</span>
                                    </span>
                                </button>
                            </div>
                            @if(Auth::user()->isSuperAdmin())
                                <button type="button" class="nav-pill" data-section-link="admin-colaboradores">
                                    <span class="flex items-center gap-3">
                                        <i class="fas fa-user-shield w-5 text-center"></i>
                                        <span class="nav-text">Colaboradores</span>
                                    </span>
                                </button>
                                <button type="button" class="nav-pill" data-section-link="admin-financeiro">
                                    <span class="flex items-center gap-3">
                                        <i class="fas fa-dollar-sign w-5 text-center"></i>
                                        <span class="nav-text">Financeiro</span>
                                    </span>
                                </button>
                                <button type="button" class="nav-pill" data-section-link="admin-transacoes">
                                    <span class="flex items-center gap-3">
                                        <i class="fas fa-exchange-alt w-5 text-center"></i>
                                        <span class="nav-text">Transações</span>
                                    </span>
                                </button>
                                <button type="button" class="nav-pill" data-section-link="admin-relatorios">
                                    <span class="flex items-center gap-3">
                                        <i class="fas fa-chart-bar w-5 text-center"></i>
                                        <span class="nav-text">Relatórios</span>
                                    </span>
                                </button>
                            @endif
                        </div>
                    @endif

                </div>

                <div class="mt-auto pt-4 border-t border-slate-100">
                    <button type="button" class="sidebar-toggle" id="sidebarToggle">
                        <i class="fas fa-chevron-left text-[10px]"></i>
                        <span class="nav-text text-[11px]">Recolher</span>
                    </button>
                </div>
            </aside>

            <section class="main-card space-y-8">
                @yield('content')
            </section>
        </div>
    </main>

    <script>
        const sidebar = document.querySelector('[data-sidebar]');
        const dashboardShell = document.querySelector('.dashboard-shell');
        const toggleButtons = [document.getElementById('sidebarToggle')];

        const updateToggleLabels = (collapsed) => {
            toggleButtons.forEach(btn => {
                if (!btn) return;
                btn.innerHTML = collapsed
                    ? '<i class="fas fa-chevron-right text-[10px]"></i>'
                    : '<i class="fas fa-chevron-left text-[10px]"></i> <span class="nav-text text-[11px]">Recolher</span>';
            });
        };

        const toggleSidebar = () => {
            const collapsed = sidebar.classList.toggle('collapsed');
            document.body.classList.toggle('sidebar-collapsed', collapsed);
            updateToggleLabels(collapsed);
        };
        toggleButtons.forEach(btn => btn?.addEventListener('click', toggleSidebar));

        // Mobile Toggle
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        mobileMenuToggle?.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });

        // Close sidebar on click outside (mobile)
        document.addEventListener('click', (e) => {
            if (window.innerWidth < 1024) {
                if (!sidebar.contains(e.target) && !mobileMenuToggle.contains(e.target) && sidebar.classList.contains('active')) {
                    sidebar.classList.remove('active');
                }
            }
        });

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

    <script>
        document.addEventListener('click', (e) => {
            // abrir/fechar
            const trigger = e.target.closest('[data-ui-select-trigger]');
            const select = trigger?.closest('[data-ui-select]');

            document.querySelectorAll('[data-ui-select-panel]').forEach(panel => {
                const owner = panel.closest('[data-ui-select]');
                if (!select || owner !== select) panel.classList.add('hidden');
            });

            if (trigger && select) {
                select.querySelector('[data-ui-select-panel]')?.classList.toggle('hidden');
                return;
            }

            // escolher opção
            const opt = e.target.closest('[data-ui-select-option]');
            if (opt) {
                const wrapper = opt.closest('[data-ui-select]');
                const valueEl = wrapper.querySelector('[data-ui-select-value]');
                const labelEl = wrapper.querySelector('[data-ui-select-label]');
                const panelEl = wrapper.querySelector('[data-ui-select-panel]');

                valueEl.value = opt.dataset.value;
                labelEl.textContent = opt.querySelector('span')?.textContent?.trim() || opt.textContent.trim();
                labelEl.classList.remove('text-slate-400');
                labelEl.classList.add('text-slate-900');

                panelEl.classList.add('hidden');
            }
        });

        // fechar com ESC
        document.addEventListener('keydown', (e) => {
            if (e.key !== 'Escape') return;
            document.querySelectorAll('[data-ui-select-panel]').forEach(p => p.classList.add('hidden'));
        });
    </script>

</body>

</html>