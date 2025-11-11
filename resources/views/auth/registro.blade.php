<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ProxyAlfa - Cadastro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3b82f6',
                        secondary: '#1e40af',
                        dark: '#1f2937',
                        light: '#f9fafb',
                    }
                }
            }
        }
    </script>
    <style>
        .input-field {
            transition: all 0.3s ease;
        }
        
        .input-field:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }
        
        .social-btn {
            transition: all 0.3s ease;
        }
        
        .social-btn:hover {
            transform: translateY(-2px);
        }
        
        .password-strength {
            height: 4px;
            transition: all 0.3s ease;
        }
        
        @media (max-width: 768px) {
            .register-container {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body class="bg-gray-50 font-sans">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-6 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <img src="{!! asset('images/logoproxy.webp') !!}" alt="Logo" height="150" width="200">
            </div>
            <nav class="hidden md:flex space-x-8">
                <a href="#" class="text-gray-600 hover:text-blue-500 font-medium">Início</a>
                <a href="#" class="text-gray-600 hover:text-blue-500 font-medium">Planos</a>
                <a href="#" class="text-gray-600 hover:text-blue-500 font-medium">API</a>
                <a href="#" class="text-gray-600 hover:text-blue-500 font-medium">Suporte</a>
            </nav>
            <div class="flex items-center space-x-4">
                <a href="#" class="text-gray-600 hover:text-blue-500"><i class="fas fa-user"></i></a>
                <a href="#" class="text-gray-600 hover:text-blue-500"><i class="fas fa-shopping-cart"></i></a>
                <button class="md:hidden text-gray-600">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <i class="fas fa-user-plus text-blue-500 text-5xl mb-4"></i>
                <h2 class="text-3xl font-bold text-gray-900">Crie sua conta</h2>
                <p class="mt-2 text-sm text-gray-600">
                    Já tem uma conta? <a href="{{ route('login.show') }}" class="font-medium text-blue-600 hover:text-blue-500">Faça login</a>
                </p>
            </div>
            
            <div class="bg-white py-8 px-6 shadow rounded-lg sm:px-10 register-container">
                <form class="mb-0 space-y-6" action="{{ route('register.perform') }}" method="POST" id="registerForm">
                @csrf    
                    <div>
                        <label for="user" class="block text-sm font-medium text-gray-700">Usuário</label>
                        @if ($errors->has('username'))
                            <span class="text-danger text-left">{{ $errors->first('username') }}</span>
                        @endif
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                            <input id="user" name="username" type="text" autocomplete="user" required 
                                   class="input-field py-3 pl-10 w-full border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" 
                                   placeholder="Seu usuário" value="{{ old('username') }}">
                        </div>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">E-mail</label>
                        @if ($errors->has('email'))
                            <span class="text-danger text-left">{{ $errors->first('email') }}</span>
                        @endif
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400"></i>
                            </div>
                            <input id="email" name="email" type="email" autocomplete="email" required 
                                   class="input-field py-3 pl-10 w-full border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" 
                                   placeholder="seu@email.com" value="{{ old('email') }}">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Senha</label>
                        @if ($errors->has('password'))
                            <span class="text-danger text-left">{{ $errors->first('password') }}</span>
                        @endif
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input id="password" name="password" type="password" autocomplete="new-password" required 
                                   class="input-field py-3 pl-10 w-full border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" 
                                   placeholder="••••••••" oninput="checkPasswordStrength()">
                        </div>
                        <div class="mt-2 flex space-x-1">
                            <div id="strength-1" class="password-strength w-full bg-gray-200 rounded"></div>
                            <div id="strength-2" class="password-strength w-full bg-gray-200 rounded"></div>
                            <div id="strength-3" class="password-strength w-full bg-gray-200 rounded"></div>
                            <div id="strength-4" class="password-strength w-full bg-gray-200 rounded"></div>
                        </div>
                        <p id="password-strength-text" class="mt-1 text-xs text-gray-500"></p>
                    </div>

                    <div>
                        <label for="confirm-password" class="block text-sm font-medium text-gray-700">Confirmar senha</label>
                        @if ($errors->has('password_confirmation'))
                            <span class="text-danger text-left">{{ $errors->first('password_confirmation') }}</span>
                        @endif
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input id="confirm-password" name="password_confirmation" type="password" autocomplete="new-password" required 
                                   class="input-field py-3 pl-10 w-full border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" 
                                   placeholder="••••••••">
                        </div>
                        <p id="password-match" class="mt-1 text-xs hidden"></p>
                    </div>

                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="terms" name="terms" type="checkbox" 
                                   class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded" required>
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="terms" class="font-medium text-gray-700">
                                Eu concordo com os <a href="#" class="text-blue-600 hover:text-blue-500">Termos de Serviço</a> e <a href="#" class="text-blue-600 hover:text-blue-500">Política de Privacidade</a>
                            </label>
                        </div>
                    </div>

                    <div>
                        <button type="submit" 
                                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-300">
                            Criar conta
                        </button>
                    </div>
                </form>
                
                <div class="relative mt-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">
                            Ou cadastre-se com
                        </span>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-2 gap-3">
                    <div>
                        <a href="#" class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-lg shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 social-btn">
                            <i class="fab fa-google text-red-500 mr-2 mt-0.5"></i> Google
                        </a>
                    </div>

                    <div>
                        <a href="#" class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-lg shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 social-btn">
                            <i class="fab fa-github text-gray-800 mr-2 mt-0.5"></i> GitHub
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="text-center text-sm text-gray-600">
                <p>
                    Ao se registrar, você concorda com nossos <a href="#" class="font-medium text-blue-600 hover:text-blue-500">Termos</a> e <a href="#" class="font-medium text-blue-600 hover:text-blue-500">Política de Privacidade</a>.
                </p>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h4 class="text-xl font-bold mb-4 flex items-center">
                        <i class="fas fa-globe mr-2"></i> ProxyAlfa
                    </h4>
                    <p class="text-gray-400">A solução mais confiável para proxies SOCKS5 premium com suporte técnico especializado.</p>
                </div>
                
                <div>
                    <h4 class="text-lg font-bold mb-4">Links Rápidos</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white">Início</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Planos</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">API</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Termos de Serviço</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-lg font-bold mb-4">Suporte</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white">Central de Ajuda</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Contato</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Status do Serviço</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-lg font-bold mb-4">Contato</h4>
                    <ul class="space-y-2">
                        <li class="flex items-center text-gray-400"><i class="fas fa-envelope mr-2"></i> suporte@proxyalfa.com</li>
                        <li class="flex items-center text-gray-400"><i class="fas fa-phone mr-2"></i> +55 11 98765-4321</li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-700 mt-12 pt-8 text-center text-gray-400">
                <p>&copy; 2023 ProxyAlfa. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

    <script>
        // Password strength checker
        function checkPasswordStrength() {
            const password = document.getElementById('password').value;
            const strengthText = document.getElementById('password-strength-text');
            const strengthBars = [
                document.getElementById('strength-1'),
                document.getElementById('strength-2'),
                document.getElementById('strength-3'),
                document.getElementById('strength-4')
            ];
            
            let strength = 0;
            
            // Check password length
            if (password.length > 0) strength += 1;
            if (password.length >= 8) strength += 1;
            
            // Check for mixed case
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength += 1;
            
            // Check for numbers and special chars
            if (/\d/.test(password)) strength += 1;
            if (/[^A-Za-z0-9]/.test(password)) strength += 1;
            
            // Cap strength at 4
            strength = Math.min(strength, 4);
            
            // Update UI
            strengthBars.forEach((bar, index) => {
                if (index < strength) {
                    if (strength <= 2) {
                        bar.className = 'password-strength w-full bg-red-500 rounded';
                    } else if (strength === 3) {
                        bar.className = 'password-strength w-full bg-yellow-500 rounded';
                    } else {
                        bar.className = 'password-strength w-full bg-green-500 rounded';
                    }
                } else {
                    bar.className = 'password-strength w-full bg-gray-200 rounded';
                }
            });
            
            // Update text
            const messages = ['Muito fraca', 'Fraca', 'Média', 'Forte', 'Muito forte'];
            const colors = ['text-red-500', 'text-red-500', 'text-yellow-500', 'text-green-500', 'text-green-500'];
            strengthText.textContent = `Força da senha: ${messages[strength]}`;
            strengthText.className = `mt-1 text-xs ${colors[strength]}`;
        }
        
        // Password match checker
        document.getElementById('confirm-password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            const matchText = document.getElementById('password-match');
            
            if (confirmPassword.length > 0) {
                if (password === confirmPassword) {
                    matchText.textContent = 'As senhas coincidem';
                    matchText.className = 'mt-1 text-xs text-green-500';
                    matchText.classList.remove('hidden');
                } else {
                    matchText.textContent = 'As senhas não coincidem';
                    matchText.className = 'mt-1 text-xs text-red-500';
                    matchText.classList.remove('hidden');
                }
            } else {
                matchText.className = 'mt-1 text-xs hidden';
            }
        });
        
        // Form validation
        const form = document.getElementById('registerForm');
        
        form.addEventListener('submit', function(e) {
            
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm-password').value;
            const terms = document.getElementById('terms').checked;
            
            // Simple validation
            if (!name || !email || !password || !confirmPassword) {
                alert('Por favor, preencha todos os campos.');
                return;
            }
            
            if (password !== confirmPassword) {
                alert('As senhas não coincidem.');
                return;
            }
            
            if (!terms) {
                alert('Você deve aceitar os Termos de Serviço e Política de Privacidade.');
                return;
            }
            
            // Here you would typically send the data to your server
            console.log('Registration attempt with:', { name, email, password });
            
            // Simulate successful registration
            //alert('Cadastro realizado com sucesso! Redirecionando para a página de login...');
            // window.location.href = 'login.html';
        });
        
        // Social register buttons
        document.querySelectorAll('.social-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                const provider = this.querySelector('i').className.includes('google') ? 'Google' : 'GitHub';
                alert(`Você será redirecionado para fazer cadastro com ${provider}`);
            });
        });
    </script>
</body>
</html>