{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Minha Aplicação')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
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

        /* Dark mode transitions */
        * {
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
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
        }
        
        .token-display {
            font-family: 'Courier New', monospace;
            background-color: #f3f4f6;
            border: 1px dashed #d1d5db;
            padding: 12px;
            border-radius: 6px;
            position: relative;
        }

        .dark .token-display {
            background-color: #374151;
            border-color: #4b5563;
        }
        
        .copy-btn {
            position: absolute;
            right: 10px;
            top: 10px;
        }
        
        .api-method {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 12px;
            margin-right: 8px;
        }
        
        .get-method {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .post-method {
            background-color: #dbeafe;
            color: #1e40af;
        }
        
        .delete-method {
            background-color: #fee2e2;
            color: #991b1b;
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js']) {{-- ou seus assets --}}
</head>
<body class="bg-gray-100 dark:bg-gray-900 font-sans leading-normal tracking-normal">

    <div class="overlay" id="overlay"></div>

    @include('logado.partials.navbar')

    <div class="flex">
        @include('logado.partials.sidebar')

        {{-- Aqui vai o conteúdo específico de cada página --}}
        <main class="flex-1 p-6">
            @yield('content')
        </main>
    </div>

</body>
</html>
