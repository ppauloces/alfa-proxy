<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AlfaProxy - Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css'])
    <style>
        :root {
            --sf-blue: #2055dd;
            --sf-blue-accent: #3677B3;
            --sf-blue-light: #4F8BFF;
        }

        body {
            font-family: 'Onest', system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
        }

        .hero-bg {
            background: linear-gradient(to right, #438ccb, #316fab, #306da8, #3066a0, #2a508a, #233a72);
        }

        .form-shell {
            position: relative;
        }

        .pill-highlight {
            position: absolute;
            inset: -60px -40px -60px 40px;
            background: radial-gradient(circle at top, rgba(255, 255, 255, 0.25), transparent 60%),
                        linear-gradient(120deg, rgba(79, 139, 255, 0.35), rgba(32, 85, 221, 0.15));
            border-radius: 180px;
            opacity: 0.8;
            filter: blur(25px);
            transform: translateZ(0);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 28px;
            box-shadow: 0 30px 90px rgba(3, 7, 18, 0.45);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
        }

        .input-field {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.25);
            color: #f8fafc;
            border-radius: 16px;
            transition: border 0.2s ease, box-shadow 0.2s ease;
        }

        .input-field:focus {
            border-color: rgba(79, 139, 255, 0.6);
            box-shadow: 0 0 0 3px rgba(32, 85, 221, 0.35);
        }

        .google-btn {
            border-radius: 999px;
            background: #fff;
            color: #111827;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .google-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 30px rgba(255, 255, 255, 0.18);
        }

        .stats-pill {
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        /* ===================================
           RESPONSIVIDADE MOBILE & TABLET
        =================================== */

        /* Mobile - Até 640px */
        @media (max-width: 640px) {
            /* Header mobile */
            header .max-w-7xl {
                padding: 1rem;
            }

            header img {
                height: 2rem !important;
            }

            /* Ocultar texto do botão "Criar conta" em mobile */
            header .flex.items-center.gap-3 a {
                padding: 0.5rem !important;
            }

            header .flex.items-center.gap-3 a span {
                display: none;
            }

            header .flex.items-center.gap-3 a i {
                margin: 0 !important;
            }

            /* Main mobile */
            main {
                padding-top: 6rem !important;
                padding-bottom: 3rem !important;
            }

            /* Glass card mobile */
            .glass-card {
                padding: 1.5rem !important;
                border-radius: 20px !important;
            }

            /* Form mobile */
            .form-shell {
                order: 2;
            }

            /* Text section mobile */
            .space-y-8 {
                order: 1;
                margin-bottom: 2rem;
            }

            h1 {
                font-size: 1.5rem !important;
            }

            h2 {
                font-size: 1.75rem !important;
            }

            /* Pill highlight - reduzir em mobile */
            .pill-highlight {
                inset: -30px -20px -30px 20px !important;
                filter: blur(15px) !important;
            }

            /* Buttons mobile */
            .google-btn {
                font-size: 0.875rem !important;
            }
        }

        /* Tablet - 641px até 1024px */
        @media (min-width: 641px) and (max-width: 1024px) {
            .glass-card {
                padding: 2rem !important;
            }

            h2 {
                font-size: 2.5rem !important;
            }

            main {
                padding-top: 8rem !important;
            }
        }

        /* Ajustes gerais para telas pequenas */
        @media (max-width: 768px) {
            .max-w-7xl {
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }

            .grid-cols-1 {
                gap: 2rem !important;
            }

            /* Esconder menu hamburguer se não tiver funcionalidade */
            button.md\:hidden {
                display: none;
            }
        }

        /* Landscape mobile */
        @media (max-height: 768px) and (orientation: landscape) {
            main {
                padding-top: 4rem !important;
                padding-bottom: 2rem !important;
            }

            .pill-highlight {
                display: none;
            }
        }

        /* Touch devices - melhorar áreas de toque */
        @media (hover: none) and (pointer: coarse) {
            button,
            a {
                min-height: 44px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            .input-field {
                padding-top: 0.875rem !important;
                padding-bottom: 0.875rem !important;
            }
        }
    </style>
</head>

<body class="bg-gray-900 text-white font-sans">
    <div class="hero-bg min-h-screen">
        <div class="absolute inset-0 pointer-events-none opacity-40"
            style="background-image: linear-gradient(rgba(255,255,255,0.04) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.04) 1px, transparent 1px); background-size: 60px 60px;">
        </div>

        <!-- Header -->
        <header class="relative z-10">
            <div class="max-w-7xl mx-auto px-6 py-6 flex justify-between items-center gap-8">
                <div class="flex items-center">
                    <a href="{{ route('inicial') }}" class="hover:opacity-80 transition-opacity">
                    <img src="{!! asset('images/logoproxy.webp') !!}" alt="Logo AlfaProxy" class="h-12 w-auto">
                    </a>
                </div>
                <!-- <nav class="hidden md:flex items-center bg-white/15 backdrop-blur-xl border border-white/20 rounded-full px-6 py-3 shadow-lg shadow-black/5">
                    <a href="{{ route('inicial') }}" class="px-6 py-2 rounded-full text-white hover:text-[#2055dd] transition-all hover:bg-white/15">Início</a>
                    <a href="{{ route('inicial') }}" class="px-6 py-2 rounded-full text-white hover:text-[#2055dd] transition-all hover:bg-white/15">Planos</a>
                    <a href="{{ route('inicial') }}" class="px-6 py-2 rounded-full text-white hover:text-[#2055dd] transition-all hover:bg-white/15">API</a>
                    <a href="{{ route('duvidas.show') }}" class="px-6 py-2 rounded-full text-white hover:text-[#2055dd] transition-all hover:bg-white/15">Suporte</a>
                </nav> -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('register.show') }}" class="flex items-center gap-2 text-white bg-white/15 border border-white/25 rounded-xl px-4 py-2 backdrop-blur-sm hover:bg-white/25 transition">
                        <i class="fas fa-user-plus"></i>
                        <span>Criar conta</span>
                    </a>
                    <button class="md:hidden text-white text-2xl">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>
        </header>

        <!-- Main -->
        <main class="relative z-10 pt-32 pb-20">
            <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 lg:grid-cols-2 gap-16 items-start">
                <!-- Form -->
                <div class="form-shell">
                    <div class="pill-highlight"></div>
                    <div class="glass-card p-10 space-y-8 relative">
                    <div>
                        <p class="uppercase tracking-[0.35em] text-sm text-white/60 mb-2">Login</p>
                        <h1 class="text-3xl font-semibold">Bem-vindo de volta</h1>
                        <p class="text-white/70 text-sm">Acesse com suas credenciais ou continue com o Google.</p>
                    </div>

                    @if (session('success'))
                        <div class="border border-emerald-400/40 bg-emerald-500/10 text-emerald-200 rounded-2xl px-4 py-3 text-sm">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="border border-rose-400/40 bg-rose-500/10 text-rose-200 rounded-2xl px-4 py-3 text-sm">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form class="space-y-6" method="POST" action="{{ route('login.perform') }}">
                        @csrf
                        <div>
                            <label class="text-sm text-white/70 mb-2 inline-block" for="email">E-mail ou usuário</label>
                            @if ($errors->has('username'))
                                <p class="text-rose-300 text-xs mb-1">{{ $errors->first('username') }}</p>
                            @endif
                            <input id="email" name="username" type="text" required value="{{ old('username') }}"
                                class="input-field w-full py-3 px-4" placeholder="seu@email.com">
                        </div>

                        <div>
                            <label class="text-sm text-white/70 mb-2 inline-block" for="password">Senha</label>
                            @if ($errors->has('password'))
                                <p class="text-rose-300 text-xs mb-1">{{ $errors->first('password') }}</p>
                            @endif
                            <input id="password" name="password" type="password" required
                                class="input-field w-full py-3 px-4" placeholder="••••••••">
                        </div>

                        <div class="flex flex-wrap items-center justify-between gap-3 text-sm text-white/70">
                            <label class="inline-flex items-center gap-2">
                                <input type="checkbox" name="remember-me" class="rounded border-white/30 bg-transparent text-sky-500 focus:ring-sky-500/50">
                                Lembrar de mim
                            </label>
                            <a href="{{ route('senha.show') }}" class="text-[#60a5fa] hover:text-white font-medium">Esqueceu sua senha?</a>
                        </div>

                        <button type="submit" class="w-full py-3 rounded-2xl font-semibold bg-gradient-to-r from-[#4F8BFF] to-[#2055dd] shadow-lg shadow-blue-500/30 hover:-translate-y-0.5 transition">
                            Entrar
                        </button>
                    </form>

                        <div class="relative">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-white/10"></div>
                            </div>
                            <div class="relative flex justify-center text-xs uppercase tracking-[0.4em] text-white/50">
                                <span class="px-2 bg-transparent mb-6">Ou continue com</span>
                            </div>
                        </div>

                        <form method="POST" action="#" class="space-y-3">
                            @csrf
                            <button type="submit" class="google-btn w-full py-3 text-gray-900">
                                <i class="fab fa-google text-red-500 text-xl"></i>
                                Entrar com Google
                            </button>
                        </form>

                        <div class="pt-4 text-center">
                            <p class="text-sm text-white/60">
                                Não tem uma conta?
                                <a href="{{ route('register.show') }}" class="text-[#60a5fa] hover:text-white font-semibold transition">
                                    Criar conta grátis
                                </a>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Text -->
                <div class="space-y-8">
                    
                    <h2 class="text-4xl lg:text-5xl font-bold leading-tight">
                       Olá! Que bom ver você de volta.
                    </h2>
                    <p class="text-lg text-white/80 max-w-xl">
                        Acesse sua conta e continue explorando as funcionalidades da AlfaProxy.
                    </p>
                </div>
            </div>
        </main>
    </div>
</body>

</html>
