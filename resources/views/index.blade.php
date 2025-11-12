<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AlfaProxy - Proxies SOCKS5 Premium</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .dropdown {
            position: relative;
            display: inline-block;
            width: 100%;
        }
        
        .dropdown-select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 1em;
        }
        
        .dropdown-content {
            display: none;
            position: absolute;
            width: 100%;
            z-index: 1;
        }
        
        .dropdown:hover .dropdown-content {
            display: block;
        }
        
        .price-display {
            transition: all 0.3s ease;
        }
        
        @media (max-width: 768px) {
            .form-container {
                flex-direction: column;
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
                <a href="{{ route('inicial') }}" class="nav-link font-sf-pro text-base text-sf-nav font-medium px-4 py-2 rounded-lg">Início</a>
                <a href="{{ route('inicial') }}" class="nav-link font-sf-pro text-base text-sf-nav font-medium px-4 py-2 rounded-lg">Planos</a>
                <a href="{{ route('inicial') }}" class="nav-link font-sf-pro text-base text-sf-nav font-medium px-4 py-2 rounded-lg">API</a>
                <a href="{{ route('duvidas.show') }}" class=" nav-link font-sf-pro text-base text-sf-nav font-medium px-4 py-2 rounded-lg">Suporte</a>
            </nav>
            <div class="flex items-center space-x-4">
                <a href="{{ route('login.show') }}"
                   data-ripple-light="true"
                   class="flex items-center text-base gap-2 select-none text-center text-[#2055d5] hover:text-white transition-all py-2 px-4 rounded-xl no-underline bg-[#DCE5FD] hover:bg-[#2055d5] "
                   style="  transition: background-color 0.3s; font-weight: 500;">
                    <i class="fas fa-user"></i>
                    <span>Log-in</span>
                </a> 
                <a href="#calculator" class="text-gray-600 hover:text-blue-500"><i class="fas fa-shopping-cart"></i></a>
                <button class="md:hidden text-gray-600">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-blue-500 to-blue-700 text-white py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto text-center">
                <h2 class="text-4xl font-bold mb-4">Proxies SOCKS5 Premium de Alta Velocidade</h2>
                <p class="text-xl mb-8">Acesso a conteúdos globais com nossa rede de proxies dedicados. Velocidade, segurança e anonimato garantidos.</p>
                <a href="#calculator" class="bg-white text-blue-600 font-bold py-3 px-8 rounded-full hover:bg-gray-100 transition duration-300 inline-block">Compre Agora</a>
            </div>
        </div>
    </section>

    <!-- Calculator Section -->
    <section id="calculator" class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto bg-gray-50 rounded-xl shadow-md overflow-hidden">
                <div class="p-8">
                    <h3 class="text-2xl font-bold text-gray-800 mb-6">Monte seu pacote de proxies</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <!-- Proxy Type -->
                        <div>
                            <label for="proxyType" class="block text-sm font-medium text-gray-700 mb-2">Tipo de Proxy</label>
                            <div class="dropdown">
                                <select id="proxyType" class="dropdown-select w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                                    <option value="socks5" selected>SOCKS5</option>
                                    <option value="http">HTTP</option>
                                    <option value="https">HTTPS</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Country -->
                        <div>
                            <label for="country" class="block text-sm font-medium text-gray-700 mb-2">País</label>
                            <div class="dropdown">
                                <select id="country" class="dropdown-select w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                                    <option value="br" selected>Brasil</option>
                                    <option value="us">Estados Unidos</option>
                                    <option value="uk">Reino Unido</option>
                                    <option value="de">Alemanha</option>
                                    <option value="fr">França</option>
                                    <option value="jp">Japão</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Rental Period -->
                        <div>
                            <label for="rentalPeriod" class="block text-sm font-medium text-gray-700 mb-2">Período de Aluguel</label>
                            <div class="dropdown">
                                <select id="rentalPeriod" class="dropdown-select w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                                    <option value="7">7 dias</option>
                                    <option value="14">14 dias</option>
                                    <option value="30" selected>30 dias</option>
                                    <option value="90">90 dias</option>
                                    <option value="180">180 dias</option>
                                    <option value="365">365 dias</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Quantity -->
                        <div>
                            <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">Quantidade</label>
                            <input type="number" id="quantity" min="1" value="1" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    
                    <!-- Price Display -->
                    <div class="bg-blue-50 border border-blue-100 rounded-lg p-6 mb-8">
                        <div class="flex justify-between items-center">
                            <div>
                                <h4 class="text-lg font-semibold text-gray-800">Total do Pedido</h4>
                                <p class="text-sm text-gray-600">Preço calculado com base nas suas seleções</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-600">Preço total</p>
                                <p id="totalPrice" class="text-3xl font-bold text-blue-600">R$ 29,90</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Submit Button -->
                    <button id="submitBtn" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-6 rounded-lg transition duration-300 flex items-center justify-center space-x-2">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Comprar Agora</span>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <h3 class="text-3xl font-bold text-center text-gray-800 mb-12">Por que escolher a ProxyAlfa?</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition duration-300">
                    <div class="text-blue-500 mb-4">
                        <i class="fas fa-bolt text-4xl"></i>
                    </div>
                    <h4 class="text-xl font-bold mb-3 text-gray-800">Alta Velocidade</h4>
                    <p class="text-gray-600">Nossos servidores são otimizados para oferecer a máxima velocidade de conexão com baixa latência.</p>
                </div>
                
                <div class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition duration-300">
                    <div class="text-blue-500 mb-4">
                        <i class="fas fa-shield-alt text-4xl"></i>
                    </div>
                    <h4 class="text-xl font-bold mb-3 text-gray-800">Segurança Total</h4>
                    <p class="text-gray-600">Criptografia avançada e protocolos seguros para proteger seus dados e sua privacidade online.</p>
                </div>
                
                <div class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition duration-300">
                    <div class="text-blue-500 mb-4">
                        <i class="fas fa-headset text-4xl"></i>
                    </div>
                    <h4 class="text-xl font-bold mb-3 text-gray-800">Suporte 24/7</h4>
                    <p class="text-gray-600">Nossa equipe de suporte está disponível a qualquer momento para ajudar com qualquer questão.</p>
                </div>
            </div>
        </div>
    </section>

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
        // Price calculation
        const proxyType = document.getElementById('proxyType');
        const country = document.getElementById('country');
        const rentalPeriod = document.getElementById('rentalPeriod');
        const quantity = document.getElementById('quantity');
        const totalPrice = document.getElementById('totalPrice');
        const submitBtn = document.getElementById('submitBtn');
        
        // Base prices
        const basePrices = {
            socks5: 29.90,
            http: 19.90,
            https: 24.90
        };
        
        // Country multipliers
        const countryMultipliers = {
            br: 1.0,
            us: 1.2,
            uk: 1.3,
            de: 1.25,
            fr: 1.25,
            jp: 1.4
        };
        
        // Rental period discounts
        const rentalDiscounts = {
            7: 0,
            14: 0.05,
            30: 0.1,
            90: 0.15,
            180: 0.2,
            365: 0.25
        };
        
        function calculatePrice() {
            const selectedType = proxyType.value;
            const selectedCountry = country.value;
            const selectedPeriod = rentalPeriod.value;
            const selectedQuantity = parseInt(quantity.value);
            
            let price = basePrices[selectedType] * countryMultipliers[selectedCountry];
            price = price * (1 - rentalDiscounts[selectedPeriod]);
            price = price * selectedQuantity;
            
            totalPrice.textContent = `R$ ${price.toFixed(2).replace('.', ',')}`;
        }
        
        // Event listeners
        proxyType.addEventListener('change', calculatePrice);
        country.addEventListener('change', calculatePrice);
        rentalPeriod.addEventListener('change', calculatePrice);
        quantity.addEventListener('input', calculatePrice);
        
        // Submit button
        submitBtn.addEventListener('click', function() {
            alert('Pedido enviado com sucesso! Redirecionando para o pagamento...');
        });
        
        // Initialize price
        calculatePrice();
    </script>
    <!-- Material Tailwind Ripple Effect -->
    <script async src="https://unpkg.com/@material-tailwind/html@latest/scripts/ripple.js"></script>
</body>
</html>