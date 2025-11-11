@extends('logado.partials.app')

@section('content')

<div class="bg-white rounded-lg shadow p-4 md:p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 md:mb-6">
                    <h2 class="text-xl md:text-2xl font-bold text-gray-800">API de Integração</h2>
                    <div class="mt-2 md:mt-0">
                        <button id="generateTokenBtn" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 md:px-4 md:py-2 rounded-lg transition duration-300 flex items-center space-x-2 text-sm md:text-base">
                            <i class="fas fa-key"></i>
                            <span>Gerar Novo Token</span>
                        </button>
                    </div>
                </div>
                
                <!-- Info Box -->
                <div class="bg-blue-50 border-l-4 border-blue-500 p-3 md:p-4 mb-4 md:mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-500 mt-1"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-xs md:text-sm text-blue-700">
                                Utilize nossa API para integrar os serviços de proxy diretamente em seus sistemas. Gere um token de acesso abaixo para começar.
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Token Section -->
                <div class="mb-6 md:mb-8">
                    <h3 class="text-base md:text-lg font-semibold text-gray-800 mb-2 md:mb-3">Seu Token de Acesso</h3>
                    <div id="tokenContainer" class="token-display relative">
                        <span id="tokenValue" class="text-gray-800">••••••••••••••••••••••••••••••••••••••••••••••••••</span>
                        <button id="copyTokenBtn" class="copy-btn bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 md:px-3 md:py-1 rounded text-xs md:text-sm hidden">
                            <i class="fas fa-copy mr-1"></i> Copiar
                        </button>
                    </div>
                    <p class="text-xs md:text-sm text-gray-500 mt-1 md:mt-2">Este token é pessoal e intransferível. Não compartilhe com terceiros.</p>
                </div>
                
                <!-- API Documentation -->
                <div class="mb-6 md:mb-8">
                    <h3 class="text-lg md:text-xl font-bold text-gray-800 mb-3 md:mb-4">Documentação da API</h3>
                    
                    <div class="space-y-4 md:space-y-6">
                        <!-- Endpoint 1 -->
                        <div class="border border-gray-200 rounded-lg p-3 md:p-4 card-hover">
                            <div class="flex items-start justify-between">
                                <div class="flex flex-wrap items-center">
                                    <span class="api-method get-method">GET</span>
                                    <span class="font-mono text-gray-800 text-sm md:text-base">/api/v1/proxies</span>
                                </div>
                                <button class="text-blue-500 hover:text-blue-600 text-xs md:text-sm flex items-center">
                                    <i class="fas fa-chevron-down mr-1"></i> Detalhes
                                </button>
                            </div>
                            <div class="mt-2 text-xs md:text-sm text-gray-600">
                                Retorna a lista de proxies disponíveis para sua conta.
                            </div>
                            <div class="mt-2 hidden">
                                <div class="bg-gray-50 p-2 md:p-3 rounded">
                                    <h4 class="font-semibold text-xs md:text-sm mb-1 md:mb-2">Parâmetros:</h4>
                                    <ul class="text-xs space-y-1">
                                        <li><span class="font-mono">?type=socks5</span> - Filtra por tipo de proxy</li>
                                        <li><span class="font-mono">?country=br</span> - Filtra por país</li>
                                        <li><span class="font-mono">?limit=10</span> - Limita o número de resultados</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Endpoint 2 -->
                        <div class="border border-gray-200 rounded-lg p-3 md:p-4 card-hover">
                            <div class="flex items-start justify-between">
                                <div class="flex flex-wrap items-center">
                                    <span class="api-method post-method">POST</span>
                                    <span class="font-mono text-gray-800 text-sm md:text-base">/api/v1/proxies/rotate</span>
                                </div>
                                <button class="text-blue-500 hover:text-blue-600 text-xs md:text-sm flex items-center">
                                    <i class="fas fa-chevron-down mr-1"></i> Detalhes
                                </button>
                            </div>
                            <div class="mt-2 text-xs md:text-sm text-gray-600">
                                Rotaciona os proxies da sua conta, gerando novos IPs.
                            </div>
                            <div class="mt-2 hidden">
                                <div class="bg-gray-50 p-2 md:p-3 rounded">
                                    <h4 class="font-semibold text-xs md:text-sm mb-1 md:mb-2">Corpo da Requisição:</h4>
                                    <pre class="text-xs bg-gray-100 p-1 md:p-2 rounded">{
    "proxy_ids": ["123", "456", "789"]
}</pre>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Endpoint 3 -->
                        <div class="border border-gray-200 rounded-lg p-3 md:p-4 card-hover">
                            <div class="flex items-start justify-between">
                                <div class="flex flex-wrap items-center">
                                    <span class="api-method get-method">GET</span>
                                    <span class="font-mono text-gray-800 text-sm md:text-base">/api/v1/proxies/:id</span>
                                </div>
                                <button class="text-blue-500 hover:text-blue-600 text-xs md:text-sm flex items-center">
                                    <i class="fas fa-chevron-down mr-1"></i> Detalhes
                                </button>
                            </div>
                            <div class="mt-2 text-xs md:text-sm text-gray-600">
                                Retorna os detalhes de um proxy específico.
                            </div>
                            <div class="mt-2 hidden">
                                <div class="bg-gray-50 p-2 md:p-3 rounded">
                                    <h4 class="font-semibold text-xs md:text-sm mb-1 md:mb-2">Exemplo de Resposta:</h4>
                                    <pre class="text-xs bg-gray-100 p-1 md:p-2 rounded">{
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
                        <div class="border border-gray-200 rounded-lg p-3 md:p-4 card-hover">
                            <div class="flex items-start justify-between">
                                <div class="flex flex-wrap items-center">
                                    <span class="api-method delete-method">DELETE</span>
                                    <span class="font-mono text-gray-800 text-sm md:text-base">/api/v1/proxies/:id</span>
                                </div>
                                <button class="text-blue-500 hover:text-blue-600 text-xs md:text-sm flex items-center">
                                    <i class="fas fa-chevron-down mr-1"></i> Detalhes
                                </button>
                            </div>
                            <div class="mt-2 text-xs md:text-sm text-gray-600">
                                Remove um proxy da sua conta.
                            </div>
                            <div class="mt-2 hidden">
                                <div class="bg-gray-50 p-2 md:p-3 rounded">
                                    <h4 class="font-semibold text-xs md:text-sm mb-1 md:mb-2">Exemplo de Resposta:</h4>
                                    <pre class="text-xs bg-gray-100 p-1 md:p-2 rounded">{
    "message": "Proxy removido com sucesso"
}</pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Authentication Section -->
                <div class="mb-6 md:mb-8">
                    <h3 class="text-lg md:text-xl font-bold text-gray-800 mb-3 md:mb-4">Autenticação</h3>
                    <p class="text-xs md:text-sm text-gray-600 mb-2 md:mb-3">Para autenticar suas requisições, inclua o token no cabeçalho HTTP:</p>
                    
                    <div class="bg-gray-50 p-2 md:p-3 rounded">
                        <pre class="text-xs">Authorization: Bearer SEU_TOKEN_AQUI</pre>
                    </div>
                    
                    <p class="text-xs md:text-sm text-gray-600 mt-2 md:mt-3">Ou como parâmetro de consulta (menos seguro):</p>
                    <div class="bg-gray-50 p-2 md:p-3 rounded mt-1 md:mt-2">
                        <pre class="text-xs">https://api.proxyalfa.com/v1/proxies?token=SEU_TOKEN_AQUI</pre>
                    </div>
                </div>
                
                <!-- Rate Limits -->
                <div class="mb-6 md:mb-8">
                    <h3 class="text-lg md:text-xl font-bold text-gray-800 mb-3 md:mb-4">Limites de Requisição</h3>
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 md:p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-yellow-500 mt-1"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-xs md:text-sm text-yellow-700">
                                    Seu plano atual permite até <strong>100 requisições por minuto</strong>. Requisições excedentes serão bloqueadas temporariamente.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Code Examples -->
                <div>
                    <h3 class="text-lg md:text-xl font-bold text-gray-800 mb-3 md:mb-4">Exemplos de Código</h3>
                    
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-6">
                        <!-- Python Example -->
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <div class="bg-gray-800 text-white px-3 py-1 md:px-4 md:py-2 flex justify-between items-center">
                                <span class="text-xs md:text-sm font-medium">Python</span>
                                <button class="text-gray-300 hover:text-white text-xs md:text-sm flex items-center copy-code-btn">
                                    <i class="fas fa-copy mr-1"></i> Copiar
                                </button>
                            </div>
                            <div class="bg-gray-900 p-2 md:p-4">
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
                            <div class="bg-gray-800 text-white px-3 py-1 md:px-4 md:py-2 flex justify-between items-center">
                                <span class="text-xs md:text-sm font-medium">JavaScript</span>
                                <button class="text-gray-300 hover:text-white text-xs md:text-sm flex items-center copy-code-btn">
                                    <i class="fas fa-copy mr-1"></i> Copiar
                                </button>
                            </div>
                            <div class="bg-gray-900 p-2 md:p-4">
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

        // Generate API Token
        const generateTokenBtn = document.getElementById('generateTokenBtn');
        const tokenValue = document.getElementById('tokenValue');
        const copyTokenBtn = document.getElementById('copyTokenBtn');
        const tokenContainer = document.getElementById('tokenContainer');
        
        generateTokenBtn.addEventListener('click', () => {
            // Generate a random token (in a real app, this would come from your backend)
            const newToken = 'proxyalfa_' + Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
            
            tokenValue.textContent = newToken;
            copyTokenBtn.classList.remove('hidden');
            
            // Show success message
            const alert = document.createElement('div');
            alert.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded shadow-lg flex items-center';
            alert.innerHTML = `
                <i class="fas fa-check-circle mr-2"></i>
                <span>Novo token gerado com sucesso!</span>
            `;
            document.body.appendChild(alert);
            
            setTimeout(() => {
                alert.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                setTimeout(() => alert.remove(), 500);
            }, 3000);
        });
        
        // Copy Token to Clipboard
        copyTokenBtn.addEventListener('click', () => {
            const token = tokenValue.textContent;
            navigator.clipboard.writeText(token);
            
            // Change button text temporarily
            const originalText = copyTokenBtn.innerHTML;
            copyTokenBtn.innerHTML = '<i class="fas fa-check mr-1"></i> Copiado!';
            
            setTimeout(() => {
                copyTokenBtn.innerHTML = originalText;
            }, 2000);
        });
        
        // Show copy button on token hover
        tokenContainer.addEventListener('mouseenter', () => {
            if (tokenValue.textContent !== '••••••••••••••••••••••••••••••••••••••••••••••••••') {
                copyTokenBtn.classList.remove('hidden');
            }
        });
        
        tokenContainer.addEventListener('mouseleave', () => {
            copyTokenBtn.classList.add('hidden');
        });
        
        // Toggle API endpoint details
        document.querySelectorAll('.border-gray-200 button').forEach(button => {
            button.addEventListener('click', () => {
                const details = button.parentElement.parentElement.querySelector('.hidden');
                details.classList.toggle('hidden');
                
                const icon = button.querySelector('i');
                if (details.classList.contains('hidden')) {
                    icon.classList.remove('fa-chevron-up');
                    icon.classList.add('fa-chevron-down');
                } else {
                    icon.classList.remove('fa-chevron-down');
                    icon.classList.add('fa-chevron-up');
                }
            });
        });
        
        // Copy code examples
        document.querySelectorAll('.copy-code-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const codeBlock = btn.closest('.border-gray-200').querySelector('pre');
                const code = codeBlock.textContent;
                
                navigator.clipboard.writeText(code);
                
                // Change button text temporarily
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check mr-1"></i> Copiado!';
                
                setTimeout(() => {
                    btn.innerHTML = originalText;
                }, 2000);
            });
        });
    </script>

@endsection