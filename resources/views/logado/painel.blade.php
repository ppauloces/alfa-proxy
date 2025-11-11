{{-- resources/views/dashboard.blade.php --}}
@extends('logado.partials.app')

@section('title', 'Dashboard')

@section('content')

            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Meu Perfil</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Profile Info -->
                    <div class="md:col-span-2">
                        <div class="space-y-6">
                            <!-- Personal Info -->

                            <!--
                            <div>
                                <h3 class="text-lg font-medium text-gray-800 mb-4">Informações Pessoais</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Nome Completo</label>
                                        <input type="text" value="João Silva" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">CPF</label>
                                        <input type="text" value="123.456.789-00" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
                                        <input type="text" value="(11) 98765-4321" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Data de Nascimento</label>
                                        <input type="text" value="01/01/1990" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>
                            </div>
                            -->

                            <!-- Account Info -->
                            <div>
                            <form action="{{ route('trocar.senha.main') }}" method="post">
                            @csrf
                                <h3 class="text-lg font-medium text-gray-800 mb-4">Informações da Conta</h3>

                                @if (session('success'))
                                    <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
                                        {{ session('success') }}
                                    </div>
                                @endif

                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                                        <input type="email" value="{{ $usuario->email }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Nova Senha</label>
                                        <input type="password" name="password" placeholder="••••••••" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar Nova Senha</label>
                                        <input type="password" name="confirm_password" placeholder="••••••••" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                    </div>

                                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition duration-300">
                                        Salvar Alterações
                                    </button>

                                </div>
                            </form>
                            </div>
                    
                        </div>
                    </div>
                    
                    <!-- Account Summary -->
                    <div>
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <h3 class="text-lg font-medium text-gray-800 mb-4">Resumo da Conta</h3>
                            
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600">Plano Atual:</span>
                                    <span class="font-medium">{{ $usuario->plano }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600">Data de Expiração:</span>
                                    <span class="font-medium">{{ $expiracao }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600">Saldo Disponível:</span>
                                    <span class="font-medium text-green-600">R$ {{ $usuario->saldo }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600">Proxies Ativos:</span>
                                    <span class="font-medium">{{ count($usuario->stocks) }}</span>
                                </div>
                            </div>
                            
                            <div class="mt-6 space-y-3">
                                <a href="{{ route('saldo.show') }}">
                                <button class="w-full bg-blue-100 hover:bg-blue-200 text-blue-700 px-4 py-2 rounded-lg transition duration-300 flex items-center justify-center space-x-2">
                                    <i class="fas fa-plus"></i>
                                    <span>Adicionar Saldo</span>
                                </button>
                                </a>
                                <button class="w-full bg-green-100 hover:bg-green-200 text-green-700 px-4 py-2 rounded-lg transition duration-300 flex items-center justify-center space-x-2">
                                    <i class="fas fa-shopping-cart"></i>
                                    <span>Comprar Proxies</span>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Security Info -->
                        <div class="mt-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <h3 class="text-lg font-medium text-gray-800 mb-4">Segurança</h3>
                            
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600">Autenticação em 2 fatores:</span>
                                    <span class="text-red-500 font-medium">Desativado</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600">Último login:</span>
                                    <span class="font-medium">Hoje, 14:30</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600">IP:</span>
                                    <span class="font-medium">192.168.1.1</span>
                                </div>
                            </div>
                            
                            <button class="mt-4 w-full bg-yellow-100 hover:bg-yellow-200 text-yellow-700 px-4 py-2 rounded-lg transition duration-300 flex items-center justify-center space-x-2">
                                <i class="fas fa-shield-alt"></i>
                                <span>Ativar 2FA</span>
                            </button>
                        </div>
                    </div>
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
@endsection
