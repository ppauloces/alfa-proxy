{{-- resources/views/dashboard.blade.php --}}
@extends('logado.partials.app')

@section('title', 'Dashboard')

@section('content')
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Códigos Promocionais</h2>
                    <div class="mt-4 md:mt-0">
                        <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition duration-300 flex items-center space-x-2">
                            <i class="fas fa-plus"></i>
                            <span>Resgatar Código</span>
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
                                Utilize os códigos promocionais abaixo para obter descontos em sua próxima compra. Cada código possui condições específicas de uso.
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Coupons Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Coupon 1 -->
                    @foreach($cupons as $cupom)
                    <div class="coupon-card bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm card-hover relative">
                        <div class="p-5">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Ativo
                                    </span>
                                    <h3 class="text-lg font-bold text-gray-900 mt-2">{{ $cupom->cupom }}</h3>
                                </div>
                                <div class="bg-blue-500 text-white rounded-lg px-3 py-1 text-xl font-bold">
                                    {{ $cupom->desconto }}%
                                </div>
                            </div>
                            <div class="mt-4">
                                <p class="text-sm text-gray-500">
                                    <i class="fas fa-calendar-alt mr-1"></i> Válido até: 30/11/2025
                                </p>
                                <p class="text-sm text-gray-500 mt-1">
                                    <i class="fas fa-shopping-cart mr-1"></i> Mínimo de compra: R$ 100,00
                                </p>
                                <p class="text-sm text-gray-500 mt-1">
                                    <i class="fas fa-tag mr-1"></i> Aplicável em todos os produtos
                                </p>
                            </div>
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <button class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-md transition duration-300">
                                    <i class="fas fa-copy mr-2"></i> Copiar Código
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    
                    <!-- 
                    <div class="coupon-card bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm card-hover relative">
                        <div class="coupon-tag">EXCLUSIVO</div>
                        <div class="p-5">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Ativo
                                    </span>
                                    <h3 class="text-lg font-bold text-gray-900 mt-2">PREMIUM50</h3>
                                </div>
                                <div class="bg-purple-500 text-white rounded-lg px-3 py-1 text-xl font-bold">
                                    50%
                                </div>
                            </div>
                            <div class="mt-4">
                                <p class="text-sm text-gray-500">
                                    <i class="fas fa-calendar-alt mr-1"></i> Válido até: 31/12/2023
                                </p>
                                <p class="text-sm text-gray-500 mt-1">
                                    <i class="fas fa-shopping-cart mr-1"></i> Apenas para assinatura Premium
                                </p>
                                <p class="text-sm text-gray-500 mt-1">
                                    <i class="fas fa-tag mr-1"></i> Primeira compra apenas
                                </p>
                            </div>
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <button class="w-full bg-purple-500 hover:bg-purple-600 text-white py-2 px-4 rounded-md transition duration-300">
                                    <i class="fas fa-copy mr-2"></i> Copiar Código
                                </button>
                            </div>
                        </div>
                    </div>
                    

                    <div class="coupon-card bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm card-hover relative">
                        <div class="p-5">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Ativo
                                    </span>
                                    <h3 class="text-lg font-bold text-gray-900 mt-2">SOCKS10</h3>
                                </div>
                                <div class="bg-yellow-500 text-white rounded-lg px-3 py-1 text-xl font-bold">
                                    10%
                                </div>
                            </div>
                            <div class="mt-4">
                                <p class="text-sm text-gray-500">
                                    <i class="fas fa-calendar-alt mr-1"></i> Sem data de expiração
                                </p>
                                <p class="text-sm text-gray-500 mt-1">
                                    <i class="fas fa-shopping-cart mr-1"></i> Apenas para proxies SOCKS5
                                </p>
                                <p class="text-sm text-gray-500 mt-1">
                                    <i class="fas fa-tag mr-1"></i> Mínimo de 50 proxies
                                </p>
                            </div>
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <button class="w-full bg-yellow-500 hover:bg-yellow-600 text-white py-2 px-4 rounded-md transition duration-300">
                                    <i class="fas fa-copy mr-2"></i> Copiar Código
                                </button>
                            </div>
                        </div>
                    </div>
                    

                    <div class="coupon-card bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm card-hover relative">
                        <div class="p-5">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Expirado
                                    </span>
                                    <h3 class="text-lg font-bold text-gray-400 mt-2">SUMMER15</h3>
                                </div>
                                <div class="bg-gray-400 text-white rounded-lg px-3 py-1 text-xl font-bold">
                                    15%
                                </div>
                            </div>
                            <div class="mt-4">
                                <p class="text-sm text-gray-400">
                                    <i class="fas fa-calendar-alt mr-1"></i> Expirado em: 31/08/2023
                                </p>
                                <p class="text-sm text-gray-400 mt-1">
                                    <i class="fas fa-shopping-cart mr-1"></i> Mínimo de compra: R$ 50,00
                                </p>
                                <p class="text-sm text-gray-400 mt-1">
                                    <i class="fas fa-tag mr-1"></i> Aplicável em todos os produtos
                                </p>
                            </div>
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <button class="w-full bg-gray-400 cursor-not-allowed text-white py-2 px-4 rounded-md" disabled>
                                    <i class="fas fa-times mr-2"></i> Expirado
                                </button>
                            </div>
                        </div>
                    </div>
                    

                    <div class="coupon-card bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm card-hover relative">
                        <div class="coupon-tag">NOVO</div>
                        <div class="p-5">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Ativo
                                    </span>
                                    <h3 class="text-lg font-bold text-gray-900 mt-2">CRYPTO20</h3>
                                </div>
                                <div class="bg-orange-500 text-white rounded-lg px-3 py-1 text-xl font-bold">
                                    20%
                                </div>
                            </div>
                            <div class="mt-4">
                                <p class="text-sm text-gray-500">
                                    <i class="fas fa-calendar-alt mr-1"></i> Válido até: 15/01/2024
                                </p>
                                <p class="text-sm text-gray-500 mt-1">
                                    <i class="fas fa-shopping-cart mr-1"></i> Apenas para pagamentos em criptomoedas
                                </p>
                                <p class="text-sm text-gray-500 mt-1">
                                    <i class="fas fa-tag mr-1"></i> Mínimo de R$ 200,00 em Bitcoin
                                </p>
                            </div>
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <button class="w-full bg-orange-500 hover:bg-orange-600 text-white py-2 px-4 rounded-md transition duration-300">
                                    <i class="fas fa-copy mr-2"></i> Copiar Código
                                </button>
                            </div>
                        </div>
                    </div>
                    

                    <div class="coupon-card bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm card-hover relative">
                        <div class="p-5">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Ativo
                                    </span>
                                    <h3 class="text-lg font-bold text-gray-900 mt-2">FIRST5</h3>
                                </div>
                                <div class="bg-blue-400 text-white rounded-lg px-3 py-1 text-xl font-bold">
                                    5%
                                </div>
                            </div>
                            <div class="mt-4">
                                <p class="text-sm text-gray-500">
                                    <i class="fas fa-calendar-alt mr-1"></i> Sem data de expiração
                                </p>
                                <p class="text-sm text-gray-500 mt-1">
                                    <i class="fas fa-shopping-cart mr-1"></i> Para todos os novos usuários
                                </p>
                                <p class="text-sm text-gray-500 mt-1">
                                    <i class="fas fa-tag mr-1"></i> Apenas na primeira compra
                                </p>
                            </div>
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <button class="w-full bg-blue-400 hover:bg-blue-500 text-white py-2 px-4 rounded-md transition duration-300">
                                    <i class="fas fa-copy mr-2"></i> Copiar Código
                                </button>
                            </div>
                        </div>
                    </div> 
