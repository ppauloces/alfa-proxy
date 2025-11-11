@extends('logado.partials.app')

@section('content')

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Comprar Proxies</h2>
                    <div class="mt-4 md:mt-0">
                        <span class="text-gray-600">Saldo disponível:</span>
                        <span class="ml-2 font-medium text-green-600">R$ 0.00</span>
                    </div>
                </div>
                
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-800 mb-4">Tipo de Proxy</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <button class="proxy-type-btn bg-blue-100 text-blue-700 px-4 py-3 rounded-lg font-medium flex items-center justify-center space-x-2" data-type="socks5">
                            <i class="fas fa-globe"></i>
                            <span>SOCKS5</span>
                        </button>
                        <button class="proxy-type-btn bg-gray-100 text-gray-700 px-4 py-3 rounded-lg font-medium flex items-center justify-center space-x-2 opacity-50 cursor-not-allowed" disabled>
                            <i class="fas fa-lock"></i>
                            <span>HTTP(S)</span>
                            <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">Em breve</span>
                        </button>
                        <button class="proxy-type-btn bg-gray-100 text-gray-700 px-4 py-3 rounded-lg font-medium flex items-center justify-center space-x-2 opacity-50 cursor-not-allowed" disabled>
                            <i class="fas fa-exchange-alt"></i>
                            <span>Residencial</span>
                            <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">Em breve</span>
                        </button>
                    </div>
                </div>
                
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-800 mb-4">Localização</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <button class="location-btn bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-3 rounded-lg font-medium flex items-center justify-center space-x-2" data-location="brasil">
                            <img src="https://flagcdn.com/w20/br.png" alt="Brasil" class="w-5 h-3">
                            <span>Brasil</span>
                        </button>
                        <button class="location-btn bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-3 rounded-lg font-medium flex items-center justify-center space-x-2" data-location="eua">
                            <img src="https://flagcdn.com/w20/us.png" alt="EUA" class="w-5 h-3">
                            <span>Estados Unidos</span>
                        </button>
                        <button class="location-btn bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-3 rounded-lg font-medium flex items-center justify-center space-x-2" data-location="europa">
                            <i class="fas fa-globe-europe"></i>
                            <span>Europa</span>
                        </button>
                        <button class="location-btn bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-3 rounded-lg font-medium flex items-center justify-center space-x-2" data-location="asia">
                            <i class="fas fa-globe-asia"></i>
                            <span>Ásia</span>
                        </button>
                    </div>
                </div>
                
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-800 mb-4">Quantidade de Proxies</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Custom Quantity Input -->
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                            <h4 class="text-lg font-bold text-gray-800 mb-4">Quantidade Personalizada</h4>
                            
                            <div class="mb-4">
                                <label for="custom-quantity" class="block text-sm font-medium text-gray-700 mb-2">Número de Proxies</label>
                                <div class="relative">
                                    <input type="number" id="custom-quantity" min="1" max="1000" value="1" 
                                           class="quantity-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <span class="text-gray-500">unidades</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Preço Unitário</label>
                                <div class="text-2xl font-bold text-gray-900">R$ 20,00</div>
                            </div>
                            
                            <div class="border-t border-gray-200 my-4"></div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-medium text-gray-700">Total:</span>
                                <span id="custom-total" class="text-2xl font-bold text-blue-600">R$ 5,00</span>
                            </div>
                            
                            <button id="custom-select-btn" class="w-full mt-4 bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-2 px-4 rounded-lg transition duration-300">
                                Selecionar Quantidade
                            </button>
                        </div>
                        
                        <!-- Predefined Plans -->
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                            <h4 class="text-lg font-bold text-gray-800 mb-4">Planos Pré-definidos</h4>
                            
                            <div class="space-y-4">
                                <!-- Proxy Plan 1 -->
                                <div class="proxy-card border border-gray-200 rounded-lg p-4 cursor-pointer" data-plan="1">
                                    <div class="flex justify-between items-start">
                                        <h4 class="text-lg font-bold text-gray-800">1 Proxy</h4>
                                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">Popular</span>
                                    </div>
                                    <div class="mt-2">
                                        <span class="text-xl font-bold text-gray-900">R$ 5,00</span>
                                        <span class="text-gray-600">/mês</span>
                                    </div>
                                    <button class="w-full mt-3 bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-2 px-4 rounded-lg transition duration-300">
                                        Selecionar Plano
                                    </button>
                                </div>
                                
                                <!-- Proxy Plan 2 -->
                                <div class="proxy-card border border-gray-200 rounded-lg p-4 cursor-pointer" data-plan="5">
                                    <div class="flex justify-between items-start">
                                        <h4 class="text-lg font-bold text-gray-800">5 Proxies</h4>
                                        <span class="bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-0.5 rounded">Melhor custo-benefício</span>
                                    </div>
                                    <div class="mt-2">
                                        <span class="text-xl font-bold text-gray-900">R$ 20,00</span>
                                        <span class="text-gray-600">/mês</span>
                                    </div>
                                    <button class="w-full mt-3 bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-2 px-4 rounded-lg transition duration-300">
                                        Selecionar Plano
                                    </button>
                                </div>
                                
                                <!-- Proxy Plan 3 -->
                                <div class="proxy-card border border-gray-200 rounded-lg p-4 cursor-pointer" data-plan="10">
                                    <div class="flex justify-between items-start">
                                        <h4 class="text-lg font-bold text-gray-800">10 Proxies</h4>
                                    </div>
                                    <div class="mt-2">
                                        <span class="text-xl font-bold text-gray-900">R$ 35,00</span>
                                        <span class="text-gray-600">/mês</span>
                                    </div>
                                    <button class="w-full mt-3 bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-2 px-4 rounded-lg transition duration-300">
                                        Selecionar Plano
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Order Summary -->
                <div class="bg-gray-50 rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-medium text-gray-800 mb-4">Resumo do Pedido</h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Tipo de Proxy:</span>
                            <span class="font-medium" id="selected-type">Nenhum selecionado</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Localização:</span>
                            <span class="font-medium" id="selected-location">Nenhuma selecionada</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Quantidade:</span>
                            <span class="font-medium" id="selected-quantity">0</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Valor Unitário:</span>
                            <span class="font-medium" id="selected-unit-price">R$ 0,00</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Valor Total:</span>
                            <span class="font-medium" id="selected-price">R$ 0,00</span>
                        </div>
                        <div class="border-t border-gray-200 my-4"></div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Total a Pagar:</span>
                            <span class="text-xl font-bold text-blue-600" id="total-price">R${{ $usuario->saldo }}</span>
                        </div>
                    </div>
                    
                    <div class="mt-6 space-y-3">
                        <button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition duration-300 flex items-center justify-center space-x-2" id="checkout-btn" disabled>
                            <i class="fas fa-shopping-cart"></i>
                            <span>Finalizar Compra</span>
                        </button>
                        <div class="text-center text-sm text-gray-500">
                            <p>Você será redirecionado para o pagamento seguro</p>
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
            if (link.href === window.location.href) {
                link.classList.add('active');
            }
            
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
        
        // Proxy purchase functionality
        const proxyTypeBtns = document.querySelectorAll('.proxy-type-btn');
        const locationBtns = document.querySelectorAll('.location-btn');
        const proxyCards = document.querySelectorAll('.proxy-card');
        const checkoutBtn = document.getElementById('checkout-btn');
        const customQuantityInput = document.getElementById('custom-quantity');
        const customTotalElement = document.getElementById('custom-total');
        const customSelectBtn = document.getElementById('custom-select-btn');
        
        // Unit price (same for all proxies)
        const unitPrice = 20.00;
        
        let selectedType = null;
        let selectedLocation = null;
        let selectedQuantity = 0;
        let selectedPrice = 0;
        let isCustomQuantity = false;
        
        // Calculate custom quantity total
        function calculateCustomTotal() {
            const quantity = parseInt(customQuantityInput.value) || 1;
            const total = quantity * unitPrice;
            customTotalElement.textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;
        }
        
        // Update custom quantity when input changes
        customQuantityInput.addEventListener('input', () => {
            // Ensure minimum value of 1
            if (parseInt(customQuantityInput.value) < 1) {
                customQuantityInput.value = 1;
            }
            calculateCustomTotal();
        });
        
        // Select custom quantity
        customSelectBtn.addEventListener('click', () => {
            const quantity = parseInt(customQuantityInput.value) || 1;
            const total = quantity * unitPrice;
            
            // Deselect any predefined plans
            proxyCards.forEach(card => {
                card.classList.remove('selected', 'border-blue-500');
                card.querySelector('button').classList.remove('bg-blue-600', 'text-white');
                card.querySelector('button').classList.add('bg-gray-100', 'text-gray-800', 'hover:bg-gray-200');
            });
            
            // Update selection
            selectedQuantity = quantity;
            selectedPrice = total;
            isCustomQuantity = true;
            
            // Update order summary
            document.getElementById('selected-quantity').textContent = selectedQuantity;
            document.getElementById('selected-unit-price').textContent = `R$ ${unitPrice.toFixed(2).replace('.', ',')}`;
            document.getElementById('selected-price').textContent = `R$ ${selectedPrice.toFixed(2).replace('.', ',')}`;
            
            // Highlight custom selection
            customSelectBtn.classList.remove('bg-gray-100', 'text-gray-800', 'hover:bg-gray-200');
            customSelectBtn.classList.add('bg-blue-600', 'text-white');
            
            updateOrderSummary();
        });
        
        // Select proxy type
        proxyTypeBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                if (btn.disabled) return;
                
                proxyTypeBtns.forEach(b => {
                    b.classList.remove('bg-blue-100', 'text-blue-700');
                    b.classList.add('bg-gray-100', 'text-gray-700');
                });
                
                btn.classList.remove('bg-gray-100', 'text-gray-700');
                btn.classList.add('bg-blue-100', 'text-blue-700');
                
                selectedType = btn.dataset.type;
                document.getElementById('selected-type').textContent = 'SOCKS5';
                updateOrderSummary();
            });
        });
        
        // Select location
        locationBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                locationBtns.forEach(b => {
                    b.classList.remove('bg-blue-100', 'text-blue-700');
                    b.classList.add('bg-gray-100', 'text-gray-800', 'hover:bg-gray-200');
                });
                
                btn.classList.remove('bg-gray-100', 'text-gray-800', 'hover:bg-gray-200');
                btn.classList.add('bg-blue-100', 'text-blue-700');
                
                selectedLocation = btn.dataset.location;
                
                // Map location codes to display names
                const locationNames = {
                    'brasil': 'Brasil',
                    'eua': 'Estados Unidos',
                    'europa': 'Europa',
                    'asia': 'Ásia'
                };
                
                document.getElementById('selected-location').textContent = locationNames[selectedLocation];
                updateOrderSummary();
            });
        });
        
        // Select proxy plan
        proxyCards.forEach(card => {
            card.addEventListener('click', () => {
                proxyCards.forEach(c => {
                    c.classList.remove('selected', 'border-blue-500');
                    c.querySelector('button').classList.remove('bg-blue-600', 'text-white');
                    c.querySelector('button').classList.add('bg-gray-100', 'text-gray-800', 'hover:bg-gray-200');
                });
                
                card.classList.add('selected', 'border-blue-500');
                card.querySelector('button').classList.remove('bg-gray-100', 'text-gray-800', 'hover:bg-gray-200');
                card.querySelector('button').classList.add('bg-blue-600', 'text-white');
                
                // Reset custom selection
                customSelectBtn.classList.remove('bg-blue-600', 'text-white');
                customSelectBtn.classList.add('bg-gray-100', 'text-gray-800', 'hover:bg-gray-200');
                isCustomQuantity = false;
                
                selectedQuantity = card.dataset.plan;
                selectedPrice = selectedQuantity * unitPrice;
                
                document.getElementById('selected-quantity').textContent = selectedQuantity;
                document.getElementById('selected-unit-price').textContent = `R$ ${unitPrice.toFixed(2).replace('.', ',')}`;
                document.getElementById('selected-price').textContent = `R$ ${selectedPrice.toFixed(2).replace('.', ',')}`;
                updateOrderSummary();
            });
        });
        
        // Update order summary and enable/disable checkout button
        function updateOrderSummary() {
            const totalElement = document.getElementById('total-price');
            
            if (selectedType && selectedLocation && selectedQuantity > 0) {
                totalElement.textContent = `R$ ${selectedPrice.toFixed(2).replace('.', ',')}`;
                checkoutBtn.disabled = false;
                checkoutBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            } else {
                totalElement.textContent = 'R$ 0,00';
                checkoutBtn.disabled = true;
                checkoutBtn.classList.add('opacity-50', 'cursor-not-allowed');
            }
        }
        
        // Checkout button click handler
        checkoutBtn.addEventListener('click', () => {
            // Here you would typically redirect to payment page or show payment modal
            alert(`Redirecionando para pagamento de ${selectedQuantity} proxies SOCKS5 (${selectedLocation}) por R$ ${selectedPrice.toFixed(2)}`);
            
            // In a real implementation, you would submit a form or make an API call
            // window.location.href = `/checkout?type=${selectedType}&location=${selectedLocation}&quantity=${selectedQuantity}`;
        });
        
        // Initialize custom quantity calculation
        calculateCustomTotal();
    </script>

@endsection