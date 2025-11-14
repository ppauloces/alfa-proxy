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
            --sf-blue-light: #4F8BFF;
        }

        body {
            font-family: 'Onest', system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #0f172a;
        }

        .hero-bg {
            background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 40%, #2563eb 100%);
        }

        .glass-card {
            background: rgba(15, 23, 42, 0.85);
            border-radius: 30px;
            padding: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 30px 120px rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(24px);
        }

        .input-field {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(148, 163, 184, 0.3);
            border-radius: 16px;
            padding: 0.9rem 1rem 0.9rem 3rem;
            width: 100%;
            color: #f8fafc;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .input-field:focus {
            outline: none;
            border-color: rgba(79, 139, 255, 0.6);
            box-shadow: 0 0 0 3px rgba(32, 85, 221, 0.25);
        }

        .form-icon {
            position: absolute;
            left: 0.9rem;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(148, 163, 184, 0.8);
        }

        .btn-primary, .btn-secondary {
            border-radius: 999px;
            padding: 0.9rem 1.6rem;
            display: inline-flex;
            align-items: center;
            gap: 0.65rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .btn-primary {
            background: linear-gradient(120deg, var(--sf-blue-light), var(--sf-blue));
            color: #fff;
            box-shadow: 0 12px 30px rgba(32, 85, 221, 0.35);
        }

        .btn-secondary {
            background: rgba(15, 23, 42, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: #f8fafc;
        }

        .btn-primary:hover, .btn-secondary:hover {
            transform: translateY(-2px);
        }

        .pill-highlight {
            position: absolute;
            inset: -60px -40px -60px 40px;
            background: radial-gradient(circle at top, rgba(255, 255, 255, 0.25), transparent 60%),
                        linear-gradient(120deg, rgba(79, 139, 255, 0.35), rgba(32, 85, 221, 0.15));
            border-radius: 180px;
            opacity: 0.8;
            filter: blur(25px);
            pointer-events: none;
        }

        .stats-pill {
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            padding: 0.4rem 0.85rem;
        }
    </style>
</head>

<body>
    <div class="hero-bg min-h-screen relative overflow-hidden">
        <div class="absolute inset-0 opacity-40 pointer-events-none"
            style="background-image: linear-gradient(rgba(255,255,255,0.04) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.04) 1px, transparent 1px); background-size: 60px 60px;">
        </div>

        <header class="relative z-10">
            <div class="max-w-6xl mx-auto px-6 py-6 flex justify-between items-center">
                <img src="{!! asset('images/logoproxy.webp') !!}" alt="Logo AlfaProxy" class="h-12 w-auto">
                <nav class="hidden md:flex items-center bg-white/15 backdrop-blur-xl border border-white/20 rounded-full px-6 py-3 shadow-lg shadow-black/5">
                    <a href="{{ route('inicial') }}" class="px-6 py-2 rounded-full text-white hover:text-[#2055dd] transition-all hover:bg-white/15">Início</a>
                    <a href="{{ route('inicial') }}" class="px-6 py-2 rounded-full text-white hover:text-[#2055dd] transition-all hover:bg-white/15">Planos</a>
                    <a href="{{ route('inicial') }}" class="px-6 py-2 rounded-full text-white hover:text-[#2055dd] transition-all hover:bg-white/15">API</a>
                    <a href="{{ route('suporte.show') }}" class="px-6 py-2 rounded-full text-white hover:text-[#2055dd] transition-all hover:bg-white/15">Suporte</a>
                </nav>
                <a href="{{ route('login') }}" class="flex items-center gap-2 text-white bg-white/15 border border-white/25 rounded-xl px-4 py-2 backdrop-blur-sm hover:bg-white/25 transition">
                    <i class="fas fa-arrow-left"></i>
                    Voltar ao login
                </a>
            </div>
        </header>

        <main class="relative z-10 flex items-center justify-center min-h-screen px-4 py-12">
            <div class="max-w-6xl w-full grid lg:grid-cols-2 gap-10 items-center">
                <div class="text-white space-y-6">
                    <div class="pill-highlight"></div>
                    <p class="text-sm uppercase tracking-[0.4em] text-white/70">Registro AlfaProxy</p>
                    <h1 class="text-4xl lg:text-5xl font-bold leading-tight">Bem-vindo à plataforma de proxies premium</h1>
                    <p class="text-lg text-white/80">Crie sua conta, configure suas compras em segundos e acompanhe o dashboard completo em um único lugar.</p>
                    <div class="grid grid-cols-2 gap-3 text-white text-sm">
                        <div class="stats-pill">
                            <i class="fas fa-check text-xs mr-2"></i> Acesso instantâneo
                        </div>
                        <div class="stats-pill">
                            <i class="fas fa-shield-alt text-xs mr-2"></i> Proteção total
                        </div>
                    </div>
                </div>

                <div class="glass-card relative">
                    <div class="absolute inset-0 rounded-2xl border border-white/10 pointer-events-none"></div>
                    <div class="relative space-y-6">
                        <div>
                            <p class="text-sm uppercase tracking-[0.4em] text-slate-400">Novo cadastro</p>
                            <h2 class="text-2xl font-bold text-white">Crie sua conta AlfaProxy</h2>
                        </div>
                        <form action="{{ route('register.perform') }}" method="POST" class="space-y-5">
                            @csrf
                            <div class="relative">
                                <i class="fas fa-user form-icon"></i>
                                <input name="name" type="text" placeholder="Nome completo" value="{{ old('name') }}" class="input-field" required>
                            </div>
                            <div class="relative">
                                <i class="fas fa-envelope form-icon"></i>
                                <input name="email" type="email" placeholder="E-mail corporativo" value="{{ old('email') }}" class="input-field" required>
                            </div>
                            <div class="relative">
                                <i class="fas fa-user-circle form-icon"></i>
                                <input name="username" type="text" placeholder="Username" value="{{ old('username') }}" class="input-field" required>
                            </div>
                            <div class="relative">
                                <i class="fas fa-lock form-icon"></i>
                                <input name="password" type="password" placeholder="Senha" class="input-field" required>
                            </div>
                            <div class="relative">
                                <i class="fas fa-lock form-icon"></i>
                                <input name="password_confirmation" type="password" placeholder="Confirmar senha" class="input-field" required>
                            </div>
                            <button type="submit" class="btn-primary w-full justify-center">
                                <i class="fas fa-user-plus"></i>
                                Criar conta
                            </button>
                        </form>
                        <div class="text-center text-white/60 text-sm">
                            Já tem conta? <a href="{{ route('login') }}" class="text-white font-semibold hover:text-[#4F8BFF]">Faça login</a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>

</html>
