<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ProxyAlfa - Painel do Usuário</title>
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
        .sidebar {
            transition: all 0.3s ease;
        }
        
        .sidebar-link {
            transition: all 0.2s ease;
        }
        
        .sidebar-link:hover {
            background-color: rgba(59, 130, 246, 0.1);
        }
        
        .sidebar-link.active {
            background-color: rgba(59, 130, 246, 0.2);
            border-left: 3px solid #3b82f6;
        }
        
        .dropdown-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }
        
        .dropdown-content.show {
            max-height: 500px;
        }
        
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                top: 0;
                left: -100%;
                z-index: 40;
                width: 80%;
                height: 100vh;
            }
            
            .sidebar.show {
                left: 0;
            }
            
            .overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 30;
                display: none;
            }
            
            .overlay.show {
                display: block;
            }
        }
    </style>
</head>
<body class="bg-gray-50 font-sans">
    <!-- Overlay for mobile sidebar -->
    <div class="overlay" id="overlay"></div>

    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <button class="md:hidden text-gray-600" id="sidebarToggle">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <div class="flex items-center space-x-2">
                    <i class="fas fa-globe text-blue-500 text-2xl"></i>
                    <h1 class="text-xl font-bold text-gray-800">Proxy<span class="text-blue-500">Alfa</span></h1>
                </div>
            </div>
            <nav class="hidden md:flex space-x-6">
                <a href="#" class="text-gray-600 hover:text-blue-500 font-medium">Dashboard</a>
                <a href="#" class="text-gray-600 hover:text-blue-500 font-medium">Proxies</a>
                <a href="#" class="text-gray-600 hover:text-blue-500 font-medium">API</a>
                <a href="#" class="text-gray-600 hover:text-blue-500 font-medium">Suporte</a>
            </nav>
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <button class="text-gray-600 hover:text-blue-500" id="notificationBtn">
                        <i class="fas fa-bell"></i>
                        <span class="absolute top-0 right-0 h-2 w-2 rounded-full bg-red-500"></span>
                    </button>
                    <div class="hidden absolute right-0 mt-2 w-64 bg-white rounded-md shadow-lg py-1 z-50" id="notificationDropdown">
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Nova mensagem</a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Atualização do sistema</a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Promoção especial</a>
                    </div>
                </div>
                <div class="relative">
                    <button class="flex items-center space-x-2" id="userMenuBtn">
                        <img src="{{ Auth::user()->foto_perfil }}" alt="User" class="w-8 h-8 rounded-full">
                        <span class="hidden md:inline text-gray-700">{{ Auth::user()->username }}</span>
                    </button>
                    <div class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50" id="userMenuDropdown">
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Meu Perfil</a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Configurações</a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Sair</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="flex">
        <!-- Sidebar -->
        <aside class="sidebar bg-white w-64 min-h-screen shadow-md fixed md:relative" id="sidebar">
            <div class="p-4 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <img src="{{ Auth::user()->foto_perfil }}" alt="User" class="w-10 h-10 rounded-full">
                    <div>
                        <h3 class="font-medium text-gray-800">{{ Auth::user()->username }}</h3>
                        <p class="text-xs text-gray-500">Plano: Premium</p>
                    </div>
                </div>
            </div>
            <nav class="p-4">
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('dashboard.show') }}" class="sidebar-link active flex items-center space-x-3 p-3 rounded-lg text-gray-700">
                            <i class="fas fa-user text-blue-500 w-5"></i>
                            <span>Perfil</span>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown">
                            <button class="sidebar-link flex items-center justify-between w-full p-3 rounded-lg text-gray-700" id="ordersDropdownBtn">
                                <div class="flex items-center space-x-3">
                                    <i class="fas fa-shopping-cart text-blue-500 w-5"></i>
                                    <span>Pedidos</span>
                                </div>
                                <i class="fas fa-chevron-down text-xs transition-transform duration-300"></i>
                            </button>
                            <div class="dropdown-content ml-8" id="ordersDropdown">
                                <a href="{{ route('socks5.show') }}" class="block p-2 text-sm text-gray-600 hover:text-blue-500">SOCKS5</a>
                            </div>
                        </div>
                    </li>
                    <li>
                        <a href="{{ route('saldo.show') }}" class="sidebar-link flex items-center space-x-3 p-3 rounded-lg text-gray-700">
                            <i class="fas fa-wallet text-blue-500 w-5"></i>
                            <span>Saldo</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('transacoes.show') }}" class="sidebar-link flex items-center space-x-3 p-3 rounded-lg text-gray-700">
                            <i class="fas fa-history text-blue-500 w-5"></i>
                            <span>Histórico de Pagamentos</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('cupons.show') }}" class="sidebar-link flex items-center space-x-3 p-3 rounded-lg text-gray-700">
                            <i class="fas fa-tag text-blue-500 w-5"></i>
                            <span>Códigos Promocionais</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('duvidas.show') }}" class="sidebar-link flex items-center space-x-3 p-3 rounded-lg text-gray-700">
                            <i class="fas fa-question-circle text-blue-500 w-5"></i>
                            <span>FAQ</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('api.show') }}" class="sidebar-link flex items-center space-x-3 p-3 rounded-lg text-gray-700">
                            <i class="fas fa-code text-blue-500 w-5"></i>
                            <span>API</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('logout.perform') }}" class="sidebar-link flex items-center space-x-3 p-3 rounded-lg text-gray-700">
                            <i class="fas fa-sign-out-alt text-blue-500 w-5"></i>
                            <span>Sair</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 p-6">
                        <div class="bg-white rounded-lg shadow p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">API de Integração</h2>
                    <div class="mt-4 md:mt-0">
                        <button id="generateTokenBtn" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition duration-300 flex items-center space-x-2">
                            <i class="fas fa-key"></i>
                            <span>Gerar Novo Token</span>
                        </button>
                    </div>
                </div>
                
                <!-- Info Box -->
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-500 mt-1"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                Utilize nossa API para integrar os serviços de proxy diretamente em seus sistemas. Gere um token de acesso abaixo para começar.
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Token Section -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Seu Token de Acesso</h3>
                    <div id="tokenContainer" class="token-display relative">
                        <span id="tokenValue" class="text-gray-800">••••••••••••••••••••••••••••••••••••••••••••••••••</span>
                        <button id="copyTokenBtn" class="copy-btn bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm hidden">
                            <i class="fas fa-copy mr-1"></i> Copiar
                        </button>
                    </div>
                    <p class="text-sm text-gray-500 mt-2">Este token é pessoal e intransferível. Não compartilhe com terceiros.</p>
                </div>
                
                <!-- API Documentation -->
                <div class="mb-8">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Documentação da API</h3>
                    
                    <div class="space-y-6">
                        <!-- Endpoint 1 -->
                        <div class="border border-gray-200 rounded-lg p-4 card-hover">
                            <div class="flex items-start justify-between">
                                <div>
                                    <span class="api-method get-method">GET</span>
                                    <span class="font-mono text-gray-800">/api/v1/proxies</span>
                                </div>
                                <button class="text-blue-500 hover:text-blue-600 text-sm flex items-center">
                                    <i class="fas fa-chevron-down mr-1"></i> Detalhes
                                </button>
                            </div>
                            <div class="mt-3 text-sm text-gray-600">
                                Retorna a lista de proxies disponíveis para sua conta.
                            </div>
                            <div class="mt-3 hidden">
                                <div class="bg-gray-50 p-3 rounded">
                                    <h4 class="font-semibold text-sm mb-2">Parâmetros:</h4>
                                    <ul class="text-xs space-y-1">
                                        <li><span class="font-mono">?type=socks5</span> - Filtra por tipo de proxy</li>
                                        <li><span class="font-mono">?country=br</span> - Filtra por país</li>
                                        <li><span class="font-mono">?limit=10</span> - Limita o número de resultados</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Endpoint 2 -->
                        <div class="border border-gray-200 rounded-lg p-4 card-hover">
                            <div class="flex items-start justify-between">
                                <div>
                                    <span class="api-method post-method">POST</span>
                                    <span class="font-mono text-gray-800">/api/v1/proxies/rotate</span>
                                </div>
                                <button class="text-blue-500 hover:text-blue-600 text-sm flex items-center">
                                    <i class="fas fa-chevron-down mr-1"></i> Detalhes
                                </button>
                            </div>
                            <div class="mt-3 text-sm text-gray-600">
                                Rotaciona os proxies da sua conta, gerando novos IPs.
                            </div>
                            <div class="mt-3 hidden">
                                <div class="bg-gray-50 p-3 rounded">
                                    <h4 class="font-semibold text-sm mb-2">Corpo da Requisição:</h4>
                                    <pre class="text-xs bg-gray-100 p-2 rounded">{
    "proxy_ids": ["123", "456", "789"]
}</pre>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Endpoint 3 -->
                        <div class="border border-gray-200 rounded-lg p-4 card-hover">
                            <div class="flex items-start justify-between">
                                <div>
                                    <span class="api-method get-method">GET</span>
                                    <span class="font-mono text-gray-800">/api/v1/proxies/:id</span>
                                </div>
                                <button class="text-blue-500 hover:text-blue-600 text-sm flex items-center">
                                    <i class="fas fa-chevron-down mr-1"></i> Detalhes
                                </button>
                            </div>
                            <div class="mt-3 text-sm text-gray-600">
                                Retorna os detalhes de um proxy específico.
                            </div>
                            <div class="mt-3 hidden">
                                <div class="bg-gray-50 p-3 rounded">
                                    <h4 class="font-semibold text-sm mb-2">Exemplo de Resposta:</h4>
                                    <pre class="text-xs bg-gray-100 p-2 rounded">{
    "id": "123",
    "ip": "192.168.1.1",
    "port": 8080,
    "username": "seu_usuario",
    "password": "sua_senha",
    "country": "BR",
    "city": "São Paulo",
    "type": "socks5",
    "expires_at": "2023-12-31T23:59:59Z"
}</pre>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Endpoint 4 -->
                        <div class="border border-gray-200 rounded-lg p-4 card-hover">
                            <div class="flex items-start justify-between">
                                <div>
                                    <span class="api-method delete-method">DELETE</span>
                                    <span class="font-mono text-gray-800">/api/v1/proxies/:id</span>
                                </div>
                                <button class="text-blue-500 hover:text-blue-600 text-sm flex items-center">
                                    <i class="fas fa-chevron-down mr-1"></i> Detalhes
                                </button>
                            </div>
                            <div class="mt-3 text-sm text-gray-600">
                                Remove um proxy da sua conta.
                            </div>
                            <div class="mt-3 hidden">
                                <div class="bg-gray-50 p-3 rounded">
                                    <h4 class="font-semibold text-sm mb-2">Exemplo de Resposta:</h4>
                                    <pre class="text-xs bg-gray-100 p-2 rounded">{
    "message": "Proxy removido com sucesso"
}</pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Authentication Section -->
                <div class="mb-8">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Autenticação</h3>
                    <p class="text-sm text-gray-600 mb-3">Para autenticar suas requisições, inclua o token no cabeçalho HTTP:</p>
                    
                    <div class="bg-gray-50 p-3 rounded">
                        <pre class="text-xs">Authorization: Bearer SEU_TOKEN_AQUI</pre>
                    </div>
                    
                    <p class="text-sm text-gray-600 mt-3">Ou como parâmetro de consulta (menos seguro):</p>
                    <div class="bg-gray-50 p-3 rounded mt-2">
                        <pre class="text-xs">https://api.proxyalfa.com/v1/proxies?token=SEU_TOKEN_AQUI</pre>
                    </div>
                </div>
                
                <!-- Rate Limits -->
                <div class="mb-8">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Limites de Requisição</h3>
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-yellow-500 mt-1"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    Seu plano atual permite até <strong>100 requisições por minuto</strong>. Requisições excedentes serão bloqueadas temporariamente.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Code Examples -->
                <div>
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Exemplos de Código</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Python Example -->
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <div class="bg-gray-800 text-white px-4 py-2 flex justify-between items-center">
                                <span class="text-sm font-medium">Python</span>
                                <button class="text-gray-300 hover:text-white text-sm flex items-center copy-code-btn">
                                    <i class="fas fa-copy mr-1"></i> Copiar
                                </button>
                            </div>
                            <div class="bg-gray-900 p-4">
                                <pre class="text-xs text-gray-300 overflow-x-auto">import requests

