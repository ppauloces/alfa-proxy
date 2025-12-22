<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AlfaProxy - Recuperar Senha</title>
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
                <div class="flex items-center gap-3">
                    <a href="{{ route('login') }}" class="flex items-center gap-2 text-white bg-white/15 border border-white/25 rounded-xl px-4 py-2 backdrop-blur-sm hover:bg-white/25 transition">
                        <i class="fas fa-arrow-left"></i>
                        Voltar ao Login
                    </a>
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
                            <p class="uppercase tracking-[0.35em] text-sm text-white/60 mb-2">Recuperação</p>
                            <h1 class="text-3xl font-semibold">Esqueceu sua senha?</h1>
                            <p class="text-white/70 text-sm">Sem problemas! Digite seu e-mail e enviaremos um link para redefinir sua senha.</p>
                        </div>

                        @if (session('success'))
                            <div class="border border-emerald-400/40 bg-emerald-500/10 text-emerald-200 rounded-2xl px-4 py-3 text-sm flex items-start gap-3">
                                <i class="fas fa-check-circle mt-0.5"></i>
                                <div>{{ session('success') }}</div>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="border border-rose-400/40 bg-rose-500/10 text-rose-200 rounded-2xl px-4 py-3 text-sm flex items-start gap-3">
                                <i class="fas fa-exclamation-circle mt-0.5"></i>
                                <div>
                                    @foreach ($errors->all() as $error)
                                        <p>{{ $error }}</p>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <form class="space-y-6" method="POST" action="{{ route('senha.enviar') }}">
                            @csrf
                            <div>
                                <label class="text-sm text-white/70 mb-2 inline-block" for="email">E-mail</label>
                                <input id="email" name="email" type="email" required value="{{ old('email') }}"
                                    class="input-field w-full py-3 px-4" placeholder="seu@email.com">
                            </div>

                            <button type="submit" class="w-full py-3 rounded-2xl font-semibold bg-gradient-to-r from-[#4F8BFF] to-[#2055dd] shadow-lg shadow-blue-500/30 hover:-translate-y-0.5 transition">
                                <i class="fas fa-paper-plane mr-2"></i>
                                Enviar Link de Recuperação
                            </button>
                        </form>

                        <div class="pt-4 text-center">
                            <p class="text-sm text-white/60">
                                Lembrou sua senha?
                                <a href="{{ route('login') }}" class="text-[#60a5fa] hover:text-white font-semibold transition">
                                    Fazer login
                                </a>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Text -->
                <div class="space-y-8">
                    <h2 class="text-4xl lg:text-5xl font-bold leading-tight">
                        Recupere o acesso à sua conta
                    </h2>
                    <p class="text-lg text-white/80 max-w-xl">
                        Enviaremos um link seguro para o seu e-mail. O link é válido por 60 minutos.
                    </p>

                    <div class="space-y-4 pt-4">
                        <div class="flex items-start gap-4">
                            <div class="bg-white/10 rounded-full p-3 mt-1">
                                <i class="fas fa-shield-alt text-[#60a5fa]"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold mb-1">Link seguro e temporário</h3>
                                <p class="text-white/70 text-sm">O link expira em 1 hora por questões de segurança</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="bg-white/10 rounded-full p-3 mt-1">
                                <i class="fas fa-clock text-[#60a5fa]"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold mb-1">Receba em segundos</h3>
                                <p class="text-white/70 text-sm">O email chegará instantaneamente na sua caixa de entrada</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>

</html>
