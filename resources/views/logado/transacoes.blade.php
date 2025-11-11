
@extends('logado.partials.app')

@section('content')

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
            
            .header-nav {
                display: none;
            }
            
            .payment-item td {
                padding: 0.75rem 0.5rem;
            }
            
            .payment-item td:first-child {
                padding-left: 0.5rem;
            }
            
            .payment-item td:last-child {
                padding-right: 0.5rem;
            }
            
            .payment-item .text-sm {
                font-size: 0.75rem;
            }
            
            .stats-cards {
                grid-template-columns: 1fr;
                gap: 0.75rem;
            }
            
            .filters-grid {
                grid-template-columns: 1fr;
                gap: 0.75rem;
            }
            
            .pagination-container {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }
            
            .pagination-buttons {
                flex-wrap: wrap;
                gap: 0.5rem;
            }
        }
        
        @media (max-width: 640px) {
            .payment-item {
                display: flex;
                flex-direction: column;
                position: relative;
                padding: 1rem 0;
            }
            
            .payment-item td {
                display: flex;
                padding: 0.25rem 0.5rem;
                border: none;
            }
            
            .payment-item td::before {
                content: attr(data-label);
                font-weight: 600;
                width: 120px;
                min-width: 120px;
                color: #4b5563;
            }
            
            .payment-item td:first-child {
                padding-top: 0.75rem;
            }
            
            .payment-item td:last-child {
                padding-bottom: 0.75rem;
            }
            
            .payment-item .text-sm {
                font-size: 0.75rem;
            }
            
            table thead {
                display: none;
            }
        }
    </style>

