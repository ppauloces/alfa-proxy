<style>
    /* Badge de desconto - tamanho ajustado */
    .price-card > span.absolute {
        font-size: 9px !important;
        padding: 0.4rem 0.75rem !important;
    }

    /* Toast de validação */
    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(100%);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes slideOutRight {
        from {
            opacity: 1;
            transform: translateX(0);
        }
        to {
            opacity: 0;
            transform: translateX(100%);
        }
    }

    .validation-toast {
        position: fixed;
        top: 24px;
        right: 24px;
        z-index: 10000;
        background: white;
        border-radius: 20px;
        padding: 1.25rem 1.5rem;
        box-shadow: 0 20px 60px rgba(15, 23, 42, 0.3);
        border-left: 4px solid #ef4444;
        max-width: 420px;
        animation: slideInRight 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .validation-toast.closing {
        animation: slideOutRight 0.3s ease forwards;
    }

    /* Responsividade para nova compra */
    @media (max-width: 640px) {
        .grid.lg\:grid-cols-3 {
            grid-template-columns: 1fr !important;
        }

        .lg\:col-span-2 {
            grid-column: span 1 !important;
        }

        .bg-white.p-8 {
            padding: 1.25rem !important;
        }

        .grid.md\:grid-cols-2,
        .grid.md\:grid-cols-3,
        .grid.grid-cols-1.md\:grid-cols-3.lg\:grid-cols-5 {
            grid-template-columns: 1fr !important;
        }

        .grid.grid-cols-2.sm\:grid-cols-4 {
            grid-template-columns: repeat(2, 1fr) !important;
        }

        .price-card {
            padding: 1rem !important;
        }

        /* Badge de desconto menor em mobile */
        .price-card > span.absolute {
            font-size: 8px !important;
            padding: 0.35rem 0.6rem !important;
        }

        h1 {
            font-size: 2rem !important;
        }

        h2 {
            font-size: 1.125rem !important;
        }

        .payment-method {
            padding: 0.75rem !important;
        }

        .sticky.top-28 {
            position: relative !important;
            top: 0 !important;
        }

        /* Toast responsivo */
        .validation-toast {
            top: 16px;
            right: 16px;
            left: 16px;
            max-width: none;
            padding: 1rem;
        }
    }

    @media (min-width: 641px) and (max-width: 1024px) {
        .grid.lg\:grid-cols-3 {
            grid-template-columns: 1fr !important;
        }

        .grid.grid-cols-1.md\:grid-cols-3.lg\:grid-cols-5 {
            grid-template-columns: repeat(3, 1fr) !important;
        }
    }
</style>

<div class="flex flex-col gap-6">
    {{-- Header da Seção --}}
    <div class="space-y-1">
        <div class="flex items-center gap-3">
            <p class="text-[10px] font-bold text-[#448ccb] uppercase tracking-[0.3em]">Adquirir proxies</p>
            @if(Auth::user()->isRevendedor())
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-amber-400 to-orange-500 rounded-full shadow-md shadow-amber-500/20">
                    <i class="fas fa-crown text-xs text-white"></i>
                    <span class="text-xs font-black text-white uppercase tracking-wider">Revendedor - Preços Especiais</span>
                </span>
            @endif
        </div>
        <h1 class="text-4xl font-black text-slate-900 tracking-tight">Nova <span class="text-[#23366f]">Compra</span>
        </h1>
        <p class="text-slate-500 font-medium max-w-xl">
            Configure e adquira novos proxies para suas necessidades.
            @if(Auth::user()->isRevendedor())
                <span class="text-amber-600 font-bold">Você tem acesso a preços exclusivos de revendedor!</span>
            @endif
        </p>
    </div>

    @if($errors->novaCompra->any())
        <div
            class="alert alert-error bg-red-50 text-red-700 border-red-100 rounded-2xl p-4 font-semibold flex items-center gap-3">
            <i class="fas fa-exclamation-circle"></i>
            <div>
                @foreach($errors->novaCompra->all() as $error)
                    <p class="text-sm">{{ $error }}</p>
                @endforeach
            </div>
        </div>
    @endif

    <form action="{{ route('compra.processar') }}" method="POST" id="orderForm">
        @csrf
        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Formulário Principal -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Configuração do Proxy -->
                <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm">
                    <h2 class="text-xl font-bold text-slate-900 mb-6 flex items-center gap-3">
                        <span
                            class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 text-sm">01</span>
                        Configuração do Proxy
                    </h2>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="form-group">
                            <label class="form-label block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2 leading-none whitespace-nowrap">País</label>
                            <x-ui.select name="pais" :value="old('pais', 'Brasil')" placeholder="Selecione" :options="[
                                'Brasil' => 'Brasil',
                                'Estados Unidos' => 'Estados Unidos',
                                'Reino Unido' => 'Reino Unido',
                                'Alemanha' => 'Alemanha',
                                'França' => 'França',
                                'Itália' => 'Itália',
                                'Espanha' => 'Espanha',
                                'Portugal' => 'Portugal',
                                'Canadá' => 'Canadá',
                                'Austrália' => 'Austrália',
                            ]" />
                        </div>

                        <div class="form-group">
                            <label class="form-label block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2 leading-none whitespace-nowrap">Motivo do Uso</label>

                            @php
                                $motivos = [
                                    [
                                        'value' => 'Facebook',
                                        'label' => 'Facebook',
                                    ],
                                    [
                                        'value' => 'Google',
                                        'label' => 'Google',
                                    ],
                                    [
                                        'value' => 'TikTok',
                                        'label' => 'TikTok',
                                    ],
                                    [
                                        'value' => 'Bet',
                                        'label' => 'Cassino',
                                    ],
                                    [
                                        'value' => 'Kwai',
                                        'label' => 'Kwai',
                                    ],
                                    [
                                        'value' => 'Instagram',
                                        'label' => 'Instagram',
                                    ],
                                    [
                                        'value' => 'Dark',
                                        'label' => 'Dark',
                                    ],
                                    [
                                        'value' => 'Outros',
                                        'label' => 'Outros',
                                    ],
                                ];
                            @endphp

                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                @foreach($motivos as $motivo)
                                    <label class="cursor-pointer select-none">
                                        <input
                                            type="radio"
                                            name="motivo"
                                            value="{{ $motivo['value'] }}"
                                            class="peer hidden"
                                            @checked(old('motivo') === $motivo['value'])
                                        >
                                        <div
                                            class="flex flex-col items-center justify-center gap-2 p-4 rounded-[1.25rem] border-2 border-slate-50 bg-white hover:border-slate-200 transition-all duration-300 peer-checked:border-blue-500 peer-checked:ring-2 peer-checked:ring-blue-500/40 [&_svg]:h-10 [&_svg]:w-10 [&_svg]:grayscale [&_svg]:opacity-50 [&_svg]:transition-all [&_svg]:duration-300 peer-checked:[&_svg]:grayscale-0 peer-checked:[&_svg]:opacity-100 [&_span]:text-xs [&_span]:font-bold [&_span]:text-slate-500 peer-checked:[&_span]:text-slate-900"
                                        >
                                            @switch($motivo['value'])
                                                @case('Facebook')
                                                    <svg viewBox="0 0 24 24" class="fill-[#1877F2]" aria-hidden="true">
                                                        <path d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047V9.414c0-3.03 1.793-4.705 4.533-4.705 1.312 0 2.686.236 2.686.236v2.975h-1.513c-1.49 0-1.953.93-1.953 1.887v2.266h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073z"/>
                                                    </svg>
                                                    @break

                                                @case('Google')
                                                    <x-svg.google />
                                                    @break

                                                @case('TikTok')
                                                    <x-svg.tiktok />
                                                    @break

                                                @case('Bet')
                                                    <x-svg.bet />
                                                    @break

                                                @case('Kwai')
                                                    <x-svg.kwai />
                                                    @break

                                                @case('Instagram')
                                                    <x-svg.instagram />
                                                    @break

                                                @case('Dark')
                                                    <x-svg.anonymous />
                                                    @break

                                                @default
                                                    <x-svg.outros />
                                            @endswitch

                                            <span>{{ $motivo['label'] }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-6">
                        <label
                            class="form-label text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Quantidade
                            de Proxies</label>
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:gap-4">
                            <div class="flex items-center gap-3">
                                <button
                                    type="button"
                                    data-qty-minus
                                    class="w-10 h-10 rounded-xl bg-slate-50 hover:bg-white border border-transparent hover:border-slate-200 text-slate-500 hover:text-slate-900 font-black transition-all"
                                    aria-label="Diminuir quantidade"
                                >
                                    −
                                </button>

                                <input type="number" name="quantidade" value="{{ old('quantidade', 1) }}" id="quantidade"
                                    min="1" max="100"
                                    class="form-input bg-slate-50 border-transparent focus:bg-white focus:border-[#448ccb] transition-all max-w-[120px] text-center font-bold">

                                <button
                                    type="button"
                                    data-qty-plus
                                    class="w-10 h-10 rounded-xl bg-slate-50 hover:bg-white border border-transparent hover:border-slate-200 text-slate-500 hover:text-slate-900 font-black transition-all"
                                    aria-label="Aumentar quantidade"
                                >
                                    +
                                </button>

                                <span id="quantidadeDescontoBadge" class="hidden whitespace-nowrap px-3 py-1 rounded-xl bg-green-50 text-green-700 text-[10px] font-black uppercase tracking-wide border border-green-100">
                                    Economize 10%
                                </span>
                            </div>
                            <p class="text-xs text-slate-400 font-medium">Você pode contratar até 100 proxies por vez.
                            </p>
                        </div>
                    </div>
                </div>

                <script>
                    (() => {
                        const form = document.getElementById('orderForm');
                        if (!form) return;

                        const qtyInput = form.querySelector('#quantidade');
                        const badge = form.querySelector('#quantidadeDescontoBadge');
                        const minus = form.querySelector('[data-qty-minus]');
                        const plus = form.querySelector('[data-qty-plus]');

                        if (!qtyInput) return;

                        const min = Number.parseInt(qtyInput.min || '1', 10);
                        const max = Number.parseInt(qtyInput.max || '100', 10);

                        const toInt = (value, fallback) => {
                            const parsed = Number.parseInt(String(value ?? ''), 10);
                            return Number.isFinite(parsed) ? parsed : fallback;
                        };

                        const clamp = (value) => Math.min(max, Math.max(min, value));

                        const updateBadge = () => {
                            if (!badge) return;
                            const value = toInt(qtyInput.value, min);
                            badge.classList.toggle('hidden', !(value > 10));
                        };

                        const setValue = (nextValue) => {
                            qtyInput.value = clamp(nextValue);
                            qtyInput.dispatchEvent(new Event('input', { bubbles: true }));
                            qtyInput.dispatchEvent(new Event('change', { bubbles: true }));
                        };

                        if (minus) {
                            minus.addEventListener('click', () => {
                                const current = toInt(qtyInput.value, min);
                                setValue(current - 1);
                            });
                        }

                        if (plus) {
                            plus.addEventListener('click', () => {
                                const current = toInt(qtyInput.value, min);
                                setValue(current + 1);
                            });
                        }

                        qtyInput.addEventListener('input', updateBadge);
                        qtyInput.addEventListener('change', updateBadge);
                        updateBadge();

                        // Validação do formulário antes de submeter
                        form.addEventListener('submit', function(e) {
                            let hasError = false;
                            let errorMessages = [];

                            // Verificar se selecionou motivo de uso
                            const motivoSelecionado = form.querySelector('input[name="motivo"]:checked');
                            if (!motivoSelecionado) {
                                hasError = true;
                                errorMessages.push('Selecione o motivo de uso');
                            }

                            // Verificar se selecionou período
                            const periodoSelecionado = form.querySelector('input[name="periodo"]:checked');
                            if (!periodoSelecionado) {
                                hasError = true;
                                errorMessages.push('Selecione o período de contratação');
                            }

                            // Verificar se selecionou método de pagamento
                            const metodoPagamento = document.getElementById('orderPaymentMethod').value;
                            if (!metodoPagamento) {
                                hasError = true;
                                errorMessages.push('Selecione a forma de pagamento');
                            }

                            if (hasError) {
                                e.preventDefault();

                                // Remover toast anterior se existir
                                const oldToast = document.querySelector('.validation-toast');
                                if (oldToast) oldToast.remove();

                                // Criar toast
                                const toast = document.createElement('div');
                                toast.className = 'validation-toast';
                                toast.innerHTML = `
                                    <div style="display: flex; gap: 1rem; align-items: flex-start;">
                                        <div style="
                                            width: 40px;
                                            height: 40px;
                                            border-radius: 50%;
                                            background: linear-gradient(135deg, #ef4444, #dc2626);
                                            display: flex;
                                            align-items: center;
                                            justify-content: center;
                                            color: white;
                                            flex-shrink: 0;
                                        ">
                                            <i class="fas fa-exclamation-circle"></i>
                                        </div>
                                        <div style="flex: 1;">
                                            <div style="font-weight: 700; color: #0f172a; margin-bottom: 0.5rem; font-size: 0.9375rem;">
                                                Campos obrigatórios
                                            </div>
                                            <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 0.375rem;">
                                                ${errorMessages.map(msg => `
                                                    <li style="
                                                        display: flex;
                                                        align-items: center;
                                                        gap: 0.5rem;
                                                        color: #64748b;
                                                        font-size: 0.8125rem;
                                                        font-weight: 500;
                                                    ">
                                                        <i class="fas fa-circle" style="font-size: 4px; color: #ef4444;"></i>
                                                        ${msg}
                                                    </li>
                                                `).join('')}
                                            </ul>
                                        </div>
                                        <button onclick="this.closest('.validation-toast').classList.add('closing'); setTimeout(() => this.closest('.validation-toast').remove(), 300);" style="
                                            background: none;
                                            border: none;
                                            color: #94a3b8;
                                            cursor: pointer;
                                            padding: 0.25rem;
                                            font-size: 1rem;
                                            line-height: 1;
                                            transition: color 0.2s;
                                        " onmouseover="this.style.color='#475569'" onmouseout="this.style.color='#94a3b8'">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                `;

                                document.body.appendChild(toast);

                                // Auto-fechar após 5 segundos
                                setTimeout(() => {
                                    if (toast.parentNode) {
                                        toast.classList.add('closing');
                                        setTimeout(() => toast.remove(), 300);
                                    }
                                }, 5000);

                                // Scroll para o primeiro campo com erro
                                setTimeout(() => {
                                    if (!motivoSelecionado) {
                                        const motivoSection = form.querySelector('input[name="motivo"]').closest('.form-group');
                                        motivoSection?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                    } else if (!periodoSelecionado) {
                                        const periodoSection = form.querySelector('input[name="periodo"]').closest('.bg-white');
                                        periodoSection?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                    } else if (!metodoPagamento) {
                                        const pagamentoSection = document.getElementById('orderPaymentMethod').closest('.bg-white');
                                        pagamentoSection?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                    }
                                }, 100);
                            }
                        });
                    })();
                </script>

                <!-- Período de Contratação -->
                <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm">
                    <h2 class="text-xl font-bold text-slate-900 mb-2 flex items-center gap-3">
                        <span
                            class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600 text-sm font-bold">02</span>
                        Período de Contratação
                    </h2>
                    <p class="text-sm text-slate-500 mb-8 ml-11">Quanto maior o período, melhor o preço por proxy!
                        @if(Auth::user()->isRevendedor())
                            <span class="inline-flex items-center gap-1 ml-2 px-2 py-1 bg-gradient-to-r from-amber-100 to-orange-100 border border-amber-200 rounded-lg">
                                <i class="fas fa-crown text-xs text-amber-600"></i>
                                <span class="text-xs font-black text-amber-700">Preços Especiais de Revendedor</span>
                            </span>
                        @endif
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
                        @php
                            $periods = [
                                ['days' => 30, 'price' => Auth::user()->getPrecoBase(30), 'badge' => null],
                                ['days' => 60, 'price' => Auth::user()->getPrecoBase(60), 'badge' => '-12%'],
                                ['days' => 90, 'price' => Auth::user()->getPrecoBase(90), 'badge' => '-25%'],
                                ['days' => 180, 'price' => Auth::user()->getPrecoBase(180), 'badge' => '-33%'],
                                ['days' => 360, 'price' => Auth::user()->getPrecoBase(360), 'badge' => 'Melhor Preço'],
                            ];
                        @endphp

                        @foreach($periods as $period)
                            <label class="relative cursor-pointer group">
                                <input
                                    type="radio"
                                    name="periodo"
                                    value="{{ $period['days'] }}"
                                    data-price="{{ $period['price'] }}"
                                    class="peer hidden"
                                    @checked(old('periodo') == $period['days'])
                                >

                                <div class="price-card flex flex-col items-center text-center p-6 rounded-[1.8rem] border-2 border-slate-50 bg-white transition-all duration-300 peer-checked:border-blue-600 peer-checked:bg-blue-50/20 peer-checked:ring-4 peer-checked:ring-blue-600/5 group-hover:border-slate-200">
                                    @if($period['badge'])
                                        <span class="absolute -top-3 left-1/2 -translate-x-1/2 px-3 py-1 bg-blue-600 text-white text-[10px] font-black rounded-full shadow-md z-10 whitespace-nowrap">
                                            {{ $period['badge'] }}
                                        </span>
                                    @endif

                                    <span class="text-4xl font-black text-slate-900 leading-none mb-1 group-hover:scale-110 transition-transform duration-300">
                                        {{ $period['days'] }}
                                    </span>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4">Dias</span>

                                    <div class="flex items-center justify-center gap-1 text-blue-600">
                                        <span class="text-xs font-bold">R$</span>
                                        <span class="text-xl font-black">{{ number_format($period['price'], 0, ',', '.') }}</span>
                                    </div>

                                    <div class="mt-4 pt-4 border-t border-slate-100 w-full flex items-center justify-center gap-2 text-[9px] font-bold text-slate-400 group-hover:text-green-600 transition-colors">
                                        <i class="fas fa-bolt"></i> Ativação imediata
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Forma de Pagamento -->
                <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm">
                    <h2 class="text-xl font-bold text-slate-900 mb-6 flex items-center gap-3">
                        <span
                            class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 text-sm">03</span>
                        Forma de Pagamento
                    </h2>

                    <div class="grid md:grid-cols-3 gap-4">
                        @php
                            $methods = [
                                ['id' => 'pix', 'name' => 'PIX', 'icon' => 'fas fa-qrcode', 'desc' => 'Instantâneo', 'enabled' => true],
                                ['id' => 'credit_card', 'name' => 'Cartão', 'icon' => 'fas fa-credit-card', 'desc' => 'Em breve', 'enabled' => false],
                                ['id' => 'usdt', 'name' => 'USDT', 'icon' => 'fab fa-bitcoin', 'desc' => 'Em breve', 'enabled' => false],
                                ['id' => 'btc', 'name' => 'Bitcoin', 'icon' => 'fab fa-btc', 'desc' => 'Em breve', 'enabled' => false],
                                ['id' => 'ltc', 'name' => 'Litecoin', 'icon' => 'fas fa-coins', 'desc' => 'Em breve', 'enabled' => false],
                                ['id' => 'bnb', 'name' => 'Binance', 'icon' => 'fas fa-coins', 'desc' => 'Em breve', 'enabled' => false],
                            ];
                        @endphp

                        @foreach($methods as $method)
                            <div class="payment-method p-5 rounded-2xl border-2 {{ $method['enabled'] ? (old('metodo_pagamento') === $method['id'] ? 'border-[#23366f] bg-blue-50/30' : 'border-slate-50 hover:border-slate-200') : 'border-slate-100 bg-slate-50' }} transition-all {{ $method['enabled'] ? 'cursor-pointer' : 'cursor-not-allowed opacity-60' }}"
                                {{ $method['enabled'] ? 'data-method=' . $method['id'] : '' }}>
                                <div class="flex flex-col gap-3">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center {{ $method['enabled'] ? 'text-slate-400 group-hover:text-[#23366f]' : 'text-slate-300' }}">
                                        <i class="{{ $method['icon'] }} text-xl"></i>
                                    </div>
                                    <div>
                                        <p
                                            class="font-bold {{ $method['enabled'] ? 'text-slate-900' : 'text-slate-400' }} text-sm">
                                            {{ $method['name'] }}</p>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">
                                            {{ $method['desc'] }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <input type="hidden" name="metodo_pagamento" id="orderPaymentMethod"
                        value="{{ old('metodo_pagamento') }}">

                    <!-- Campos de Cartão de Crédito -->
                    <div id="creditCardFields" class="mt-8 p-6 bg-slate-50 rounded-2xl"
                        style="display: {{ old('metodo_pagamento') === 'credit_card' ? 'block' : 'none' }};">
                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="form-group">
                                <label
                                    class="form-label text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Selecione
                                    o Cartão</label>
                                @if(isset($savedCards) && count($savedCards) > 0)
                                    <select name="card_id" id="cardSelect" class="form-select bg-white border-slate-200">
                                        <option value="">Selecione um cartão</option>
                                        @foreach($savedCards as $card)
                                            <option value="{{ $card->id }}" {{ old('card_id') == $card->id ? 'selected' : '' }}>
                                                {{ ucfirst($card->brand) }} •••• {{ $card->last4 }}
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    <div class="bg-amber-50 border border-amber-100 rounded-xl p-4 flex items-start gap-3">
                                        <i class="fas fa-exclamation-triangle text-amber-500 mt-1"></i>
                                        <div>
                                            <p class="text-xs text-amber-800 font-bold mb-1">Nenhum cartão salvo</p>
                                            <a href="{{ route('dash.show', ['section' => 'cartoes']) }}"
                                                class="text-[10px] text-amber-600 underline font-black uppercase">Cadastrar
                                                agora</a>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="form-group">
                                <label
                                    class="form-label text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Parcelas</label>
                                <select name="installments" id="installmentsSelect"
                                    class="form-select bg-white border-slate-200">
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ old('installments', 1) == $i ? 'selected' : '' }}>
                                            {{ $i }}x sem juros</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resumo do Pedido -->
            <div class="lg:col-span-1">
                <div
                    class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-xl shadow-blue-900/5 sticky top-28">
                    <h2 class="text-xl font-bold text-slate-900 mb-6">Resumo</h2>

                    <div class="space-y-4 mb-8">
                        <div class="flex justify-between items-center py-3 border-b border-slate-50">
                            <span class="text-sm font-medium text-slate-500">Quantidade:</span>
                            <span class="text-sm font-bold text-slate-900" id="summary-qty">1 proxy</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-slate-50">
                            <span class="text-sm font-medium text-slate-500">Período:</span>
                            <span class="text-sm font-bold text-slate-900" id="summary-period">Selecione</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-slate-50">
                            <span class="text-sm font-medium text-slate-500">Valor Unitário:</span>
                            <span class="text-sm font-bold text-slate-900" id="summary-unit">R$ 0,00</span>
                        </div>
                        <div class="pt-4 flex flex-col gap-1">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total a
                                pagar</span>
                            <span class="text-4xl font-black text-[#23366f]" id="summary-total">R$ 0,00</span>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full py-4 rounded-2xl bg-[#23366f] text-white font-bold hover:scale-[1.02] transition-all shadow-lg shadow-blue-900/20 flex items-center justify-center gap-3">
                        <i class="fas fa-shopping-cart"></i> Finalizar Pedido
                    </button>

                    <div class="mt-6 flex items-center justify-center gap-2 text-slate-400">
                        <i class="fas fa-shield-alt text-xs"></i>
                        <span class="text-[10px] font-bold uppercase tracking-widest">Pagamento 100% Seguro</span>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
