<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ProxyAlfa - Login</title>
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
        
        @media (max-width: 768px) {
            .login-container {
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

                @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if(session('danger'))
                <div class="alert alert-danger">{{ session('danger') }}</div>
                @endif

                <i class="fas fa-user-lock text-blue-500 text-5xl mb-4"></i>
                <h2 class="text-3xl font-bold text-gray-900">Acesse sua conta</h2>
                <p class="mt-2 text-sm text-gray-600">
                    Ou <a href="{{ route('register.show') }}" class="font-medium text-blue-600 hover:text-blue-500">crie uma nova conta</a>
                </p>
            </div>
            
            <div class="bg-white py-8 px-6 shadow rounded-lg sm:px-10 login-container">
                <form class="mb-0 space-y-6" action="{{ route('login.perform') }}" method="POST">
                @csrf
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">E-mail ou usuário</label>
                        @if ($errors->has('username'))
                            <span class="text-danger text-left">{{ $errors->first('username') }}</span>
                        @endif
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400"></i>
                            </div>
                            <input id="email" name="username" type="text" autocomplete="email" required 
                                   class="input-field py-3 pl-10 w-full border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" 
                                   placeholder="seu@email.com">
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
                            <input id="password" name="password" type="password" autocomplete="current-password" required 
                                   class="input-field py-3 pl-10 w-full border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" 
                                   placeholder="••••••••">
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember-me" name="remember-me" type="checkbox" 
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="remember-me" class="ml-2 block text-sm text-gray-700">
                                Lembrar de mim
                            </label>
                        </div>

                        <div class="text-sm">
                            <a href="#" class="font-medium text-blue-600 hover:text-blue-500">
                                Esqueceu sua senha?
                            </a>
                        </div>
                    </div>

                    <div>
                        <button type="submit" 
                                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-300">
                            Entrar
                        </button>
                    </div>
                </form>
                
                <div class="relative mt-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">
                            Ou entre com
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
                    Não tem uma conta? <a href="{{ route('register.show') }}" class="font-medium text-blue-600 hover:text-blue-500">Cadastre-se agora</a>
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
        // Form validation
        const form = document.querySelector('form');
        
        form.addEventListener('submit', function(e) {
            
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            // Simple validation
            if (!email || !password) {
                alert('Por favor, preencha todos os campos.');
                return;
            }
            
            // Here you would typically send the data to your server
            console.log('Login attempt with:', { email, password });
            
            // Simulate successful login
            //alert('Login realizado com sucesso! Redirecionando...');
            // window.location.href = 'dashboard.html';
        });
        
        // Social login buttons
        document.querySelectorAll('.social-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                const provider = this.querySelector('i').className.includes('google') ? 'Google' : 'GitHub';
                alert(`Você será redirecionado para fazer login com ${provider}`);
            });
        });
    </script>
</body>
</html>