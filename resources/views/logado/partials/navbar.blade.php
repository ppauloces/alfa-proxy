<header class="bg-white dark:bg-gray-800 shadow-sm transition-colors">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <button class="md:hidden text-gray-600" id="sidebarToggle">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <div class="flex items-center space-x-2">
                    <img src="{!! asset('images/logoproxy.webp') !!}" alt="Logo" height="150" width="200">
                </div>
            </div>
            <nav class="hidden md:flex space-x-6 items-center">
                <a href="{{ route('dashboard.show') }}" class="text-gray-600 dark:text-gray-300 hover:text-blue-500 dark:hover:text-blue-400 font-medium transition-colors">Dashboard</a>
                <a href="{{ route('socks5.show') }}" class="text-gray-600 dark:text-gray-300 hover:text-blue-500 dark:hover:text-blue-400 font-medium transition-colors">Proxies</a>
                <div class="flex items-center space-x-2 text-gray-400 dark:text-gray-500 cursor-not-allowed opacity-60">
                    <span class="font-medium">Saldo</span>
                    <span class="text-[10px] bg-amber-100 dark:bg-amber-900 text-amber-700 dark:text-amber-300 px-2 py-0.5 rounded-full font-semibold">Em breve</span>
                </div>
                <a href="{{ route('api.show') }}" class="text-gray-600 dark:text-gray-300 hover:text-blue-500 dark:hover:text-blue-400 font-medium transition-colors">API</a>
                <a href="{{ route('faq') }}" class="text-gray-600 dark:text-gray-300 hover:text-blue-500 dark:hover:text-blue-400 font-medium transition-colors">Suporte</a>
            </nav>
            <div class="flex items-center space-x-4">
                <!-- Dark Mode Toggle -->
                <button onclick="toggleDarkMode()" class="text-gray-600 dark:text-gray-300 hover:text-blue-500 dark:hover:text-blue-400 transition-colors" title="Alternar modo escuro">
                    <i class="fas fa-moon dark:hidden"></i>
                    <i class="fas fa-sun hidden dark:inline"></i>
                </button>

                <div class="relative">
                    <button class="text-gray-600 dark:text-gray-300 hover:text-blue-500 dark:hover:text-blue-400 transition-colors" id="notificationBtn">
                        <i class="fas fa-bell"></i>
                        <span class="absolute top-0 right-0 h-2 w-2 rounded-full bg-red-500"></span>
                    </button>
                    <div class="hidden absolute right-0 mt-2 w-64 bg-white dark:bg-gray-700 rounded-md shadow-lg py-1 z-50" id="notificationDropdown">
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600">Nova mensagem</a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600">Atualização do sistema</a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600">Promoção especial</a>
                    </div>
                </div>
                <div class="relative">
                    <button class="flex items-center space-x-2" id="userMenuBtn">
                        <img src="{{ Auth::user()->foto_perfil }}" alt="User" class="w-8 h-8 rounded-full">
                        <span class="hidden md:inline text-gray-700 dark:text-gray-200">{{ Auth::user()->username }}</span>
                    </button>
                    <div class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 rounded-md shadow-lg py-1 z-50" id="userMenuDropdown">
                        <a href="{{ route('dashboard.show') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600">Meu Perfil</a>
                   <!-- <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600">Configurações</a> -->
                        <a href="{{ route('logout.perform') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600">Sair</a>
                    </div>
                </div>
            </div>
        </div>
    </header>