<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ sobre Proxies SOCKS5</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }
        .gradient-bg {
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
        }
        .faq-item {
            transition: all 0.3s ease;
        }
        .faq-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="min-h-screen">
    <!-- Header -->
    <header class="gradient-bg text-white">
        <div class="container mx-auto px-4 py-12">
            <div class="flex flex-col items-center text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">Perguntas Frequentes</h1>
                <p class="text-xl md:text-2xl max-w-3xl opacity-90">Tudo o que você precisa saber sobre proxies SOCKS5</p>
                <div class="mt-8 relative w-full max-w-xl">
                    <input type="text" placeholder="Buscar perguntas..." class="w-full py-3 px-6 rounded-full shadow-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-800">
                    <button class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-indigo-600 text-white p-2 rounded-full hover:bg-indigo-700 transition">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-12 -mt-16">
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
                            <ul class="list-disc pl-6 mt-2 space-y-2">
                                <li><strong>HTTP Proxy</strong> trabalha apenas com tráfego HTTP/HTTPS e pode interpretar e modificar os dados, sendo útil para filtragem de conteúdo ou cache.</li>
                                <li><strong>SOCKS5 Proxy</strong> opera em um nível mais baixo, não interpreta o tráfego, apenas o roteia. Isso significa que pode lidar com qualquer tipo de tráfego (FTP, SMTP, torrents, etc.) e é mais versátil.</li>
                                <li>SOCKS5 também oferece melhor desempenho para certas aplicações e suporte a UDP, que HTTP proxies não têm.</li>
                            </ul>
                        </p>
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
                            <ul class="list-disc pl-6 mt-2 space-y-2">
                                <li>Ele oferece métodos de autenticação (usuário/senha) para controlar o acesso</li>
                                <li>Quando combinado com SSL/TLS (como em aplicações HTTPS), o tráfego pode ser seguro</li>
                                <li>Para máxima segurança, recomenda-se usar SOCKS5 sobre VPN ou em conjunto com criptografia</li>
                                <li>Escolher provedores de proxy confiáveis é essencial para evitar interceptação de dados</li>
                            </ul>
                            Para atividades sensíveis, considere usar SOCKS5 com criptografia adicional ou uma VPN.
                        </p>
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
                            <ol class="list-decimal pl-6 mt-2 space-y-2">
                                <li>Abra <strong>Configurações</strong> > <strong>Rede e Internet</strong> > <strong>Proxy</strong></li>
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
                        </p>
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
                            <div class="grid md:grid-cols-2 gap-4 mt-4">
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
                        </p>
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
                            <div class="mt-4 space-y-4">
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
                        </p>
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

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between">
                <div class="mb-8 md:mb-0">
                    <h3 class="text-xl font-bold mb-4">SOCKS5 Proxies</h3>
                    <p class="text-gray-400 max-w-md">Tudo o que você precisa saber sobre proxies SOCKS5 para navegação segura, streaming e muito mais.</p>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-8">
                    <div>
                        <h4 class="font-semibold mb-4">Links</h4>
                        <ul class="space-y-2">
                            <li><a href="#" class="text-gray-400 hover:text-white transition">Início</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white transition">Preços</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white transition">Tutoriais</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white transition">Blog</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold mb-4">Suporte</h4>
                        <ul class="space-y-2">
                            <li><a href="#" class="text-gray-400 hover:text-white transition">FAQ</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white transition">Contato</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white transition">Termos</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white transition">Privacidade</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold mb-4">Redes Sociais</h4>
                        <div class="flex space-x-4">
                            <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-facebook"></i></a>
                            <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-linkedin"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-12 pt-8 text-center text-gray-400">
                <p>© 2023 SOCKS5 Proxies. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

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
</body>
</html>