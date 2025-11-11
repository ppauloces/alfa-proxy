{{-- resources/views/dashboard.blade.php --}}
@extends('logado.partials.app')

@section('title', 'Dashboard')

@section('content')

            <div class="max-w-4xl mx-auto">
            <!-- FAQ Categories -->
            <div class="flex flex-wrap justify-center gap-4 mb-12">
                <button class="px-6 py-2 bg-indigo-600 text-white rounded-full font-medium hover:bg-indigo-700 transition">Todos</button>
                <button class="px-6 py-2 bg-gray-200 text-gray-700 rounded-full font-medium hover:bg-gray-300 transition">Básico</button>
                <button class="px-6 py-2 bg-gray-200 text-gray-700 rounded-full font-medium hover:bg-gray-300 transition">Técnico</button>
                <button class="px-6 py-2 bg-gray-200 text-gray-700 rounded-full font-medium hover:bg-gray-300 transition">Segurança</button>
                <button class="px-6 py-2 bg-gray-200 text-gray-700 rounded-full font-medium hover:bg-gray-300 transition">Configuração</button>
            </div>

            <!-- FAQ List -->
            <div class="space-y-6">
                <!-- FAQ Item 1 -->
                <div class="faq-item bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="flex justify-between items-center p-6 cursor-pointer" onclick="toggleFAQ(1)">
                        <h3 class="text-lg md:text-xl font-semibold text-gray-800">O que é um proxy SOCKS5?</h3>
                        <i class="fas fa-chevron-down text-indigo-600 transition-transform duration-300" id="icon-1"></i>
                    </div>
                    <div class="px-6 pb-6 hidden" id="answer-1">
                        <p class="text-gray-600">
                            SOCKS5 é o protocolo mais recente do protocolo SOCKS (Socket Secure), que roteia pacotes entre um servidor e um cliente por meio de um servidor proxy. Diferente de outros proxies, o SOCKS5 pode lidar com vários tipos de tráfego, incluindo HTTP, HTTPS, FTP e mais. Ele também oferece autenticação para maior segurança e suporte para UDP, tornando-o ideal para streaming, P2P e outras aplicações que requerem baixa latência.
                        </p>
                    </div>
                </div>

                <!-- FAQ Item 2 -->
                <div class="faq-item bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="flex justify-between items-center p-6 cursor-pointer" onclick="toggleFAQ(2)">
                        <h3 class="text-lg md:text-xl font-semibold text-gray-800">Qual a diferença entre SOCKS5 e HTTP proxy?</h3>
                        <i class="fas fa-chevron-down text-indigo-600 transition-transform duration-300" id="icon-2"></i>
                    </div>
                    <div class="px-6 pb-6 hidden" id="answer-2">
                        <p class="text-gray-600">
                            A principal diferença está no nível de operação e flexibilidade:
                            </p><ul class="list-disc pl-6 mt-2 space-y-2">
                                <li><strong>HTTP Proxy</strong> trabalha apenas com tráfego HTTP/HTTPS e pode interpretar e modificar os dados, sendo útil para filtragem de conteúdo ou cache.</li>
                                <li><strong>SOCKS5 Proxy</strong> opera em um nível mais baixo, não interpreta o tráfego, apenas o roteia. Isso significa que pode lidar com qualquer tipo de tráfego (FTP, SMTP, torrents, etc.) e é mais versátil.</li>
                                <li>SOCKS5 também oferece melhor desempenho para certas aplicações e suporte a UDP, que HTTP proxies não têm.</li>
                            </ul>
                        <p></p>
                    </div>
                </div>

                <!-- FAQ Item 3 -->
                <div class="faq-item bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="flex justify-between items-center p-6 cursor-pointer" onclick="toggleFAQ(3)">
                        <h3 class="text-lg md:text-xl font-semibold text-gray-800">O SOCKS5 é seguro para usar?</h3>
                        <i class="fas fa-chevron-down text-indigo-600 transition-transform duration-300" id="icon-3"></i>
                    </div>
                    <div class="px-6 pb-6 hidden" id="answer-3">
                        <p class="text-gray-600">
                            O SOCKS5 em si não criptografa seu tráfego, então por padrão não é tão seguro quanto uma VPN. No entanto:
                            </p><ul class="list-disc pl-6 mt-2 space-y-2">
                                <li>Ele oferece métodos de autenticação (usuário/senha) para controlar o acesso</li>
                                <li>Quando combinado com SSL/TLS (como em aplicações HTTPS), o tráfego pode ser seguro</li>
                                <li>Para máxima segurança, recomenda-se usar SOCKS5 sobre VPN ou em conjunto com criptografia</li>
                                <li>Escolher provedores de proxy confiáveis é essencial para evitar interceptação de dados</li>
                            </ul>
                            Para atividades sensíveis, considere usar SOCKS5 com criptografia adicional ou uma VPN.
                        <p></p>
                    </div>
                </div>

                <!-- FAQ Item 4 -->
                <div class="faq-item bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="flex justify-between items-center p-6 cursor-pointer" onclick="toggleFAQ(4)">
                        <h3 class="text-lg md:text-xl font-semibold text-gray-800">Como configurar um proxy SOCKS5 no Windows?</h3>
                        <i class="fas fa-chevron-down text-indigo-600 transition-transform duration-300" id="icon-4"></i>
                    </div>
                    <div class="px-6 pb-6 hidden" id="answer-4">
                        <p class="text-gray-600">
                            Siga estes passos para configurar no Windows:
                            </p><ol class="list-decimal pl-6 mt-2 space-y-2">
                                <li>Abra <strong>Configurações</strong> &gt; <strong>Rede e Internet</strong> &gt; <strong>Proxy</strong></li>
                                <li>Em "Configuração manual de proxy", ative "Usar servidor proxy"</li>
                                <li>Digite o endereço IP e porta do seu servidor SOCKS5</li>
                                <li>Clique em <strong>Salvar</strong></li>
                            </ol>
                            Para configuração por aplicativo (recomendado):
                            <ul class="list-disc pl-6 mt-2 space-y-2">
                                <li><strong>Navegadores:</strong> Use extensões como FoxyProxy ou configure nas opções de rede</li>
                                <li><strong>Clientes Torrent:</strong> Configure nas preferências de conexão</li>
                                <li><strong>Aplicativos específicos:</strong> Verifique nas configurações de rede do aplicativo</li>
                            </ul>
                        <p></p>
                    </div>
                </div>

                <!-- FAQ Item 5 -->
                <div class="faq-item bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="flex justify-between items-center p-6 cursor-pointer" onclick="toggleFAQ(5)">
                        <h3 class="text-lg md:text-xl font-semibold text-gray-800">Posso usar SOCKS5 para streaming e jogos?</h3>
                        <i class="fas fa-chevron-down text-indigo-600 transition-transform duration-300" id="icon-5"></i>
                    </div>
                    <div class="px-6 pb-6 hidden" id="answer-5">
                        <p class="text-gray-600">
                            Sim, o SOCKS5 é excelente para streaming e jogos devido ao seu suporte a UDP e baixa latência:
                            </p><div class="grid md:grid-cols-2 gap-4 mt-4">
                                <div class="bg-indigo-50 p-4 rounded-lg">
                                    <h4 class="font-semibold text-indigo-800 mb-2"><i class="fas fa-film mr-2"></i>Para Streaming</h4>
                                    <p class="text-gray-700 text-sm">Permite acessar conteúdo georestrito de serviços como Netflix, Hulu ou BBC iPlayer. Como não modifica cabeçalhos HTTP, é menos detectável que proxies HTTP.</p>
                                </div>
                                <div class="bg-indigo-50 p-4 rounded-lg">
                                    <h4 class="font-semibold text-indigo-800 mb-2"><i class="fas fa-gamepad mr-2"></i>Para Jogos</h4>
                                    <p class="text-gray-700 text-sm">Reduz ping em alguns casos ao conectar a servidores mais próximos. Útil para jogos bloqueados por região ou para contornar restrições de rede.</p>
                                </div>
                            </div>
                            <p class="mt-4 text-gray-600">Nota: Alguns serviços podem bloquear endereços IP de proxies conhecidos. Para melhor desempenho, escolha proxies dedicados ou residenciais.</p>
                        <p></p>
                    </div>
                </div>

                <!-- FAQ Item 6 -->
                <div class="faq-item bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="flex justify-between items-center p-6 cursor-pointer" onclick="toggleFAQ(6)">
                        <h3 class="text-lg md:text-xl font-semibold text-gray-800">Onde posso obter proxies SOCKS5 confiáveis?</h3>
                        <i class="fas fa-chevron-down text-indigo-600 transition-transform duration-300" id="icon-6"></i>
                    </div>
                    <div class="px-6 pb-6 hidden" id="answer-6">
                        <p class="text-gray-600">
                            Existem várias opções para obter proxies SOCKS5:
                            </p><div class="mt-4 space-y-4">
                                <div class="flex items-start">
                                    <div class="bg-indigo-100 p-2 rounded-full mr-4">
                                        <i class="fas fa-server text-indigo-600"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-800">Provedores Pagos</h4>
                                        <p class="text-gray-600 text-sm mt-1">Serviços como Luminati, Smartproxy, Oxylabs oferecem proxies residenciais e datacenter de alta qualidade com suporte SOCKS5.</p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <div class="bg-indigo-100 p-2 rounded-full mr-4">
                                        <i class="fas fa-cloud text-indigo-600"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-800">Serviços VPN</h4>
                                        <p class="text-gray-600 text-sm mt-1">Muitas VPNs (NordVPN, Surfshark, IPVanish) incluem servidores SOCKS5 em suas assinaturas.</p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <div class="bg-indigo-100 p-2 rounded-full mr-4">
                                        <i class="fas fa-lock text-indigo-600"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-800">Auto-hospedado</h4>
                                        <p class="text-gray-600 text-sm mt-1">Você pode configurar seu próprio servidor SOCKS5 usando Shadowsocks, Dante ou outros softwares em um VPS.</p>
                                    </div>
                                </div>
                            </div>
                            <p class="mt-4 text-gray-600 font-medium">⚠️ Cuidado com proxies gratuitos, pois podem ser lentos, inseguros ou conter malware.</p>
                        <p></p>
                    </div>
                </div>
            </div>

            <!-- Still have questions -->
            <div class="mt-16 bg-indigo-50 rounded-xl p-8 text-center">
                <h3 class="text-2xl font-bold text-gray-800 mb-4">Ainda tem dúvidas?</h3>
                <p class="text-gray-600 mb-6">Nossa equipe está pronta para ajudar com qualquer questão sobre proxies SOCKS5.</p>
                <button class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-3 px-8 rounded-full transition flex items-center mx-auto">
                    <i class="fas fa-envelope mr-2"></i> Entre em Contato
                </button>
            </div>
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

<script>
        function toggleFAQ(id) {
            const answer = document.getElementById(`answer-${id}`);
            const icon = document.getElementById(`icon-${id}`);
            
            if (answer.classList.contains('hidden')) {
                answer.classList.remove('hidden');
                icon.classList.add('transform', 'rotate-180');
            } else {
                answer.classList.add('hidden');
                icon.classList.remove('transform', 'rotate-180');
            }
        }

        // Optional: Add search functionality
        document.querySelector('input[type="text"]').addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                searchFAQs(this.value.toLowerCase());
            }
        });

        function searchFAQs(term) {
            const faqItems = document.querySelectorAll('.faq-item');
            
            faqItems.forEach(item => {
                const question = item.querySelector('h3').textContent.toLowerCase();
                const answer = item.querySelector('p').textContent.toLowerCase();
                
                if (question.includes(term) || answer.includes(term)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }
    </script>
@endsection