url = "https://api.proxyalfa.com/v1/proxies"
headers = {
    "Authorization": "Bearer SEU_TOKEN_AQUI"
}

response = requests.get(url, headers=headers)
proxies = response.json()

print(proxies)</pre>
                            </div>
                        </div>
                        
                        <!-- JavaScript Example -->
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <div class="bg-gray-800 text-white px-4 py-2 flex justify-between items-center">
                                <span class="text-sm font-medium">JavaScript</span>
                                <button class="text-gray-300 hover:text-white text-sm flex items-center copy-code-btn">
                                    <i class="fas fa-copy mr-1"></i> Copiar
                                </button>
                            </div>
                            <div class="bg-gray-900 p-4">
                                <pre class="text-xs text-gray-300 overflow-x-auto">const fetch = require('node-fetch');

const url = 'https://api.proxyalfa.com/v1/proxies';
const options = {
    method: 'GET',
    headers: {
        'Authorization': 'Bearer SEU_TOKEN_AQUI'
    }
};

fetch(url, options)
    .then(res => res.json())
    .then(proxies => console.log(proxies))
    .catch(err => console.error('error:' + err));</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
        </main>
    </div>

    <script>
        // Toggle mobile sidebar
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        });
        
        overlay.addEventListener('click', () => {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });
        
        // Toggle dropdown menus
        const ordersDropdownBtn = document.getElementById('ordersDropdownBtn');
        const ordersDropdown = document.getElementById('ordersDropdown');
        
        ordersDropdownBtn.addEventListener('click', () => {
            ordersDropdown.classList.toggle('show');
            ordersDropdownBtn.querySelector('i.fa-chevron-down').classList.toggle('transform');
            ordersDropdownBtn.querySelector('i.fa-chevron-down').classList.toggle('rotate-180');
        });
        
        // Toggle notification dropdown
        const notificationBtn = document.getElementById('notificationBtn');
        const notificationDropdown = document.getElementById('notificationDropdown');
        
        notificationBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            notificationDropdown.classList.toggle('hidden');
        });
        
        // Toggle user menu dropdown
        const userMenuBtn = document.getElementById('userMenuBtn');
        const userMenuDropdown = document.getElementById('userMenuDropdown');
        
        userMenuBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            userMenuDropdown.classList.toggle('hidden');
        });
        
        // Close dropdowns when clicking outside
        document.addEventListener('click', (e) => {
            if (!notificationBtn.contains(e.target) && !notificationDropdown.contains(e.target)) {
                notificationDropdown.classList.add('hidden');
            }
            
            if (!userMenuBtn.contains(e.target) && !userMenuDropdown.contains(e.target)) {
                userMenuDropdown.classList.add('hidden');
            }
        });
        
        // Active sidebar link
        const sidebarLinks = document.querySelectorAll('.sidebar-link');
        
        sidebarLinks.forEach(link => {
            link.addEventListener('click', () => {
                sidebarLinks.forEach(l => l.classList.remove('active'));
                link.classList.add('active');
                
                // Close sidebar on mobile after clicking a link
                if (window.innerWidth < 768) {
                    sidebar.classList.remove('show');
                    overlay.classList.remove('show');
                }
            });
        });
    </script>
</body>
</html>