<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AlfaProxy - Proxies SOCKS5 Premium</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrambleTextPlugin.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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

        #alfaLogo {
            width: 180px;
            height: auto;
            margin-bottom: 20px;
        }

        #alfaLogo path {
            fill: none;
            stroke: #ffffff;
            stroke-width: 3;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        /* Hero Text Animations */
        @keyframes shimmer {
            0% {
                background-position: 200% center;
            }

            100% {
                background-position: -200% center;
            }
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        @keyframes glow {

            0%,
            100% {
                filter: drop-shadow(0 0 8px rgba(96, 165, 250, 0.4));
            }

            50% {
                filter: drop-shadow(0 0 16px rgba(96, 165, 250, 0.8)) drop-shadow(0 0 24px rgba(96, 165, 250, 0.4));
            }
        }

        .hero-main {
            display: inline-block;
            animation: float 4s ease-in-out infinite;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .hero-highlight {
            animation: glow 3s ease-in-out infinite;
            text-shadow: 0 0 20px rgba(96, 165, 250, 0.5);
        }

        .hero-btn-primary {
            transition: all 0.3s ease;
        }

        .hero-btn-primary:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 8px 20px rgba(96, 165, 250, 0.4);
        }

        .hero-btn-secondary {
            transition: all 0.3s ease;
        }

        .hero-btn-secondary:hover {
            transform: translateY(-2px) scale(1.02);
            border-color: rgba(255, 255, 255, 0.6);
            box-shadow: 0 4px 12px rgba(255, 255, 255, 0.1);
        }

        /* Proxy Calculator Styles */
        .proxy-calculator {
            font-family: 'Onest', sans-serif;
        }

        .calculator-card {
            min-height: 62px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .calculator-card select {
            font-family: 'Onest', sans-serif;
            border-radius: 12px;
        }

        .calculator-card select:focus {
            outline: none;
        }

        /* Estilização do dropdown do select */
        .calculator-card select option {
            font-family: 'Onest', sans-serif;
            font-weight: 600;
            font-size: 14px;
            padding: 14px 18px;
            background-color: #ffffff;
            color: #1E293B;
            border-radius: 8px;
            margin: 4px 8px;
        }

        .calculator-card select option:hover {
            background: linear-gradient(135deg, #f1f5f9 0%, #e8f0ff 100%);
            color: #2055d5;
        }

        .calculator-card select option:checked,
        .calculator-card select option:focus {
            background: linear-gradient(135deg, #2055d5 0%, #1e40af 100%);
            color: #ffffff;
            font-weight: 700;
        }

        /* Estilização da scrollbar do dropdown */
        .calculator-card select::-webkit-scrollbar {
            width: 8px;
        }

        .calculator-card select::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }

        .calculator-card select::-webkit-scrollbar-thumb {
            background: #94a3b8;
            border-radius: 10px;
        }

        .calculator-card select::-webkit-scrollbar-thumb:hover {
            background: #2055d5;
        }

        .buy-proxy-btn {
            font-family: 'Onest', sans-serif;
            letter-spacing: 0.3px;
        }

        @keyframes price-pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }

        #priceDisplay {
            font-family: 'Onest', sans-serif;
        }

        /* Multilogin Logo Marquee */
        @keyframes marquee {
            from {
                transform: translateX(0);
            }

            to {
                transform: translateX(-50%);
            }
        }

        .logo-marquee {
            position: relative;
        }

        .logo-marquee-track {
            display: flex;
            width: max-content;
            gap: 16px;
            animation: marquee 26s linear infinite;
            will-change: transform;
        }

        .logo-marquee:hover .logo-marquee-track {
            animation-play-state: paused;
        }

        .logo-pill {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            padding: 14px 18px;
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.75);
            border: 1px solid rgba(226, 232, 240, 0.85);
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.06);
            color: #0f172a;
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
            white-space: nowrap;
            user-select: none;
        }

        .logo-pill:hover {
            transform: translateY(-2px);
            border-color: rgba(35, 54, 111, 0.25);
            box-shadow: 0 22px 55px rgba(15, 23, 42, 0.10);
        }

        .logo-mark {
            width: 42px;
            height: 42px;
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 900;
            letter-spacing: 0.02em;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.18);
        }

        .logo-fade {
            position: absolute;
            top: 0;
            bottom: 0;
            width: 140px;
            pointer-events: none;
        }

        .logo-fade.left {
            left: 0;
            background: linear-gradient(90deg, rgba(248, 250, 252, 1) 0%, rgba(248, 250, 252, 0) 100%);
        }

        .logo-fade.right {
            right: 0;
            background: linear-gradient(270deg, rgba(248, 250, 252, 1) 0%, rgba(248, 250, 252, 0) 100%);
        }

        /* ===================================
           RESPONSIVIDADE MOBILE & TABLET
        =================================== */

        /* Mobile - Até 640px */
        @media (max-width: 640px) {
            /* Header mobile */
            header .container {
                padding: 1rem;
            }

            header img {
                width: 120px !important;
                height: auto !important;
            }

            /* Hero Section mobile */
            #alfaLogo {
                width: 120px;
                margin-bottom: 10px;
                transform: translate(0, 0) !important;
            }

            #heroTitle {
                font-size: 28px !important;
                max-width: 100% !important;
            }

            #heroSubtitle {
                font-size: 16px !important;
                max-width: 100% !important;
            }

            /* Proxy Calculator mobile */
            .proxy-calculator {
                padding: 1rem !important;
                border-radius: 20px !important;
            }

            .calculator-card {
                min-height: auto !important;
                padding: 0.75rem !important;
            }

            #priceDisplay {
                font-size: 1.5rem !important;
            }

            .buy-proxy-btn {
                font-size: 13px !important;
                padding: 0.75rem 1.25rem !important;
            }

            /* Multilogin section mobile */
            .logo-pill {
                padding: 10px 14px !important;
                gap: 8px !important;
            }

            .logo-mark {
                width: 32px !important;
                height: 32px !important;
            }

            .logo-pill span:last-child {
                font-size: 12px !important;
            }

            /* Benefits cards mobile */
            .group.rounded-\[2rem\] {
                padding: 1.5rem !important;
            }

            /* FAQ mobile */
            .faq-item p {
                font-size: 14px !important;
            }

            .faq-answer p {
                font-size: 13px !important;
            }

            /* Footer mobile */
            footer {
                padding: 2rem 0 !important;
            }

            footer .container {
                padding: 0 1rem !important;
            }
        }

        /* Tablet - 641px até 1024px */
        @media (min-width: 641px) and (max-width: 1024px) {
            /* Header tablet */
            header img {
                width: 180px !important;
            }

            /* Hero Section tablet */
            #alfaLogo {
                width: 150px;
            }

            #heroTitle {
                font-size: 40px !important;
            }

            #heroSubtitle {
                font-size: 18px !important;
            }

            /* Proxy Calculator tablet */
            .calculator-card {
                min-width: 120px !important;
            }

            /* Multilogin section tablet */
            .logo-pill {
                padding: 12px 16px !important;
            }
        }

        /* Landscape mobile - Até 768px altura */
        @media (max-height: 768px) and (orientation: landscape) {
            section[style*="min-height: 85vh"] {
                min-height: auto !important;
                padding-top: 80px !important;
                padding-bottom: 60px !important;
            }

            #alfaLogo {
                display: none;
            }
        }

        /* Ajustes gerais para telas pequenas */
        @media (max-width: 768px) {
            .container {
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }

            h1, h2, h3 {
                text-align: center;
            }

            .text-left {
                text-align: center !important;
            }

            /* Centralizar conteúdo do hero em mobile */
            .grid.grid-cols-1 {
                text-align: center;
            }

            /* Esconder elementos decorativos em mobile */
            .hidden.lg\:flex {
                display: none !important;
            }

            /* Ajustar espaçamentos */
            section {
                padding-top: 3rem !important;
                padding-bottom: 3rem !important;
            }

            /* Cards responsivos */
            .grid.gap-6 {
                gap: 1rem !important;
            }

            /* Ajustar bordas arredondadas em mobile */
            .rounded-\[2rem\],
            .rounded-\[2\.25rem\],
            .rounded-\[1\.75rem\] {
                border-radius: 1.25rem !important;
            }
        }

        /* Extra small devices (phones em portrait) */
        @media (max-width: 375px) {
            #heroTitle {
                font-size: 24px !important;
            }

            #heroSubtitle {
                font-size: 14px !important;
            }

            .buy-proxy-btn {
                font-size: 12px !important;
                padding: 0.65rem 1rem !important;
            }
        }

        /* Touch devices - melhorar áreas de toque */
        @media (hover: none) and (pointer: coarse) {
            button,
            .cursor-pointer,
            a {
                min-height: 44px;
                min-width: 44px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            .faq-item > div {
                padding: 1rem !important;
            }
        }
    </style>
</head>

<body class="bg-gray-50 font-sans">
    <!-- Header Transparente -->
    <header class="absolute top-0 left-0 right-0 z-50 bg-transparent">
        <div class="container mx-auto px-4 py-4 md:py-6 flex justify-between items-center gap-4 md:gap-8">
            <div class="flex items-center">
                <img src="{!! asset('images/logoproxy.webp') !!}" alt="Logo" height="200" width="250" class="h-auto max-w-[120px] sm:max-w-[180px] md:max-w-[250px]">
            </div>
            <!-- Glass Pill Menu -->
            <!-- <nav
                class="hidden md:flex items-center bg-white/15 backdrop-blur-xl border border-white/20 rounded-full px-5 py-3 shadow-lg shadow-black/5">
                <a href="{{ route('inicial') }}"
                    class="glass-pill-link font-onest text-base font-medium px-8 py-2 rounded-full text-white hover:text-[#2055dd] transition-all duration-300 ease-out hover:bg-white/20">
                    Início
                </a>
                <a href="{{ route('inicial') }}"
                    class="glass-pill-link font-onest text-base font-medium px-8 py-2 rounded-full text-white hover:text-[#2055dd] transition-all duration-300 ease-out hover:bg-white/20">
                    Planos
                </a>
                <a href="{{ route('inicial') }}"
                    class="glass-pill-link font-onest text-base font-medium px-8 py-2 rounded-full text-white hover:text-[#2055dd] transition-all duration-300 ease-out hover:bg-white/20">
                    API
                </a>
                <a href="{{ route('duvidas.show') }}"
                    class="glass-pill-link font-onest text-base font-medium px-8 py-2 rounded-full text-white hover:text-[#2055dd] transition-all duration-300 ease-out hover:bg-white/20">
                    Suporte
                </a>
            </nav> -->
            <div class="flex items-center gap-2 md:gap-3">
                @auth
                    <a href="{{ route('dash.show') }}" data-ripple-light="true"
                        class="flex items-center text-sm md:text-base gap-1 md:gap-2 select-none text-center text-white hover:text-white transition-all py-2 px-3 md:px-5 rounded-xl no-underline bg-white/20 backdrop-blur-md hover:bg-white/30 border border-white/30"
                        style="transition: all 0.3s; font-weight: 500;">
                        <i class="fa-solid fa-door-open text-sm md:text-base"></i> <span class="hidden sm:inline">Acessar conta</span><span class="sm:hidden">Entrar</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" data-ripple-light="true"
                        class="flex items-center text-sm md:text-base gap-1 md:gap-2 select-none text-center text-white hover:text-white transition-all py-2 px-3 md:px-5 rounded-xl no-underline bg-white/20 backdrop-blur-md hover:bg-white/30 border border-white/30"
                        style="transition: all 0.3s; font-weight: 500;">
                        <i class="fas fa-user-plus text-sm md:text-base"></i>
                        <span class="hidden sm:inline">Entrar</span><span class="sm:hidden">Login</span>
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="relative flex items-start justify-center overflow-hidden"
        style="background: linear-gradient(to right, #438ccb, #316fab, #306da8, #3066a0, #2a508a, #233a72); min-height: 85vh; padding-top: 100px;">
        <div class="container mx-auto px-4 md:px-6">
            <div class="grid grid-cols-1 lg:grid-cols-[1.2fr_0.8fr] gap-8 md:gap-16 items-start">

                <!-- Texto -->
                <div class="text-center lg:text-left">
                    <!-- SVG Animado -->
                    <svg id="alfaLogo" viewBox="0 0 600 250" class="inline-block transform -translate-x-7 translate-y-5"
                        xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="logoGradient" x1="0" y1="0" x2="1" y2="1">
                                <stop offset="0%" stop-color="#60a5fa" />
                                <stop offset="100%" stop-color="#2563eb" />
                            </linearGradient>
                        </defs>
                        <path id="logoPath" fill="none" stroke="white" stroke-width="3" stroke-linecap="round"
                            d="M 184.503906 128.6875 L 185.003906 128.675781 C 192.949219 128.550781 199.875 130.457031 207.058594 133.875 C 207.886719 134.269531 208.734375 134.621094 209.597656 134.933594 C 210.617188 135.308594 211.566406 135.769531 212.527344 136.277344 L 212.929688 136.488281 C 213.882812 137.003906 213.882812 137.003906 214.265625 137.398438 C 214.777344 137.898438 215.429688 138.152344 216.066406 138.449219 C 216.554688 138.679688 217.027344 138.914062 217.5 139.167969 L 217.707031 139.582031 L 218.097656 139.675781 L 218.542969 139.792969 L 218.824219 140.074219 C 219.21875 140.46875 219.625 140.726562 220.097656 141.019531 L 220.660156 141.371094 L 221.261719 141.746094 C 223.25 142.992188 225.15625 144.3125 227.042969 145.707031 C 227.507812 146.046875 227.976562 146.382812 228.449219 146.71875 C 230.429688 148.125 232.351562 149.605469 234.222656 151.160156 C 234.730469 151.578125 235.242188 151.992188 235.753906 152.410156 C 237.121094 153.527344 238.457031 154.679688 239.792969 155.832031 L 240.257812 156.234375 C 244.140625 159.597656 248.148438 163.105469 251.464844 167.03125 C 251.9375 167.570312 252.425781 168.089844 252.917969 168.613281 C 254.230469 170.003906 255.472656 171.421875 256.667969 172.917969 C 256.875 173.167969 257.082031 173.417969 257.289062 173.667969 C 263.042969 180.621094 268.773438 188.851562 271.042969 197.707031 L 271.160156 198.15625 C 271.40625 199.09375 271.5 199.871094 271.457031 200.832031 L 271.875 200.832031 C 272.042969 209.511719 270.730469 217.253906 266.667969 225 C 264.667969 228.601562 262.074219 231.683594 259.167969 234.582031 L 258.703125 235.066406 C 255.546875 238.339844 251.308594 241.214844 247.082031 242.917969 L 246.570312 243.125 C 244.363281 244.003906 242.15625 244.839844 239.84375 245.390625 L 239.375 245.503906 C 238.824219 245.625 238.265625 245.730469 237.707031 245.832031 C 237.566406 245.859375 237.425781 245.886719 237.28125 245.910156 C 234.714844 246.371094 232.21875 246.550781 229.613281 246.527344 C 229.042969 246.523438 228.472656 246.523438 227.898438 246.523438 C 226.050781 246.523438 224.308594 246.449219 222.5 246.042969 C 222.199219 245.992188 221.898438 245.949219 221.59375 245.910156 C 219.902344 245.664062 218.285156 245.320312 216.652344 244.804688 L 216.171875 244.652344 C 214.644531 244.148438 213.191406 243.488281 211.757812 242.761719 L 211.222656 242.488281 L 210.832031 242.292969 L 210.832031 241.875 L 210.324219 241.746094 C 207.71875 240.738281 205.4375 238.476562 203.320312 236.691406 L 202.988281 236.414062 C 202.304688 235.835938 201.667969 235.222656 201.042969 234.582031 C 200.792969 234.332031 200.542969 234.082031 200.292969 233.835938 L 198.664062 232.207031 L 198.277344 231.816406 C 197.964844 231.507812 197.65625 231.195312 197.347656 230.882812 C 196.9375 230.480469 196.511719 230.09375 196.066406 229.726562 C 195.339844 229.097656 194.726562 228.421875 194.109375 227.6875 C 193.761719 227.304688 193.40625 226.988281 193.007812 226.667969 C 192.339844 226.117188 191.875 225.507812 191.375 224.808594 C 190.898438 224.1875 190.34375 223.660156 189.78125 223.117188 C 189.292969 222.625 188.84375 222.105469 188.402344 221.570312 C 188.128906 221.246094 188.128906 221.246094 187.824219 220.949219 C 187.308594 220.433594 186.851562 219.875 186.378906 219.320312 C 186.0625 218.9375 186.0625 218.9375 185.625 218.75 L 185.363281 218.28125 C 185.015625 217.667969 185.015625 217.667969 184.375 217.1875 C 183.765625 216.679688 183.523438 216.289062 183.125 215.625 L 182.5 215.105469 C 181.875 214.582031 181.875 214.582031 181.625 214.136719 C 181.074219 213.257812 180.355469 212.554688 179.636719 211.808594 C 178.660156 210.792969 177.703125 209.765625 176.78125 208.699219 C 176.253906 208.101562 175.679688 207.5625 175.105469 207.011719 L 174.792969 206.667969 L 174.792969 206.25 L 174.375 206.25 C 174.085938 205.957031 174.085938 205.957031 173.746094 205.542969 C 172.851562 204.496094 171.890625 203.527344 170.917969 202.558594 L 170.347656 201.988281 C 169.953125 201.589844 169.554688 201.195312 169.160156 200.800781 C 168.65625 200.296875 168.152344 199.792969 167.652344 199.289062 C 167.164062 198.804688 166.679688 198.316406 166.191406 197.832031 L 165.652344 197.289062 C 164.636719 196.277344 163.585938 195.308594 162.5 194.375 C 162.265625 194.164062 162.027344 193.953125 161.792969 193.742188 C 157.8125 190.199219 153.078125 186.386719 147.855469 184.929688 C 144.652344 184.355469 141.394531 184.78125 138.332031 185.832031 C 136.265625 186.636719 134.441406 187.785156 132.707031 189.167969 L 132.34375 189.445312 C 130.105469 191.195312 128.371094 193.878906 127.214844 196.445312 C 126.027344 199.402344 125.585938 202.351562 125.613281 205.519531 L 125.613281 206.222656 C 125.617188 206.785156 125.621094 207.351562 125.625 207.917969 L 126.042969 208.125 C 126.109375 208.492188 126.109375 208.492188 126.136719 208.960938 C 126.230469 209.972656 126.46875 210.867188 126.796875 211.824219 L 126.964844 212.316406 C 127.847656 214.816406 129.179688 216.898438 130.832031 218.957031 L 131.171875 219.386719 C 134.113281 222.976562 138.632812 225.46875 142.914062 227.113281 C 146.324219 228.375 149.738281 229.242188 153.332031 229.792969 L 153.851562 229.875 C 161.691406 231.058594 169.921875 230.023438 177.335938 227.3125 C 178.476562 226.863281 179.558594 226.308594 180.636719 225.722656 C 181.25 225.417969 181.25 225.417969 181.875 225.417969 C 181.53125 226.5 180.867188 227.34375 180.207031 228.253906 L 179.53125 229.199219 C 179.429688 229.339844 179.332031 229.476562 179.230469 229.621094 C 178.996094 229.953125 178.765625 230.285156 178.542969 230.625 L 178.125 230.625 L 177.960938 230.992188 C 177.691406 231.488281 177.394531 231.859375 177.019531 232.277344 L 176.65625 232.6875 C 176.1875 233.195312 175.699219 233.679688 175.207031 234.167969 L 174.753906 234.644531 C 173.347656 236.097656 171.824219 237.332031 170.207031 238.542969 L 169.675781 238.945312 C 163.855469 243.1875 156.886719 245.566406 149.792969 246.457031 C 148.695312 246.507812 147.597656 246.507812 146.496094 246.511719 L 146.023438 246.511719 C 139.503906 246.5 132.925781 245.554688 127.082031 242.5 L 127.082031 242.082031 L 126.535156 242.019531 C 125.796875 241.867188 125.242188 241.609375 124.582031 241.25 L 124.582031 240.832031 L 123.957031 240.832031 L 123.957031 240.417969 L 122.917969 240.207031 L 122.917969 239.792969 L 122.566406 239.726562 C 121.984375 239.554688 121.539062 239.304688 121.042969 238.957031 L 121.042969 238.542969 L 120.417969 238.542969 C 120.132812 238.269531 119.855469 237.992188 119.582031 237.707031 C 119.234375 237.453125 118.878906 237.207031 118.515625 236.96875 C 118.109375 236.65625 117.757812 236.308594 117.398438 235.945312 C 117.023438 235.566406 116.640625 235.195312 116.257812 234.828125 C 113.746094 232.390625 111.25 229.78125 109.582031 226.667969 L 109.582031 226.25 L 109.167969 226.25 C 105.144531 218.960938 103.136719 211.519531 103.253906 203.1875 C 103.402344 195.8125 105.589844 188.84375 109.351562 182.503906 C 109.882812 181.644531 110.449219 180.816406 111.042969 180 L 111.3125 179.625 C 115.808594 173.480469 121.761719 168.820312 128.679688 165.683594 C 135.195312 162.839844 142.515625 161.703125 149.582031 162.464844 C 151.5 162.695312 153.394531 163.023438 155.285156 163.425781 L 155.867188 163.546875 C 157 163.800781 158.050781 164.148438 159.121094 164.601562 C 159.558594 164.78125 159.988281 164.921875 160.441406 165.050781 C 165.777344 166.808594 170.539062 170.617188 174.792969 174.167969 L 175.167969 174.480469 C 178.933594 177.621094 182.382812 181.097656 185.839844 184.5625 C 186.457031 185.179688 187.074219 185.796875 187.691406 186.414062 C 188.171875 186.894531 188.652344 187.375 189.132812 187.855469 L 189.816406 188.539062 C 190.917969 189.640625 192.003906 190.746094 193.015625 191.929688 C 193.5 192.484375 194.023438 193 194.550781 193.511719 L 195.222656 194.179688 C 195.566406 194.515625 195.910156 194.855469 196.253906 195.191406 C 197.585938 196.5 198.847656 197.8125 200.007812 199.28125 C 200.441406 199.820312 200.898438 200.335938 201.367188 200.847656 C 202.125 201.679688 202.835938 202.539062 203.535156 203.421875 C 204.136719 204.183594 204.75 204.929688 205.367188 205.675781 C 205.96875 206.414062 206.554688 207.164062 207.140625 207.917969 C 207.503906 208.378906 207.503906 208.378906 208.125 208.75 C 208.335938 209.097656 208.542969 209.445312 208.75 209.792969 C 209.121094 210.25 209.496094 210.703125 209.882812 211.144531 L 210.21875 211.53125 C 211.855469 213.414062 213.554688 215.238281 215.3125 217.003906 L 215.683594 217.378906 C 216.164062 217.855469 216.621094 218.300781 217.167969 218.703125 L 217.5 218.957031 L 217.5 219.375 L 217.859375 219.542969 C 218.355469 219.804688 218.78125 220.101562 219.230469 220.429688 C 222.007812 222.378906 224.476562 223.761719 227.917969 224.167969 C 231.769531 224.402344 235.472656 223.769531 238.957031 222.082031 C 239.53125 221.757812 240.074219 221.410156 240.625 221.042969 L 241.042969 221.042969 L 241.042969 220.625 L 241.667969 220.625 L 242.082031 219.792969 L 242.707031 219.792969 L 242.863281 219.429688 C 243.164062 218.886719 243.515625 218.511719 243.957031 218.074219 L 244.402344 217.628906 L 244.792969 217.292969 L 245.207031 217.292969 L 245.417969 216.457031 L 245.832031 216.457031 L 245.898438 216.109375 C 246.070312 215.527344 246.34375 215.085938 246.667969 214.570312 C 247.019531 214.007812 247.289062 213.546875 247.5 212.917969 L 247.917969 212.917969 C 248.304688 211.707031 248.628906 210.507812 248.847656 209.253906 C 248.957031 208.75 248.957031 208.75 249.167969 208.148438 C 249.742188 206.347656 249.667969 204.371094 249.328125 202.53125 C 248.132812 197.71875 244.691406 193.429688 241.648438 189.664062 C 241.128906 189.015625 240.621094 188.351562 240.113281 187.691406 C 239.816406 187.320312 239.503906 186.996094 239.167969 186.667969 L 238.332031 185.625 L 238.035156 185.324219 C 237.640625 184.929688 237.292969 184.5 236.941406 184.070312 C 236.699219 183.785156 236.4375 183.519531 236.171875 183.253906 C 235.722656 182.808594 235.308594 182.351562 234.894531 181.875 C 234.332031 181.226562 233.742188 180.601562 233.125 180 C 232.445312 179.320312 231.765625 178.640625 231.085938 177.957031 C 230.5625 177.429688 230.039062 176.902344 229.515625 176.378906 L 228.691406 175.550781 C 226.761719 173.597656 226.761719 173.597656 224.726562 171.757812 C 224.117188 171.238281 223.539062 170.679688 222.957031 170.128906 C 221.90625 169.148438 220.824219 168.210938 219.703125 167.3125 C 219.171875 166.878906 218.667969 166.425781 218.164062 165.964844 C 216.886719 164.804688 215.53125 163.757812 214.167969 162.707031 L 213.324219 162.054688 C 210.574219 159.933594 207.796875 157.851562 204.871094 155.976562 L 204.425781 155.6875 C 203.011719 154.800781 201.519531 154.074219 200.023438 153.339844 C 199.597656 153.132812 199.175781 152.921875 198.753906 152.714844 C 191.203125 148.980469 183.539062 146.527344 175.089844 145.808594 L 174.507812 145.753906 C 171.894531 145.523438 169.339844 145.640625 166.730469 145.898438 L 166.285156 145.941406 C 165.300781 146.039062 164.316406 146.144531 163.332031 146.25 L 162.769531 146.3125 C 157.832031 146.878906 152.839844 148.589844 148.332031 150.625 L 147.398438 151.03125 C 145.296875 151.96875 143.359375 153.117188 141.425781 154.351562 C 141.027344 154.589844 140.625 154.796875 140.207031 155 C 140.519531 154.101562 140.992188 153.378906 141.550781 152.617188 L 141.828125 152.230469 C 142.121094 151.832031 142.414062 151.4375 142.707031 151.042969 L 142.980469 150.675781 C 148.761719 142.933594 156.691406 136.894531 165.539062 133.054688 C 165.984375 132.867188 166.429688 132.683594 166.875 132.5 L 167.300781 132.328125 C 169.472656 131.445312 171.691406 130.796875 173.957031 130.207031 L 174.339844 130.105469 C 175.328125 129.875 176.332031 129.722656 177.332031 129.550781 C 177.742188 129.480469 178.152344 129.410156 178.5625 129.335938 L 179.359375 129.199219 L 180.078125 129.074219 C 181.554688 128.847656 183.011719 128.714844 184.503906 128.6875 " />
                    </svg>

                    <h1 id="heroTitle" class="text-white font-bold mb-4 mx-auto lg:mx-0"
                        style="font-family: 'Onest', sans-serif; font-size: 52px; line-height: 1.2; max-width: 650px;">
                        <span class="hero-main">Proxies Premium de</span><br>
                        <span class="hero-highlight"
                            style="background: linear-gradient(90deg, #60a5fa 0%, #e8eef5 50%, #60a5fa 100%); background-size: 200% auto; -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; animation: shimmer 3s linear infinite;">Alta
                            Performance</span>
                    </h1>

                    <p id="heroSubtitle" class="text-white text-xl mb-8 md:mb-10 mx-auto lg:mx-0"
                        style="font-family: 'Onest', sans-serif; font-weight: 400; max-width: 550px; opacity: 0.95;">
                        Velocidade, segurança e preços acessíveis. <br class="hidden sm:block">
                        Conquiste resultados com a <strong style="color: #e8eef5;">AlfaProxy</strong>
                    </p>

                    <!-- Proxy Calculator -->
                    <div id="calculator"
                        class="proxy-calculator bg-white/95 backdrop-blur-md rounded-[28px] shadow-[0_4px_20px_rgba(0,0,0,0.08)] ring-1 ring-white/30 p-5 flex flex-col lg:flex-row items-stretch lg:items-center gap-3 w-full max-w-6xl relative z-10">

                        <!-- Proxy Type -->
                        <div
                            class="calculator-card bg-white/60 backdrop-blur-md rounded-xl px-4 py-3 flex-1 min-w-[140px] relative group cursor-pointer hover:bg-white/80 transition-all duration-300">
                            <label
                                class="block text-[10px] font-semibold text-[#94A3B8] mb-1 uppercase tracking-wider">Tipo</label>
                            <div class="flex items-center justify-between">
                                <span class="text-[#1E293B] font-bold text-sm">SOCKS5</span>
                                <svg class="w-3.5 h-3.5 text-[#94A3B8] transition-transform group-hover:translate-y-0.5"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>

                        <!-- Country -->
                        <div
                            class="calculator-card bg-white/60 backdrop-blur-md rounded-xl px-4 py-3 flex-1 min-w-[160px] relative group cursor-pointer hover:bg-white/80 transition-all duration-300">
                            <label
                                class="block text-[10px] font-semibold text-[#94A3B8] mb-1 uppercase tracking-wider">País</label>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <img src="https://flagcdn.com/w20/br.png" alt="Brasil" class="w-5 h-auto">
                                    <span class="text-[#1E293B] font-bold text-sm">Brasil</span>
                                </div>
                                <svg class="w-3.5 h-3.5 text-[#94A3B8] transition-transform group-hover:translate-y-0.5"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                            <div class="absolute -bottom-0.5 right-3 text-[9px] text-[#94A3B8] font-medium opacity-70">
                                Outros países em breve</div>
                        </div>

                        <!-- Rental Period -->
                        <div
                            class="calculator-card bg-white/60 backdrop-blur-md rounded-xl px-4 py-3 flex-1 min-w-[140px] relative">
                            <label
                                class="block text-[10px] font-semibold text-[#94A3B8] mb-1 uppercase tracking-wider">Período</label>
                            <div class="relative" data-ui-select data-sync-select="#rentalPeriod">
                                <!-- select original escondido (mantém toda a lógica/JS existente) -->
                                <select id="rentalPeriod"
                                    class="appearance-none absolute opacity-0 pointer-events-none w-0 h-0 overflow-hidden">
                                    <option value="30" data-price="20">30 dias</option>
                                    <option value="60" data-price="35">60 dias</option>
                                    <option value="90" data-price="45">90 dias</option>
                                    <option value="180" data-price="80">180 dias</option>
                                    <option value="360" data-price="120">360 dias</option>
                                </select>

                                <!-- trigger visual -->
                                <button type="button"
                                    class="w-full text-[#1E293B] font-bold text-sm bg-transparent border-none outline-none cursor-pointer hover:text-[#2055d5] transition-colors pr-6 text-left"
                                    data-ui-select-trigger>
                                    <span data-ui-select-label>30 dias</span>

                                </button>

                                <!-- dropdown -->
                                <div class="absolute z-[9999] mt-2 w-full rounded-xl border border-slate-200 bg-white shadow-xl hidden overflow-hidden"
                                    data-ui-select-panel>
                                    <button type="button"
                                        class="w-full px-4 py-3 text-left hover:bg-slate-50 font-bold text-sm text-slate-700"
                                        data-ui-select-option data-value="30">30 dias</button>
                                    <button type="button"
                                        class="w-full px-4 py-3 text-left hover:bg-slate-50 font-bold text-sm text-slate-700"
                                        data-ui-select-option data-value="60">60 dias</button>
                                    <button type="button"
                                        class="w-full px-4 py-3 text-left hover:bg-slate-50 font-bold text-sm text-slate-700"
                                        data-ui-select-option data-value="90">90 dias</button>
                                    <button type="button"
                                        class="w-full px-4 py-3 text-left hover:bg-slate-50 font-bold text-sm text-slate-700"
                                        data-ui-select-option data-value="180">180 dias</button>
                                    <button type="button"
                                        class="w-full px-4 py-3 text-left hover:bg-slate-50 font-bold text-sm text-slate-700"
                                        data-ui-select-option data-value="360">360 dias</button>
                                </div>
                            </div>
                            <svg class="w-3.5 h-3.5 text-[#94A3B8] absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>

                        <!-- Price Display & Buy Button -->
                        <div class="flex flex-col lg:flex-row items-center gap-3 lg:gap-4 lg:pl-2 w-full lg:w-auto">
                            <div class="text-center lg:text-right w-full lg:w-auto">
                                <div class="text-[9px] font-bold text-[#94A3B8] uppercase tracking-wider mb-1">Total
                                </div>
                                <div id="priceDisplay"
                                    class="text-2xl font-extrabold text-[#2055d5] leading-none whitespace-nowrap">R$ 20
                                </div>
                            </div>
                            <a href="{{ route('login') }}"
                                class="buy-proxy-btn bg-gradient-to-r from-[#4F8BFF] to-[#3AA0FF] text-white font-bold text-sm px-6 py-3 rounded-3xl shadow-[0_4px_16px_rgba(79,139,255,0.3)] hover:shadow-[0_6px_20px_rgba(79,139,255,0.4)] hover:brightness-105 hover:-translate-y-0.5 active:scale-[0.97] transition-all duration-300 whitespace-nowrap w-full lg:w-auto text-center">
                                Comprar Agora
                            </a>
                        </div>

                    </div>
                </div>

                <!-- Globo 3D Three.js -->
                <div class="hidden lg:flex items-center justify-end pr-6 mb-8">
                    <canvas id="globeCanvas" style="width: 550px; height: 550px;"></canvas>
                </div>
            </div>
        </div>
        <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-[0] transform rotate-180">
            <svg class="relative block w-[calc(100%+1.3px)] h-[80px]" data-name="Layer 1"
                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path
                    d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5,73.84-4.36,147.54,16.88,218.2,35.26,69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113,2.99,1200,52.47V0Z"
                    fill="#FFFFFF" opacity=".25"></path>
                <path
                    d="M0,0V15.81C13,36.92,27.64,56.86,47.69,72.05,99.41,111.27,165,111,224.58,91.58c31.15-10.15,60.09-26.07,89.67-39.8,40.92-19,84.73-46,130.83-49.67,36.26-2.85,70.9,9.42,98.6,31.56,31.77,25.39,62.32,62,103.63,73,40.44,10.79,81.35-6.69,119.13-24.28s75.16-39,116.92-43.05c59.73-5.85,113.28,22.88,168.9,38.84,30.2,8.66,59,6.17,87.09-7.5,22.43-10.89,48-26.93,60.65-49.24V0Z"
                    fill="#FFFFFF" opacity=".5"></path>
                <path
                    d="M0,0V5.63C149.93,59,314.09,71.32,475.83,42.57c43-7.64,84.23-20.12,127.61-26.46,59-8.63,112.48,12.24,165.56,35.41,31.3,13.6,63.24,28.27,97,33.15,64.44,9.33,121.51-12.05,174.69-42.33,54.31-30.9,104.58-40.4,165.31-41.59V0Z"
                    fill="#FFFFFF"></path>
            </svg>
        </div>
    </section>

    <!-- Multilogins (overlap + continuidade visual) -->
    <section class="relative -mt-12 md:-mt-24 pb-12 md:pb-20 overflow-hidden bg-gradient-to-b from-transparent via-gray-50/70 to-gray-50">
        <div class="container mx-auto px-4 md:px-6">
            <div
                class="relative rounded-[1.25rem] md:rounded-[2.25rem] border border-white/30 bg-white/35 backdrop-blur-2xl shadow-[0_28px_70px_rgba(15,23,42,0.18)] overflow-hidden">
                <div class="px-4 sm:px-6 md:px-10 pt-6 md:pt-10 pb-4 md:pb-6">
                    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-6">
                        <div class="max-w-3xl">
                            <p class="text-[10px] font-black uppercase tracking-[0.35em] text-slate-500">Integrações</p>
                            <h2 class="mt-3 text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                                Compatível com <span class="relative inline-block group">
    <span class="relative z-10 font-black text-[#23366f] transition-colors group-hover:text-[#4F8BFF]">todos</span>
    <span class="absolute bottom-0 left-0 w-full h-1.5 bg-gradient-to-r from-[#4F8BFF] to-[#2055dd] rounded-full transform transition-transform group-hover:scale-105"></span>
</span> os multilogins do mercado.
                            </h2>
                            <p class="mt-2 text-slate-500 font-medium">
                                Você compra aqui e usa nos seus perfis imediatamente.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="px-2 sm:px-4 md:px-10 pb-6 md:pb-10">
                    <div
                        class="relative rounded-xl md:rounded-[1.75rem] border border-slate-200 bg-white/75 backdrop-blur-xl overflow-hidden">
                        <div class="logo-marquee py-4 md:py-6">
                            <div class="logo-marquee-track px-4 md:px-6">
                                @php
                                    $multilogins = [
                                        ['name' => 'Lauth App', 'logo' => 'lauth.webp', 'bg' => '#000002'],
                                        ['name' => 'AdsPower', 'logo' => 'adspower.webp', 'bg' => '#1952FF'],
                                        ['name' => 'Dolphin Anty', 'logo' => 'dolphin.webp', 'bg' => 'linear-gradient(180deg, #b645ee 0.6%, #9b26b6 100%)'],
                                        ['name' => 'GoLogin', 'logo' => 'gologin.jpeg', 'bg' => 'white'],
                                        ['name' => 'Incogniton', 'logo' => 'incogniton.png', 'bg' => '#000002'],
                                        ['name' => 'Multilogin', 'logo' => 'multilogin.jpeg', 'bg' => '#fff'],
                                        ['name' => 'Octo Browser', 'logo' => 'octobrowser.png', 'bg' => 'linear-gradient(180deg, #FFFFFF 15%, #A5F0F0 85%)'],
                                        ['name' => 'VMLogin', 'logo' => 'vmlogin.png', 'bg' => ' #fff'],
                                    ];
                                @endphp

                                @foreach(array_merge($multilogins, $multilogins) as $item)
                                    <div
                                        class="logo-pill flex items-center gap-3 bg-white border border-slate-100 px-4 py-2 rounded-2xl shadow-sm">
                                        <span class="logo-mark w-8 h-8 rounded-lg flex items-center justify-center p-1.5"
                                            style="background: {{ $item['bg'] }};">
                                            <img src="{{ asset('images/multilogins/' . $item['logo']) }}"
                                                alt="{{ $item['name'] }} Logo" class="w-full h-full object-contain">
                                            {{-- Removi 'brightness-0 invert' para mostrar as cores originais --}}
                                            {{-- A classe 'brightness-0 invert' deixa o logo branco, combinando com seus
                                            gradientes escuros --}}
                                        </span>
                                        <span class="text-sm font-black tracking-tight text-slate-900">
                                            {{ $item['name'] }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>

                            <div class="logo-fade left"></div>
                            <div class="logo-fade right"></div>
                        </div>
                    </div>
                </div>
            </div>
    </section>

    <!-- Benefícios rápidos -->
    <section class="relative py-12 md:py-16 bg-gradient-to-b from-gray-50 to-slate-100">
        <div class="container mx-auto px-4 md:px-6">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 md:gap-6 mb-8 md:mb-10">
                <div class="max-w-2xl">
                    <p class="text-[10px] font-black uppercase tracking-[0.35em] text-slate-400">Benefícios</p>
                    <h2 class="mt-3 text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                        Tudo o que você precisa para rodar em escala
                    </h2>
                    <p class="mt-2 text-slate-500 font-medium">
                        Setup rápido, estabilidade e suporte para manter sua operação fluindo.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">
                <div
                    class="group rounded-xl md:rounded-[2rem] border border-slate-200 bg-white/70 backdrop-blur-xl p-6 md:p-8 shadow-sm hover:shadow-xl hover:shadow-blue-900/5 transition-all">
                    <div
                        class="w-12 h-12 rounded-2xl bg-[#23366f]/10 text-[#23366f] flex items-center justify-center mb-5">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-black text-slate-900 tracking-tight">Ativação instantânea</h3>
                    <p class="mt-2 text-sm text-slate-500 font-medium">Comprou, conectou e começou a usar — sem espera.
                    </p>
                </div>

                <div
                    class="group rounded-xl md:rounded-[2rem] border border-slate-200 bg-white/70 backdrop-blur-xl p-6 md:p-8 shadow-sm hover:shadow-xl hover:shadow-blue-900/5 transition-all">
                    <div
                        class="w-12 h-12 rounded-2xl bg-[#23366f]/10 text-[#23366f] flex items-center justify-center mb-5">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V6l-8-4-8 4v6c0 6 8 10 8 10z" />
                            <path d="M9 12l2 2 4-4" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-black text-slate-900 tracking-tight">Operação segura</h3>
                    <p class="mt-2 text-sm text-slate-500 font-medium">Infra e controle pensados para estabilidade no
                        dia a dia.</p>
                </div>

                <div
                    class="group rounded-xl md:rounded-[2rem] border border-slate-200 bg-white/70 backdrop-blur-xl p-6 md:p-8 shadow-sm hover:shadow-xl hover:shadow-blue-900/5 transition-all">
                    <div
                        class="w-12 h-12 rounded-2xl bg-[#23366f]/10 text-[#23366f] flex items-center justify-center mb-5">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h16v12H7l-3 3V4z" />
                            <path d="M8 10h8" />
                            <path d="M8 13h5" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-black text-slate-900 tracking-tight">Suporte humano</h3>
                    <p class="mt-2 text-sm text-slate-500 font-medium">Ajuda rápida para resolver o que trava sua
                        entrega.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="relative py-12 md:py-16 bg-slate-100">
        <div class="container mx-auto px-4 md:px-6">
            <div class="max-w-4xl mx-auto">
                <div class="text-center mb-8 md:mb-12">
                    <p class="text-[10px] font-black uppercase tracking-[0.35em] text-slate-400">Suporte</p>
                    <h2 class="mt-3 text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                        Perguntas Frequentes
                    </h2>
                    <p class="mt-2 text-slate-500 font-medium">
                        Respostas rápidas para dúvidas comuns sobre nossos serviços.
                    </p>
                </div>

                <div class="bg-white rounded-xl md:rounded-[2rem] border border-slate-200 p-4 md:p-8 shadow-sm">
                    <div class="space-y-2 md:space-y-3">
                        <div class="faq-item">
                            <div class="flex items-center justify-between cursor-pointer py-3 md:py-4 px-2 hover:bg-slate-50 rounded-lg transition-all" onclick="toggleFaq(this)">
                                <p class="font-semibold text-sm md:text-base text-slate-900">Como funciona o proxy SOCKS5?</p>
                                <i class="fas fa-chevron-down faq-icon text-slate-400 transition-transform"></i>
                            </div>
                            <div class="faq-answer hidden px-2 pb-3 md:pb-4">
                                <p class="text-sm md:text-base text-slate-600">O SOCKS5 é um protocolo de proxy que permite rotear qualquer tipo de tráfego através de um servidor intermediário, oferecendo maior privacidade e segurança. Ele suporta autenticação e pode lidar com diversos protocolos, incluindo HTTP, HTTPS e FTP.</p>
                            </div>
                        </div>

                        <div class="border-t border-slate-100"></div>

                        <div class="faq-item">
                            <div class="flex items-center justify-between cursor-pointer py-3 md:py-4 px-2 hover:bg-slate-50 rounded-lg transition-all" onclick="toggleFaq(this)">
                                <p class="font-semibold text-sm md:text-base text-slate-900">Quanto tempo leva para ativar meu proxy?</p>
                                <i class="fas fa-chevron-down faq-icon text-slate-400 transition-transform"></i>
                            </div>
                            <div class="faq-answer hidden px-2 pb-3 md:pb-4">
                                <p class="text-sm md:text-base text-slate-600">Após a confirmação do pagamento, seus proxies são ativados automaticamente em até 5 minutos. Você receberá um e-mail com as credenciais de acesso.</p>
                            </div>
                        </div>

                        <div class="border-t border-slate-100"></div>

                        <div class="faq-item">
                            <div class="flex items-center justify-between cursor-pointer py-3 md:py-4 px-2 hover:bg-slate-50 rounded-lg transition-all" onclick="toggleFaq(this)">
                                <p class="font-semibold text-sm md:text-base text-slate-900">Posso trocar o país do meu proxy?</p>
                                <i class="fas fa-chevron-down faq-icon text-slate-400 transition-transform"></i>
                            </div>
                            <div class="faq-answer hidden px-2 pb-3 md:pb-4">
                                <p class="text-sm md:text-base text-slate-600">Sim! Entre em contato com nosso suporte e faremos a troca sem custo adicional, sujeito à disponibilidade de IPs no país desejado.</p>
                            </div>
                        </div>

                        <div class="border-t border-slate-100"></div>

                        <div class="faq-item">
                            <div class="flex items-center justify-between cursor-pointer py-3 md:py-4 px-2 hover:bg-slate-50 rounded-lg transition-all" onclick="toggleFaq(this)">
                                <p class="font-semibold text-sm md:text-base text-slate-900">O que é renovação automática?</p>
                                <i class="fas fa-chevron-down faq-icon text-slate-400 transition-transform"></i>
                            </div>
                            <div class="faq-answer hidden px-2 pb-3 md:pb-4">
                                <p class="text-sm md:text-base text-slate-600">A renovação automática garante que seu proxy seja renovado automaticamente antes do vencimento, evitando interrupções no serviço. Você pode ativar/desativar essa opção a qualquer momento.</p>
                            </div>
                        </div>

                        <div class="border-t border-slate-100"></div>

                        <div class="faq-item">
                            <div class="flex items-center justify-between cursor-pointer py-3 md:py-4 px-2 hover:bg-slate-50 rounded-lg transition-all" onclick="toggleFaq(this)">
                                <p class="font-semibold text-sm md:text-base text-slate-900">Quais formas de pagamento são aceitas?</p>
                                <i class="fas fa-chevron-down faq-icon text-slate-400 transition-transform"></i>
                            </div>
                            <div class="faq-answer hidden px-2 pb-3 md:pb-4">
                                <p class="text-sm md:text-base text-slate-600">Aceitamos PIX, cartão de crédito/débito e criptomoedas (Bitcoin, USDT, Litecoin e BNB). Todas as transações são processadas de forma segura.</p>
                            </div>
                        </div>

                        <div class="border-t border-slate-100"></div>

                        <div class="faq-item">
                            <div class="flex items-center justify-between cursor-pointer py-3 md:py-4 px-2 hover:bg-slate-50 rounded-lg transition-all" onclick="toggleFaq(this)">
                                <p class="font-semibold text-sm md:text-base text-slate-900">Posso solicitar reembolso?</p>
                                <i class="fas fa-chevron-down faq-icon text-slate-400 transition-transform"></i>
                            </div>
                            <div class="faq-answer hidden px-2 pb-3 md:pb-4">
                                <p class="text-sm md:text-base text-slate-600">Sim, oferecemos garantia de reembolso de 7 dias para novos clientes. Se não estiver satisfeito com nosso serviço, entre em contato e processaremos o reembolso.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

        <!-- Footer -->
        <footer class="text-white py-8 md:py-12" style="background: linear-gradient(to right, #438ccb, #316fab, #306da8, #3066a0, #2a508a, #233a72);">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 md:gap-8">
                <div>
                    <img src="{{ asset('images/logoproxy.webp') }}" alt="AlfaProxy" class="h-8 w-auto mb-4">
                    <p class="text-white">A solução mais confiável para proxies SOCKS5 premium com suporte técnico
                        especializado.</p>
                </div>



                <div>
                    <h4 class="text-lg font-bold mb-4 text-white">Contato</h4>
                    <ul class="space-y-2">
                        <li class="flex items-center text-white"><i class="fas fa-envelope mr-2"></i>
                            contato@alfaproxy.io</li>
                        <li class="flex items-center text-white"><i class="fas fa-phone mr-2"></i> +55 73 9835-6817
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-white/30 mt-8 md:mt-12 pt-6 md:pt-8 text-center text-white">
                <p class="text-sm md:text-base">&copy; {{ date('Y') }} AlfaProxy. Todos os direitos reservados.</p>
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
        submitBtn.addEventListener('click', function () {
            alert('Pedido enviado com sucesso! Redirecionando para o pagamento...');
        });

        // Initialize price
        calculatePrice();
    </script>

    <!-- GSAP Animation for Logo -->
    <script>
        const logoPath = document.querySelector("#alfaLogo path");
        const logoSvg = document.querySelector("#alfaLogo");

        if (logoPath) {
            const length = logoPath.getTotalLength();

            // Define o comprimento total como "tamanho do traço"
            logoPath.style.strokeDasharray = length;
            logoPath.style.strokeDashoffset = length;

            // Timeline
            const tl = gsap.timeline({ repeat: -1, repeatDelay: 3 });

            // "Desenhar" a linha
            tl.to(logoPath, {
                strokeDashoffset: 0,
                duration: 3,
                ease: "power2.out"
            });

            // Brilho leve após desenhar
            tl.to(logoPath, {
                fill: "#e8eef5",
                duration: 1.5,
                repeat: 1,
                repeatDelay: 2.5,
                yoyo: true,
                ease: "sine.inOut"
            });

            // "Apagar" a linha antes de reiniciar
            tl.to(logoPath, {
                strokeDashoffset: length,
                duration: 3,
                ease: "power2.in"
            });

            // Flutuação do logo
            gsap.to(logoSvg, {
                y: -10,
                duration: 3,
                repeat: -1,
                yoyo: true,
                ease: "sine.inOut"
            });
        }


    </script>

    <script>
        // Função de scramble custom sem plugin
        function scrambleText(element, finalText, duration = 2, chars = "01!<>$#%?&") {
            const original = finalText;
            const state = { progress: 0 };

            gsap.to(state, {
                progress: 1,
                duration,
                ease: "power2.out",
                onUpdate() {
                    const p = state.progress;
                    const revealCount = Math.floor(p * original.length);
                    let result = "";

                    for (let i = 0; i < original.length; i++) {
                        const currentChar = original[i];

                        if (currentChar === " " || currentChar === "\n") {
                            result += currentChar; // preserva espaços/quebras
                        } else if (i < revealCount) {
                            result += original[i]; // já "revelou"
                        } else {
                            // caractere randômico
                            result += chars[Math.floor(Math.random() * chars.length)];
                        }
                    }

                    element.textContent = result;
                },
                onComplete() {
                    // garante texto final certinho
                    element.textContent = original;
                }
            });
        }

        document.addEventListener("DOMContentLoaded", () => {
            const mainTitle = document.querySelector(".hero-main");
            const highlight = document.querySelector(".hero-highlight");
            const subtitle = document.querySelector("#heroSubtitle");

            if (!mainTitle || !highlight) return;

            const mainText = mainTitle.textContent;
            const highlightText = highlight.textContent;
            const subtitleText = subtitle.textContent;

            // começa com tudo invisível
            mainTitle.textContent = "";
            highlight.textContent = "";
            subtitle.textContent = "";
            subtitle.style.opacity = 0;

            // timeline para orquestrar tudo
            const tl = gsap.timeline({ delay: 1.5 }); // ajusta pra sincronizar com o logo

            // Animação do título principal com efeito de entrada
            tl.add(() => {
                gsap.from(mainTitle, {
                    x: -50,
                    opacity: 0,
                    duration: 0.6,
                    ease: "power3.out"
                });
                scrambleText(mainTitle, mainText, 2.5);
            })
                .add(() => {
                    gsap.from(highlight, {
                        scale: 0.9,
                        opacity: 0,
                        duration: 0.8,
                        ease: "back.out(1.4)"
                    });
                    scrambleText(highlight, highlightText, 2.2);
                }, "+=0.3");

            // Subtítulo com scramble e entrada lateral
            tl.to(subtitle, {
                opacity: 1,
                duration: 0.3,
                ease: "power1.out"
            }, "+=0.4")
                .add(() => {
                    gsap.from(subtitle, {
                        x: -30,
                        duration: 0.5,
                        ease: "power2.out"
                    });
                    scrambleText(subtitle, subtitleText, 3);
                }, "-=0.2");

            // ===========================
            // Proxy Calculator Logic
            // ===========================
            const rentalPeriodSelect = document.getElementById('rentalPeriod');
            const priceDisplay = document.getElementById('priceDisplay');
            const calculator = document.querySelector('.proxy-calculator');

            if (rentalPeriodSelect && priceDisplay) {
                // Atualizar preço quando mudar período
                rentalPeriodSelect.addEventListener('change', function () {
                    const selectedOption = this.options[this.selectedIndex];
                    const price = selectedOption.getAttribute('data-price');

                    // Animação de troca de preço
                    gsap.to(priceDisplay, {
                        scale: 1.1,
                        duration: 0.2,
                        ease: "power2.out",
                        onComplete: () => {
                            priceDisplay.textContent = `R$ ${price}`;
                            gsap.to(priceDisplay, {
                                scale: 1,
                                duration: 0.2,
                                ease: "power2.out"
                            });
                        }
                    });
                });

                // Animação de entrada da calculadora
                gsap.from(calculator, {
                    opacity: 0,
                    y: 30,
                    duration: 1,
                    ease: "power3.out",
                    delay: 2.5
                });
            }
        });
    </script>

    <script>
        document.addEventListener('click', (e) => {
            const trigger = e.target.closest('[data-ui-select-trigger]');
            const wrap = trigger?.closest('[data-ui-select]');

            // fecha outros
            document.querySelectorAll('[data-ui-select-panel]').forEach(panel => {
                const owner = panel.closest('[data-ui-select]');
                if (!wrap || owner !== wrap) panel.classList.add('hidden');
            });

            // abre/fecha o atual
            if (trigger && wrap) {
                wrap.querySelector('[data-ui-select-panel]')?.classList.toggle('hidden');
                return;
            }

            // seleciona
            const opt = e.target.closest('[data-ui-select-option]');
            if (!opt) return;

            const wrapper = opt.closest('[data-ui-select]');
            const label = wrapper.querySelector('[data-ui-select-label]');
            const panel = wrapper.querySelector('[data-ui-select-panel]');
            const sync = wrapper.getAttribute('data-sync-select');
            const realSelect = sync ? document.querySelector(sync) : null;

            label.textContent = opt.textContent.trim();
            panel.classList.add('hidden');

            if (realSelect) {
                realSelect.value = opt.dataset.value;
                realSelect.dispatchEvent(new Event('change', { bubbles: true }));
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key !== 'Escape') return;
            document.querySelectorAll('[data-ui-select-panel]').forEach(p => p.classList.add('hidden'));
        });
    </script>

    <!-- FAQ Toggle Script -->
    <script>
        function toggleFaq(element) {
            const faqItem = element.parentElement;
            const answer = faqItem.querySelector('.faq-answer');
            const icon = element.querySelector('.faq-icon');

            // Toggle the answer visibility
            answer.classList.toggle('hidden');

            // Rotate the icon
            icon.classList.toggle('rotate-180');
        }
    </script>

    <!-- Material Tailwind Ripple Effect -->
    <script async src="https://unpkg.com/@material-tailwind/html@latest/scripts/ripple.js"></script>

</body>

</html>