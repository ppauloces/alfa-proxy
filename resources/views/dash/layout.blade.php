<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'AlfaProxy - Dashboard')</title>

    <!-- Dark Mode Script - Must be in head to prevent flash -->
    <script>
        // Inicializar dark mode imediatamente
        (function() {
            const isDark = localStorage.getItem('darkMode') === 'true' ||
                          (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches);

            if (isDark) {
                document.documentElement.classList.add('dark');
            }
        })();

        // Função global para toggle
        function toggleDarkMode() {
            const html = document.documentElement;
            const isDark = html.classList.contains('dark');

            if (isDark) {
                html.classList.remove('dark');
                localStorage.setItem('darkMode', 'false');
            } else {
                html.classList.add('dark');
                localStorage.setItem('darkMode', 'true');
            }
        }
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/card/2.5.4/card.min.css">
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

        /* Dark mode transitions */
        * {
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
        }

        .dark body {
            background: #0f172a;
            color: #e2e8f0;
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

        .dark .dashboard-shell {
            background: #0f172a;
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
            padding: 2rem 1rem;
            box-shadow: 4px 0 20px rgba(15, 23, 42, 0.02);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
        }

        .dark .sidebar-card {
            background: #1e293b;
            border-right-color: rgba(51, 65, 85, 0.8);
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.3);
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
            padding: 2rem 0.75rem;
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
            padding: 0.75rem 1rem;
            border-radius: 12px;
            border: none;
            background: transparent;
            color: #64748b;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .nav-pill i {
            font-size: 1rem;
            transition: all 0.2s ease;
        }

        .nav-pill:hover {
            background: #f1f5f9;
            color: var(--sf-blue);
        }

        .dark .nav-pill {
            color: #94a3b8;
        }

        .dark .nav-pill:hover {
            background: #334155;
            color: #60a5fa;
        }

        .nav-pill.active {
            background: #eff6ff;
            color: var(--sf-blue);
        }

        .dark .nav-pill.active {
            background: #1e3a8a;
            color: #93c5fd;
        }

        .nav-pill.active i {
            color: var(--sf-blue);
        }

        .dark .nav-pill.active i {
            color: #93c5fd;
        }

        .sidebar-toggle {
            width: 100%;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            padding: 0.65rem;
            font-size: 0.75rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            color: #64748b;
            background: #fff;
            cursor: pointer;
            transition: all 0.2s ease;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .sidebar-toggle:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: #334155;
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

        .dark .admin-modal-overlay {
            background: rgba(0, 0, 0, 0.8);
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

        .dark .admin-modal {
            background: #1e293b;
            box-shadow: 0 50px 100px rgba(0, 0, 0, 0.5);
        }

        /* ===================================
           DARK MODE - GLOBAL STYLES
        =================================== */

        /* Cards & Containers */
        .dark .admin-card,
        .dark .support-card,
        .dark .finance-card,
        .dark .contact-method,
        .dark .faq-item,
        .dark .timeline-item {
            background: #1e293b !important;
            border-color: #334155 !important;
        }

        /* Text Colors */
        .dark h1,
        .dark h2,
        .dark h3,
        .dark h4,
        .dark h5,
        .dark h6 {
            color: #f1f5f9 !important;
        }

        .dark p,
        .dark span:not(.badge):not(.badge-amber):not(.badge-success):not(.badge-pending):not([class*="text-red"]):not([class*="text-green"]):not([class*="text-emerald"]):not([class*="text-blue"]):not([class*="text-indigo"]):not([class*="text-amber"]) {
            color: #cbd5e1 !important;
        }

        .dark .text-slate-900 {
            color: #f1f5f9 !important;
        }

        .dark .text-slate-800 {
            color: #e2e8f0 !important;
        }

        .dark .text-slate-700 {
            color: #cbd5e1 !important;
        }

        .dark .text-slate-600 {
            color: #94a3b8 !important;
        }

        .dark .text-slate-500 {
            color: #64748b !important;
        }

        .dark .text-slate-400 {
            color: #475569 !important;
        }

        /* Background Colors */
        .dark .bg-white {
            background-color: #1e293b !important;
        }

        .dark .bg-slate-50 {
            background-color: #334155 !important;
        }

        .dark .bg-slate-100 {
            background-color: #475569 !important;
        }

        .dark .bg-gray-50 {
            background-color: #334155 !important;
        }

        .dark .bg-gray-100 {
            background-color: #475569 !important;
        }

        /* Borders */
        .dark .border-slate-100,
        .dark .border-slate-200 {
            border-color: #334155 !important;
        }

        .dark .border-gray-200,
        .dark .border-gray-300 {
            border-color: #334155 !important;
        }

        /* Forms */
        .dark .form-input,
        .dark .form-textarea,
        .dark input[type="text"],
        .dark input[type="email"],
        .dark input[type="password"],
        .dark input[type="number"],
        .dark textarea,
        .dark select {
            background-color: #334155 !important;
            border-color: #475569 !important;
            color: #f1f5f9 !important;
        }

        .dark .form-input::placeholder,
        .dark .form-textarea::placeholder,
        .dark input::placeholder,
        .dark textarea::placeholder {
            color: #64748b !important;
        }

        .dark .form-label,
        .dark label {
            color: #e2e8f0 !important;
        }

        /* Tables */
        .dark .admin-table,
        .dark table {
            color: #e2e8f0;
        }

        .dark .admin-table thead,
        .dark table thead {
            background-color: #334155 !important;
        }

        .dark .admin-table th,
        .dark table th {
            color: #f1f5f9 !important;
            border-color: #475569 !important;
        }

        .dark .admin-table td,
        .dark table td {
            color: #cbd5e1 !important;
            border-color: #334155 !important;
        }

        .dark .admin-table tbody tr:hover,
        .dark table tbody tr:hover {
            background-color: #334155 !important;
        }

        /* Buttons */
        .dark .btn-secondary {
            background-color: #334155 !important;
            color: #e2e8f0 !important;
            border-color: #475569 !important;
        }

        .dark .btn-secondary:hover {
            background-color: #475569 !important;
        }

        /* Chart Bars */
        .dark .chart-bar {
            background-color: #334155 !important;
        }

        .dark .chart-bar span {
            background: linear-gradient(90deg, #3b82f6, #60a5fa) !important;
        }

        /* Select Components */
        .dark [data-ui-select] {
            background-color: #334155 !important;
            border-color: #475569 !important;
        }

        .dark [data-ui-select-label] {
            color: #e2e8f0 !important;
        }

        .dark [data-ui-select-panel] {
            background-color: #1e293b !important;
            border-color: #475569 !important;
        }

        .dark [data-ui-select-option]:hover {
            background-color: #334155 !important;
        }

        /* Sidebar Account Info */
        .dark .account-info p {
            color: #94a3b8 !important;
        }

        .dark .account-info p.text-slate-800 {
            color: #e2e8f0 !important;
        }

        /* Scrollbar Dark Mode */
        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #475569;
        }

        .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #64748b;
        }

        /* Logo containers */
        .dark header a.flex.items-center {
            background-color: #1e293b/90 !important;
            border-color: #334155 !important;
        }

        .dark header a.flex.items-center:hover {
            background-color: #1e293b !important;
        }

        /* ===================================
           RESPONSIVIDADE MOBILE & TABLET
        =================================== */

        /* Mobile - Até 640px */
        @media (max-width: 640px) {
            header .max-w-full {
                padding: 0.75rem 1rem !important;
                gap: 0.5rem !important;
            }

            header img {
                height: 1.5rem !important;
            }

            header nav {
                display: none !important;
            }

            header .flex.items-center.gap-4 {
                gap: 0.5rem !important;
            }

            header button {
                padding: 0.5rem !important;
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

<body class="antialiased bg-white dark:bg-gray-900">

    <div class="grid-overlay"></div>

    <header class="fixed top-0 left-0 right-0 z-40 bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg border-b border-slate-100 dark:border-slate-700">
        <div class="max-w-full mx-auto px-8 py-4 flex justify-between items-center gap-8">
            <!-- LOGO HORIZONTAL -->
            <a href="{{ route('dash.show') }}"
                class="flex items-center gap-3 shrink-0 bg-white/90 backdrop-blur rounded-2xl border border-slate-100 shadow-sm px-3 sm:px-4 py-2 hover:bg-white transition">
                {{-- Troque o src pelo caminho real da sua logo horizontal --}}
                <img src="{{ asset('images/logoproxy1.webp') }}" alt="AlfaProxy"
                    class="h-6 sm:h-8 lg:h-9 w-auto object-contain">
            </a>
            <div class="flex items-center flex-1 justify-end">
                {{-- Espaçador para mobile --}}
                <div class="lg:hidden w-10"></div>
            </div>
            <nav class="hidden xl:flex items-center bg-slate-100/50 dark:bg-slate-700/50 rounded-full px-2 py-1">
                <a href="{{ route('inicial') }}"
                    class="px-5 py-2 rounded-full text-slate-600 dark:text-slate-300 text-sm font-semibold hover:text-[#23366f] dark:hover:text-blue-400 hover:bg-white dark:hover:bg-slate-600 transition-all">Inicio</a>
                <a href="{{ route('inicial') }}"
                    class="px-5 py-2 rounded-full text-slate-600 dark:text-slate-300 text-sm font-semibold hover:text-[#23366f] dark:hover:text-blue-400 hover:bg-white dark:hover:bg-slate-600 transition-all">Planos</a>
                <a href="{{ route('inicial') }}"
                    class="px-5 py-2 rounded-full text-slate-600 dark:text-slate-300 text-sm font-semibold hover:text-[#23366f] dark:hover:text-blue-400 hover:bg-white dark:hover:bg-slate-600 transition-all">API</a>
                <a href="{{ route('duvidas.show') }}"
                    class="px-5 py-2 rounded-full text-slate-600 dark:text-slate-300 text-sm font-semibold hover:text-[#23366f] dark:hover:text-blue-400 hover:bg-white dark:hover:bg-slate-600 transition-all">Suporte</a>
            </nav>
            <div class="flex items-center gap-4">
                @auth
                    <!-- <div class="flex items-center gap-3 bg-[#23366f]/5 px-4 py-2 rounded-2xl border border-[#23366f]/10">
                                <div class="w-8 h-8 rounded-lg bg-[#23366f] flex items-center justify-center text-white text-xs">
                                    <i class="fas fa-wallet"></i>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-[9px] uppercase tracking-widest text-slate-400 font-bold leading-none mb-1">Saldo Disponível</span>
                                    <span class="text-sm font-extrabold text-[#23366f] leading-none">
                                        R$ {{ number_format(Auth::user()->saldo ?? 0, 2, ',', '.') }}
                                    </span>
                                </div>
                                <button type="button" data-section-link="saldo" class="ml-2 w-6 h-6 rounded-md bg-white flex items-center justify-center text-[#23366f] hover:bg-[#23366f] hover:text-white transition-all shadow-sm">
                                    <i class="fas fa-plus text-[10px]"></i>
                                </button>
                            </div> -->

                    <div class="h-8 w-[1px] bg-slate-200 dark:bg-slate-600 hidden sm:block"></div>

                    <!-- Dark Mode Toggle -->
                    <button onclick="toggleDarkMode()" class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:text-blue-500 dark:hover:text-blue-400 hover:bg-slate-100 dark:hover:bg-slate-600 transition-all cursor-pointer" title="Alternar modo escuro">
                        <i class="fas fa-moon dark:hidden"></i>
                        <i class="fa-solid fa-sun hidden dark:block"></i>
                    </button>

                    <form action="{{ route('logout.perform') }}" method="GET" class="inline">
                        <button type="submit"
                            class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-700 text-slate-400 dark:text-slate-300 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900 transition-all cursor-pointer">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                @endauth
                <button class="lg:hidden p-2.5 rounded-xl bg-slate-100 text-slate-600" id="mobileMenuToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </header>

    <main class="dashboard-shell">
        <div class="dashboard-grid">
            <aside class="sidebar-card" data-sidebar>
                <div class="flex items-center gap-3 mb-10 px-2">
                    <div class="relative">
                        <div
                            class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#448ccb] to-[#23366f] flex items-center justify-center text-white font-bold shadow-lg shadow-blue-900/20">
                            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}{{ strtoupper(substr(explode(' ', Auth::user()->name ?? 'U')[1] ?? '', 0, 1)) }}
                        </div>
                        <div
                            class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 border-2 border-white rounded-full">
                        </div>
                    </div>
                    <div class="account-info">
                        <p class="text-[10px] uppercase tracking-widest text-slate-400 font-bold leading-tight">Painel
                            Cliente</p>
                        <p class="text-sm font-extrabold text-slate-800 truncate max-w-[140px]">
                            {{ Auth::user()->name ?? 'Usuario' }}
                        </p>
                    </div>
                </div>

                <div class="space-y-6 flex-1 overflow-y-auto custom-scrollbar px-2">
                    {{-- Seção Geral --}}
                    <div>
                        <p
                            class="sidebar-title text-[10px] uppercase tracking-[0.2em] text-slate-400 font-bold mb-3 px-4">
                            Menu Principal</p>
                        <div class="space-y-1">
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
                            class="sidebar-title text-[10px] uppercase tracking-[0.2em] text-slate-400 font-bold mb-3 px-4">
                            Financeiro</p>
                        <div class="space-y-1">
                            <div class="nav-pill opacity-60 cursor-not-allowed">
                                <span class="flex items-center gap-3">
                                    <i class="fas fa-wallet w-5 text-center text-slate-400"></i>
                                    <span class="nav-text text-slate-400">Carteira / Saldo</span>
                                </span>
                                <span
                                    class="text-[9px] bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full font-bold">Em
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
                                    <i class="fas fa-credit-card w-5 text-center"></i>
                                    <span class="nav-text">Meus Cartões</span>
                                </span>
                            </button>
                        </div>
                    </div>

                    {{-- Seção Suporte e Conta --}}
                    <div>
                        <p
                            class="sidebar-title text-[10px] uppercase tracking-[0.2em] text-slate-400 font-bold mb-3 px-4">
                            Atendimento & Conta</p>
                        <div class="space-y-1">
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
                            <button type="button" class="nav-pill" data-section-link="configuracoes">
                                <span class="flex items-center gap-3">
                                    <i class="fas fa-cog w-5 text-center"></i>
                                    <span class="nav-text">Configurações</span>
                                </span>
                            </button>
                        </div>
                    </div>

                    @if(Auth::user()->isAdmin())
                        {{-- Seção Admin --}}
                        <div class="pt-4 border-t border-slate-100">
                            <p
                                class="sidebar-title text-[10px] uppercase tracking-[0.2em] text-red-400 font-bold mb-3 px-4">
                                Administração</p>
                            <div class="space-y-1">
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
                            </div>
                        </div>
                    @endif
                </div>

                <div class="mt-8">
                    <button type="button" class="sidebar-toggle" id="sidebarToggle">
                        <i class="fas fa-chevron-left text-xs"></i>
                        <span class="nav-text">Recolher Menu</span>
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
                    ? '<i class="fas fa-chevron-right text-xs"></i>'
                    : '<i class="fas fa-chevron-left text-xs"></i> <span class="nav-text">Recolher Menu</span>';
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