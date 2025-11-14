<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AlfaProxy - Registro</title>
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
            padding: 0.75rem 1rem 0.75rem 3rem;
            width: 100%;
        }

        .input-field:focus {
            outline: none;
            border-color: rgba(79, 139, 255, 0.6);
            box-shadow: 0 0 0 3px rgba(32, 85, 221, 0.35);
        }

        .form-icon {
            position: absolute;
            left: 0.9rem;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.6);
        }

        .stats-pill {
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
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
                    <img src="{!! asset('images/logoproxy.webp') !!}" alt="Logo AlfaProxy" class="h-12 w-auto">
                </div>
                <nav class="hidden md:flex items-center bg-white/15 backdrop-blur-xl border border-white/20 rounded-full px-6 py-3 shadow-lg shadow-black/5">
                    <a href="{{ route('inicial') }}" class="px-6 py-2 rounded-full text-white hover:text-[#2055dd] transition-all hover:bg-white/15">Início</a>
                    <a href="{{ route('inicial') }}" class="px-6 py-2 rounded-full text-white hover:text-[#2055dd] transition-all hover:bg-white/15">Planos</a>
                    <a href="{{ route('inicial') }}" class="px-6 py-2 rounded-full text-white hover:text-[#2055dd] transition-all hover:bg-white/15">API</a>
                    <a href="{{ route('duvidas.show') }}" class="px-6 py-2 rounded-full text-white hover:text-[#2055dd] transition-all hover:bg-white/15">Suporte</a>
                </nav>
                <div class="flex items-center gap-3">
                    <a href="{{ route('login') }}" class="flex items-center gap-2 text-white bg-white/15 border border-white/25 rounded-xl px-4 py-2 backdrop-blur-sm hover:bg-white/25 transition">
                        <i class="fas fa-arrow-left"></i>
                        Voltar ao login
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
                <!-- Text -->
                <div class="space-y-8">
                    <h2 class="text-4xl lg:text-5xl font-bold leading-tight">
                        Bem-vindo à plataforma de proxies premium
                    </h2>
                    <p class="text-lg text-white/80 max-w-xl">
                        Crie sua conta, configure suas compras em segundos e acompanhe o dashboard completo em um único lugar.
                    </p>
                    <div class="grid grid-cols-2 gap-3 text-white text-sm">
                        <div class="stats-pill px-3 py-2">
                            <i class="fas fa-check text-xs mr-2"></i> Acesso instantâneo
                        </div>
                        <div class="stats-pill px-3 py-2">
                            <i class="fas fa-shield-alt text-xs mr-2"></i> Proteção total
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <div class="form-shell">
                    <div class="pill-highlight"></div>
                    <div class="glass-card p-10 space-y-8 relative">
                        <div>
                            <p class="uppercase tracking-[0.35em] text-sm text-white/60 mb-2">Novo cadastro</p>
                            <h1 class="text-3xl font-semibold">Crie sua conta AlfaProxy</h1>
                            <p class="text-white/70 text-sm">Preencha os dados abaixo para começar.</p>
                        </div>

                        <form class="space-y-6" method="POST" action="{{ route('register.perform') }}">
                            @csrf
                            <div class="relative">
                                <i class="fas fa-user form-icon"></i>
                                <input name="name" type="text" required value="{{ old('name') }}"
                                    class="input-field" placeholder="Nome completo">
                            </div>

                            <div class="relative">
                                <i class="fas fa-envelope form-icon"></i>
                                <input name="email" type="email" required value="{{ old('email') }}"
                                    class="input-field" placeholder="Seu e-mail">
                            </div>

                            <div class="relative">
                                <i class="fas fa-user-circle form-icon"></i>
                                <input name="username" type="text" required value="{{ old('username') }}"
                                    class="input-field" placeholder="Username">
                            </div>

                            <div class="relative">
                                <i class="fas fa-lock form-icon"></i>
                                <input name="password" type="password" required
                                    class="input-field" placeholder="Senha">
                            </div>

                            <div class="relative">
                                <i class="fas fa-lock form-icon"></i>
                                <input name="password_confirmation" type="password" required
                                    class="input-field" placeholder="Confirmar senha">
                            </div>

                            <button type="submit" class="w-full py-3 rounded-2xl font-semibold bg-gradient-to-r from-[#4F8BFF] to-[#2055dd] shadow-lg shadow-blue-500/30 hover:-translate-y-0.5 transition">
                                <i class="fas fa-user-plus mr-2"></i>
                                Criar conta
                            </button>
                        </form>

                        <div class="text-center text-white/60 text-sm">
                            Já tem conta? <a href="{{ route('login') }}" class="text-[#60a5fa] hover:text-white font-medium">Faça login</a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>

</html>