<div class="bg-white rounded-lg shadow p-4 md:p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                    <h2 class="text-xl md:text-2xl font-bold text-gray-800">Histórico de Pagamentos</h2>
                    <div class="mt-4 md:mt-0">
                        <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition duration-300 flex items-center space-x-2 text-sm md:text-base">
                            <i class="fas fa-plus"></i>
                            <span>Adicionar Saldo</span>
                        </button>
                    </div>
                </div>
                
                <!-- Stats Cards -->
                <div class="grid stats-cards gap-4 mb-6">
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-blue-600">Total Recarregado</p>
                                <p class="text-xl md:text-2xl font-bold text-blue-800">R$ {{ $totalValor }}</p>
                            </div>
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg border border-green-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-green-600">Pagamentos Aprovados</p>
                                <p class="text-xl md:text-2xl font-bold text-green-800">{{ count($pagamentos_aprovados) }}</p>
                            </div>
                            <div class="p-3 rounded-full bg-green-100 text-green-600">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-yellow-600">Pagamentos Pendentes</p>
                                <p class="text-xl md:text-2xl font-bold text-yellow-800">{{ count($pagamentos_pendentes) }}</p>
                            </div>
                            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Filters -->
                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <div class="grid filters-grid gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                <option>Todos</option>
                                <option>Aprovado</option>
                                <option>Pendente</option>
                                <option>Cancelado</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Método</label>
                            <select class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                <option>Todos</option>
                                <option>Pix</option>
                                <option>Cartão de Crédito</option>
                                <option>Boleto</option>
                                <option>Criptomoedas</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">De</label>
                            <input type="date" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Até</label>
                            <input type="date" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm transition duration-300">
                            Aplicar Filtros
                        </button>
                    </div>
                </div>
                
                <!-- Payment History Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descrição</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Método</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <!-- Payment Item 1 -->
                             @foreach($pagamentos as $pagamento)
                            <tr class="payment-item">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" data-label="ID">#PAY-{{ $pagamento->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" data-label="Data">{{ $pagamento->created_at->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap" data-label="Descrição">
                                    <div class="text-sm text-gray-900">Recarga de saldo</div>
                                    <div class="text-sm text-gray-500">Recarga de {{ $pagamento->valor }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap" data-label="Método">
                                    <div class="flex items-center">
                                        <i class="fas fa-barcode text-gray-500 mr-2"></i>
                                        <div class="text-sm text-gray-900">Pix</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium" data-label="Valor">R$ {{ $pagamento->valor }}</td>
                                <td class="px-6 py-4 whitespace-nowrap" data-label="Status">
                                @if($pagamento->status == 1)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Aprovado
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Pendente
                                    </span>
                                @endif

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" data-label="Ações">
                                    <button class="text-blue-600 hover:text-blue-900 mr-3">
                                        <i class="fas fa-receipt"></i> <span class="hidden md:inline">Recibo</span>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                            
                            <!--

                            <tr class="payment-item">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" data-label="ID">#PAY-78944</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" data-label="Data">10/10/2023</td>
                                <td class="px-6 py-4 whitespace-nowrap" data-label="Descrição">
                                    <div class="text-sm text-gray-900">Recarga de saldo</div>
                                    <div class="text-sm text-gray-500">100 proxies SOCKS5</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap" data-label="Método">
                                    <div class="flex items-center">
                                        <i class="fab fa-bitcoin text-orange-500 mr-2"></i>
                                        <div class="text-sm text-gray-900">Bitcoin</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium" data-label="Valor">R$ 180,00</td>
                                <td class="px-6 py-4 whitespace-nowrap" data-label="Status">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Aprovado
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" data-label="Ações">
                                    <button class="text-blue-600 hover:text-blue-900 mr-3">
                                        <i class="fas fa-receipt"></i> <span class="hidden md:inline">Recibo</span>
                                    </button>
                                </td>
                            </tr>
                            
                            <tr class="payment-item">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" data-label="ID">#PAY-78943</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" data-label="Data">05/10/2023</td>
                                <td class="px-6 py-4 whitespace-nowrap" data-label="Descrição">
                                    <div class="text-sm text-gray-900">Assinatura Premium</div>
                                    <div class="text-sm text-gray-500">Mensal</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap" data-label="Método">
                                    <div class="flex items-center">
                                        <i class="far fa-credit-card text-blue-500 mr-2"></i>
                                        <div class="text-sm text-gray-900">Cartão de Crédito</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium" data-label="Valor">R$ 120,00</td>
                                <td class="px-6 py-4 whitespace-nowrap" data-label="Status">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Aprovado
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" data-label="Ações">
                                    <button class="text-blue-600 hover:text-blue-900 mr-3">
                                        <i class="fas fa-receipt"></i> <span class="hidden md:inline">Recibo</span>
                                    </button>
                                </td>
                            </tr>
                            
                            <tr class="payment-item">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" data-label="ID">#PAY-78942</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" data-label="Data">28/09/2023</td>
                                <td class="px-6 py-4 whitespace-nowrap" data-label="Descrição">
                                    <div class="text-sm text-gray-900">Recarga de saldo</div>
                                    <div class="text-sm text-gray-500">25 proxies SOCKS5</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap" data-label="Método">
                                    <div class="flex items-center">
                                        <i class="fas fa-barcode text-gray-500 mr-2"></i>
                                        <div class="text-sm text-gray-900">Pix</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium" data-label="Valor">R$ 50,00</td>
                                <td class="px-6 py-4 whitespace-nowrap" data-label="Status">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Aprovado
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" data-label="Ações">
                                    <button class="text-blue-600 hover:text-blue-900 mr-3">
                                        <i class="fas fa-receipt"></i> <span class="hidden md:inline">Recibo</span>
                                    </button>
                                </td>
                            </tr>
                            
                            <tr class="payment-item">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" data-label="ID">#PAY-78941</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" data-label="Data">20/09/2023</td>
                                <td class="px-6 py-4 whitespace-nowrap" data-label="Descrição">
                                    <div class="text-sm text-gray-900">Recarga de saldo</div>
                                    <div class="text-sm text-gray-500">10 proxies HTTP</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap" data-label="Método">
                                    <div class="flex items-center">
                                        <i class="fas fa-barcode text-gray-500 mr-2"></i>
                                        <div class="text-sm text-gray-900">Boleto</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium" data-label="Valor">R$ 40,00</td>
                                <td class="px-6 py-4 whitespace-nowrap" data-label="Status">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Pendente
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" data-label="Ações">
                                    <button class="text-blue-600 hover:text-blue-900 mr-3">
                                        <i class="fas fa-receipt"></i> <span class="hidden md:inline">Recibo</span>
                                    </button>
                                </td>
                            </tr>

                        -->
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="mt-6 flex items-center justify-between pagination-container">
                    <div class="text-sm text-gray-500">
                        Mostrando <span class="font-medium">1</span> a <span class="font-medium">5</span> de <span class="font-medium">8</span> pagamentos
                    </div>
                    <div class="flex space-x-2 pagination-buttons">
                        <button class="px-3 py-1 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm">
                            Anterior
                        </button>
                        <button class="px-3 py-1 rounded-md bg-blue-600 text-white hover:bg-blue-700 text-sm">
                            1
                        </button>
                        <button class="px-3 py-1 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm">
                            2
                        </button>
                        <button class="px-3 py-1 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm">
                            Próximo
                        </button>
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
    </script>

@endsection