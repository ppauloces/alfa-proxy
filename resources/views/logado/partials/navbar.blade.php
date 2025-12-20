<header class="bg-white shadow-sm">
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
                <a href="{{ route('dashboard.show') }}" class="text-gray-600 hover:text-blue-500 font-medium">Dashboard</a>
                <a href="{{ route('socks5.show') }}" class="text-gray-600 hover:text-blue-500 font-medium">Proxies</a>
                <div class="flex items-center space-x-2 text-gray-400 cursor-not-allowed opacity-60">
                    <span class="font-medium">Saldo</span>
                    <span class="text-[10px] bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full font-semibold">Em breve</span>
                </div>
                <a href="{{ route('api.show') }}" class="text-gray-600 hover:text-blue-500 font-medium">API</a>
                <a href="{{ route('faq') }}" class="text-gray-600 hover:text-blue-500 font-medium">Suporte</a>
            </nav>
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <button class="text-gray-600 hover:text-blue-500" id="notificationBtn">
                        <i class="fas fa-bell"></i>
                        <span class="absolute top-0 right-0 h-2 w-2 rounded-full bg-red-500"></span>
                    </button>
                    <div class="hidden absolute right-0 mt-2 w-64 bg-white rounded-md shadow-lg py-1 z-50" id="notificationDropdown">
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Nova mensagem</a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Atualização do sistema</a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Promoção especial</a>
                    </div>
                </div>
                <div class="relative">
                    <button class="flex items-center space-x-2" id="userMenuBtn">
                        <img src="{{ Auth::user()->foto_perfil }}" alt="User" class="w-8 h-8 rounded-full">
                        <span class="hidden md:inline text-gray-700">{{ Auth::user()->username }}</span>
                    </button>
                    <div class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50" id="userMenuDropdown">
                        <a href="{{ route('dashboard.show') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Meu Perfil</a>
                   <!-- <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Configurações</a> -->
                        <a href="{{ route('logout.perform') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Sair</a>
                    </div>
                </div>
            </div>
        </div>
    </header>