@extends('logado.partials.app')

@section('content')

<div class="bg-white rounded-lg shadow p-4 md:p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                    <h2 class="text-xl md:text-2xl font-bold text-gray-800">Meus Proxies SOCKS5</h2>
                    <div class="mt-4 md:mt-0 flex space-x-3">
                        <button id="downloadAllBtn" class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-3 py-1 md:px-4 md:py-2 rounded-lg transition duration-300 flex items-center space-x-2 text-sm md:text-base">
                            <i class="fas fa-download"></i>
                            <span>Baixar Todos</span>
                        </button>
                        <button id="copyAllBtn" class="bg-green-100 hover:bg-green-200 text-green-700 px-3 py-1 md:px-4 md:py-2 rounded-lg transition duration-300 flex items-center space-x-2 text-sm md:text-base">
                            <i class="fas fa-copy"></i>
                            <span>Copiar Todos</span>
                        </button>
                    </div>
                </div>
                
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 stats-grid">
                    <div class="bg-blue-50 p-3 md:p-4 rounded-lg border border-blue-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs md:text-sm text-blue-600">Proxies Ativos</p>
                                <p class="text-xl md:text-2xl font-bold text-blue-800">{{ count($usuario->stocks) }}</p>
                            </div>
                            <div class="p-2 md:p-3 rounded-full bg-blue-100 text-blue-600">
                                <i class="fas fa-link"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-green-50 p-3 md:p-4 rounded-lg border border-green-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs md:text-sm text-green-600">Expiração mais próxima</p>
                                <p class="text-xl md:text-2xl font-bold text-green-800">30/12/2023</p>
                            </div>
                            <div class="p-2 md:p-3 rounded-full bg-green-100 text-green-600">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-purple-50 p-3 md:p-4 rounded-lg border border-purple-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs md:text-sm text-purple-600">Expirando</p>
                                <p class="text-xl md:text-2xl font-bold text-purple-800">{{ count($usuario->stocks) }} / {{ count($usuario->stocks) }}</p>
                            </div>
                            <div class="p-2 md:p-3 rounded-full bg-purple-100 text-purple-600">
                                <i class="fas fa-chart-line"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Proxy List -->
                <div class="proxy-table">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 hidden md:table-header-group">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proxy</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Localização</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expiração</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <!-- Proxy Item 1 -->
                            @foreach($usuario->stocks as $proxy)
                            <tr class="proxy-item">
                                <td class="px-4 md:px-6 py-3" data-label="Proxy">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8 md:h-10 md:w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                            <i class="fas fa-server"></i>
                                        </div>
                                        <div class="ml-3 md:ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $proxy->ip }}:{{ $proxy->porta }}</div>
                                            <div class="text-xs md:text-sm text-gray-500">usuário: {{ $proxy->usuario }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 md:px-6 py-3" data-label="Localização">
                                    <div class="flex items-center">
                                        <img src="https://flagcdn.com/w20/{{ $proxy->pais }}.png" alt="{{ $proxy->pais }}" class="w-4 h-4 md:w-5 md:h-5 mr-2">
                                        <div class="text-sm text-gray-900">Brasil</div>
                                    </div>
                                </td>
                                <td class="px-4 md:px-6 py-3" data-label="Status">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Ativo
                                    </span>
                                </td>
                                <td class="px-4 md:px-6 py-3 text-sm text-gray-500" data-label="Expiração">{{ $proxy->expiracao->format('d/m/Y') }}</td>
                                <td class="px-4 md:px-6 py-3" data-label="Ações">
                                    <div class="flex space-x-2 proxy-actions">
                                        <button class="copy-btn px-2 py-1 md:px-3 md:py-1 rounded-md text-blue-600 hover:text-blue-800 text-xs md:text-sm" data-proxy="45.132.245.12:1080:user123:pass123">
                                            <i class="fas fa-copy mr-1"></i> Copiar
                                        </button>
                                        <button class="download-btn px-2 py-1 md:px-3 md:py-1 rounded-md text-green-600 hover:text-green-800 text-xs md:text-sm" data-proxy="45.132.245.12:1080:user123:pass123">
                                            <i class="fas fa-download mr-1"></i> Baixar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="mt-6 flex items-center justify-between pagination-container">
                    <div class="text-sm text-gray-500">
                        Mostrando <span class="font-medium">1</span> a <span class="font-medium">5</span> de <span class="font-medium">{{ count($usuario->stocks) }}</span> proxies
                    </div>
                    <div class="flex space-x-2 pagination-buttons">
                        <button class="px-2 py-1 md:px-3 md:py-1 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm">
                            Anterior
                        </button>
                        <button class="px-2 py-1 md:px-3 md:py-1 rounded-md bg-blue-600 text-white hover:bg-blue-700 text-sm">
                            1
                        </button>
                        <button class="px-2 py-1 md:px-3 md:py-1 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm">
                            2
                        </button>
                        <button class="px-2 py-1 md:px-3 md:py-1 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm">
                            3
                        </button>
                        <button class="px-2 py-1 md:px-3 md:py-1 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm">
                            Próximo
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Copy Success Notification -->
            <div id="copyNotification" class="fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg hidden items-center space-x-2">
                <i class="fas fa-check-circle"></i>
                <span>Copiado para a área de transferência!</span>
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

        // Copy proxy functionality
        const copyButtons = document.querySelectorAll('.copy-btn');
        const copyAllBtn = document.getElementById('copyAllBtn');
        const copyNotification = document.getElementById('copyNotification');
        
        copyButtons.forEach(button => {
            button.addEventListener('click', () => {
                const proxy = button.getAttribute('data-proxy');
                navigator.clipboard.writeText(proxy).then(() => {
                    copyNotification.classList.remove('hidden');
                    setTimeout(() => {
                        copyNotification.classList.add('hidden');
                    }, 3000);
                });
            });
        });
        
        copyAllBtn.addEventListener('click', () => {
            const allProxies = Array.from(document.querySelectorAll('.copy-btn')).map(btn => btn.getAttribute('data-proxy')).join('\n');
            navigator.clipboard.writeText(allProxies).then(() => {
                copyNotification.classList.remove('hidden');
                setTimeout(() => {
                    copyNotification.classList.add('hidden');
                }, 3000);
            });
        });

        // Download proxy functionality
        const downloadButtons = document.querySelectorAll('.download-btn');
        const downloadAllBtn = document.getElementById('downloadAllBtn');
        
        downloadButtons.forEach(button => {
            button.addEventListener('click', () => {
                const proxy = button.getAttribute('data-proxy');
                const blob = new Blob([proxy], { type: 'text/plain' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'proxy.txt';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
            });
        });
        
        downloadAllBtn.addEventListener('click', () => {
            const allProxies = Array.from(document.querySelectorAll('.copy-btn')).map(btn => btn.getAttribute('data-proxy')).join('\n');
            const blob = new Blob([allProxies], { type: 'text/plain' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'all_proxies.txt';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        });

        // Responsive table functionality
        function setupMobileTable() {
            if (window.innerWidth < 768) {
                const rows = document.querySelectorAll('.proxy-item');
                const headers = document.querySelectorAll('thead th');
                
                rows.forEach(row => {
                    const cells = row.querySelectorAll('td');
                    cells.forEach((cell, index) => {
                        if (headers[index]) {
                            cell.setAttribute('data-label', headers[index].textContent);
                        }
                    });
                });
            }
        }
        
        // Run on load and resize
        window.addEventListener('load', setupMobileTable);
        window.addEventListener('resize', setupMobileTable);
    </script>

@endsection