-->
                </div>
                
                <!-- How to Use Section -->
                <div class="mt-10 bg-gray-50 p-6 rounded-lg">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Como usar os códigos promocionais?</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 bg-blue-100 p-3 rounded-full text-blue-500">
                                <i class="fas fa-search"></i>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-bold text-gray-800">1. Escolha seu código</h4>
                                <p class="text-sm text-gray-600 mt-1">Selecione um dos códigos promocionais disponíveis acima que atenda às suas necessidades.</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 bg-blue-100 p-3 rounded-full text-blue-500">
                                <i class="fas fa-copy"></i>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-bold text-gray-800">2. Copie o código</h4>
                                <p class="text-sm text-gray-600 mt-1">Clique no botão "Copiar Código" para copiar automaticamente o código promocional.</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 bg-blue-100 p-3 rounded-full text-blue-500">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-bold text-gray-800">3. Aplique no carrinho</h4>
                                <p class="text-sm text-gray-600 mt-1">No momento da compra, cole o código no campo "Cupom de desconto" antes de finalizar o pagamento.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Terms and Conditions -->
                <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                    <h4 class="text-sm font-bold text-gray-800 mb-2">Termos e condições:</h4>
                    <ul class="text-xs text-gray-600 list-disc pl-5 space-y-1">
                        <li>Cada código promocional possui condições específicas de uso.</li>
                        <li>Descontos não são cumulativos com outras promoções.</li>
                        <li>O desconto será aplicado apenas se todas as condições forem atendidas.</li>
                        <li>ProxyAlfa reserva-se o direito de alterar ou cancelar promoções a qualquer momento.</li>
                    </ul>
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
    </script>
@endsection
