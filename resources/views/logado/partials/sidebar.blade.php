<aside class="sidebar bg-white dark:bg-gray-800 w-64 min-h-screen shadow-md fixed md:relative transition-colors" id="sidebar">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center space-x-3">
                    <img src="{{ Auth::user()->foto_perfil }}" alt="User" class="w-10 h-10 rounded-full">
                    <div>
                        <h3 class="font-medium text-gray-800 dark:text-gray-100">{{ Auth::user()->username }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Plano: {{ $usuario->plano }}</p>
                    </div>
                </div>
            </div>
            <nav class="p-4">
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('dashboard.show') }}" class="sidebar-link {{ request()->routeIs('dashboard.show') ? 'active' : '' }} flex items-center space-x-3 p-3 rounded-lg text-gray-700">
                            <i class="fas fa-user text-blue-500 w-5"></i>
                            <span>Perfil</span>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown">
                            <button class="sidebar-link {{ request()->routeIs('socks5.show') ? 'active' : '' }} flex items-center justify-between w-full p-3 rounded-lg text-gray-700" id="ordersDropdownBtn">
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
                        <div class="sidebar-link flex items-center space-x-3 p-3 rounded-lg text-gray-400 cursor-not-allowed opacity-60">
                            <i class="fas fa-wallet w-5"></i>
                            <span>Saldo</span>
                            <span class="ml-auto text-[10px] bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full font-semibold">Em breve</span>
                        </div>
                    </li>
                    <li>
                        <a href="{{ route('comprar.show') }}" class="sidebar-link {{ request()->routeIs('comprar.show') ? 'active' : '' }} flex items-center space-x-3 p-3 rounded-lg text-gray-700">
                            <i class="fas fa-shopping-bag text-blue-500 w-5"></i>
                            <span>Comprar</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('transacoes.show') }}" class="sidebar-link {{ request()->routeIs('transacoes.show') ? 'active' : '' }} flex items-center space-x-3 p-3 rounded-lg text-gray-700">
                            <i class="fas fa-history text-blue-500 w-5"></i>
                            <span>Histórico de Pagamentos</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('cupons.show') }}" class="sidebar-link {{ request()->routeIs('cupons.show') ? 'active' : '' }} flex items-center space-x-3 p-3 rounded-lg text-gray-700">
                            <i class="fas fa-tag text-blue-500 w-5"></i>
                            <span>Códigos Promocionais</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('duvidas.show') }}" class="sidebar-link {{ request()->routeIs('duvidas.show') ? 'active' : '' }} flex items-center space-x-3 p-3 rounded-lg text-gray-700">
                            <i class="fas fa-question-circle text-blue-500 w-5"></i>
                            <span>FAQ</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('api.show') }}" class="sidebar-link {{ request()->routeIs('api.show') ? 'active' : '' }} flex items-center space-x-3 p-3 rounded-lg text-gray-700">
                            <i class="fas fa-code text-blue-500 w-5"></i>
                            <span>API</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('logout.perform') }}" class="sidebar-link {{ request()->routeIs('logout.perform') ? 'active' : '' }} flex items-center space-x-3 p-3 rounded-lg text-gray-700">
                            <i class="fas fa-sign-out-alt text-blue-500 w-5"></i>
                            <span>Sair</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>