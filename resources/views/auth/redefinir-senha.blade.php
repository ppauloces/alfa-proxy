<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AlfaProxy - Redefinir Senha</title>
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

        .password-strength {
            height: 4px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 999px;
            overflow: hidden;
            margin-top: 8px;
        }

        .password-strength-bar {
            height: 100%;
            width: 0%;
            transition: width 0.3s, background 0.3s;
            border-radius: 999px;
        }

        .strength-weak { background: #ef4444; width: 33%; }
        .strength-medium { background: #f59e0b; width: 66%; }
        .strength-strong { background: #10b981; width: 100%; }
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
                            <p class="uppercase tracking-[0.35em] text-sm text-white/60 mb-2">Redefinição</p>
                            <h1 class="text-3xl font-semibold">Crie uma nova senha</h1>
                            <p class="text-white/70 text-sm">Digite sua nova senha. Certifique-se de criar uma senha forte e segura.</p>
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

                        <form class="space-y-6" method="POST" action="{{ route('senha.redefinir') }}" id="resetForm">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">

                            <div>
                                <label class="text-sm text-white/70 mb-2 inline-block" for="email">E-mail</label>
                                <input id="email" name="email" type="email" required value="{{ old('email') }}"
                                    class="input-field w-full py-3 px-4" placeholder="seu@email.com">
                            </div>

                            <div>
                                <label class="text-sm text-white/70 mb-2 inline-block" for="password">Nova Senha</label>
                                <div class="relative">
                                    <input id="password" name="password" type="password" required
                                        class="input-field w-full py-3 px-4 pr-12" placeholder="Mínimo 6 caracteres">
                                    <button type="button" onclick="togglePassword('password', 'toggleIcon1')"
                                        class="absolute right-4 top-1/2 -translate-y-1/2 text-white/50 hover:text-white">
                                        <i class="fas fa-eye" id="toggleIcon1"></i>
                                    </button>
                                </div>
                                <div class="password-strength">
                                    <div class="password-strength-bar" id="strengthBar"></div>
                                </div>
                                <p class="text-xs text-white/50 mt-2" id="strengthText">Digite uma senha</p>
                            </div>

                            <div>
                                <label class="text-sm text-white/70 mb-2 inline-block" for="password_confirmation">Confirmar Nova Senha</label>
                                <div class="relative">
                                    <input id="password_confirmation" name="password_confirmation" type="password" required
                                        class="input-field w-full py-3 px-4 pr-12" placeholder="Digite a senha novamente">
                                    <button type="button" onclick="togglePassword('password_confirmation', 'toggleIcon2')"
                                        class="absolute right-4 top-1/2 -translate-y-1/2 text-white/50 hover:text-white">
                                        <i class="fas fa-eye" id="toggleIcon2"></i>
                                    </button>
                                </div>
                            </div>

                            <button type="submit" class="w-full py-3 rounded-2xl font-semibold bg-gradient-to-r from-[#4F8BFF] to-[#2055dd] shadow-lg shadow-blue-500/30 hover:-translate-y-0.5 transition">
                                <i class="fas fa-lock mr-2"></i>
                                Redefinir Senha
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Text -->
                <div class="space-y-8">
                    <h2 class="text-4xl lg:text-5xl font-bold leading-tight">
                        Crie uma senha segura
                    </h2>
                    <p class="text-lg text-white/80 max-w-xl">
                        Sua nova senha deve ter no mínimo 6 caracteres. Recomendamos usar letras, números e símbolos.
                    </p>

                    <div class="space-y-4 pt-4">
                        <div class="flex items-start gap-4">
                            <div class="bg-white/10 rounded-full p-3 mt-1">
                                <i class="fas fa-check text-emerald-400"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold mb-1">Mínimo 6 caracteres</h3>
                                <p class="text-white/70 text-sm">Use uma combinação de letras e números</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="bg-white/10 rounded-full p-3 mt-1">
                                <i class="fas fa-shield-alt text-blue-400"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold mb-1">Evite senhas óbvias</h3>
                                <p class="text-white/70 text-sm">Não use sequências ou datas de nascimento</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="bg-white/10 rounded-full p-3 mt-1">
                                <i class="fas fa-key text-purple-400"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold mb-1">Senha única</h3>
                                <p class="text-white/70 text-sm">Não reutilize senhas de outras contas</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        const passwordInput = document.getElementById('password');
        const strengthBar = document.getElementById('strengthBar');
        const strengthText = document.getElementById('strengthText');

        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;

            if (password.length >= 6) strength++;
            if (password.length >= 10) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;

            strengthBar.className = 'password-strength-bar';

            if (password.length === 0) {
                strengthText.textContent = 'Digite uma senha';
            } else if (strength <= 2) {
                strengthBar.classList.add('strength-weak');
                strengthText.textContent = 'Força: Fraca';
                strengthText.className = 'text-xs text-red-400 mt-2';
            } else if (strength <= 3) {
                strengthBar.classList.add('strength-medium');
                strengthText.textContent = 'Força: Média';
                strengthText.className = 'text-xs text-yellow-400 mt-2';
            } else {
                strengthBar.classList.add('strength-strong');
                strengthText.textContent = 'Força: Forte';
                strengthText.className = 'text-xs text-emerald-400 mt-2';
            }
        });
    </script>
</body>

</html>
