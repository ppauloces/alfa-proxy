<div class="flex flex-col gap-2 mb-8">
    <p class="text-sm uppercase tracking-[0.35em] text-slate-500">Adquirir proxies</p>
    <h1 class="text-3xl font-bold text-slate-900">Nova Compra</h1>
    <p class="text-slate-500">Configure e adquira novos proxies para suas necessidades.</p>
</div>

@if($errors->novaCompra->any())
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i>
        @foreach($errors->novaCompra->all() as $error)
            {{ $error }}
        @endforeach
    </div>
@endif

<form action="{{ route('compra.processar') }}" method="POST" id="orderForm">
    @csrf
    <div class="grid lg:grid-cols-3 gap-6">
        <!-- Formulário Principal -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Configuração do Proxy -->
            <div class="order-card">
                <h2 class="text-xl font-semibold text-slate-900 mb-4">Configuracao do Proxy</h2>

                <div class="form-group">
                    <label class="form-label">Pais</label>
                                        <select name="pais" class="form-select" required>
                        <option value="">Selecione o pais</option>
                        <option value="BR" {{ old('pais') === 'BR' ? 'selected' : '' }}>Brasil</option>
                        <option value="US" {{ old('pais') === 'US' ? 'selected' : '' }}>Estados Unidos</option>
                        <option value="GB" {{ old('pais') === 'GB' ? 'selected' : '' }}>Reino Unido</option>
                        <option value="DE" {{ old('pais') === 'DE' ? 'selected' : '' }}>Alemanha</option>
                        <option value="FR" {{ old('pais') === 'FR' ? 'selected' : '' }}>Franca</option>
                        <option value="CA" {{ old('pais') === 'CA' ? 'selected' : '' }}>Canada</option>
                        <option value="JP" {{ old('pais') === 'JP' ? 'selected' : '' }}>Japao</option>
                        <option value="AU" {{ old('pais') === 'AU' ? 'selected' : '' }}>Australia</option>
                    </select>

                </div>

                <div class="form-group">
                    <label class="form-label">Motivo do Uso</label>
                                        <select name="motivo" class="form-select" required>
                        <option value="">Selecione o motivo</option>
                        @foreach(['Facebook','Google','TikTok','Bet','Kwai','Instagram','Outros'] as $motivo)
                            <option value="{{ strtolower($motivo) }}" {{ old('motivo') === strtolower($motivo) ? 'selected' : '' }}>{{ $motivo }}</option>
                        @endforeach
                    </select>

                </div>

                <div class="form-group">
                    <label class="form-label">Quantidade de Proxies</label>
                    <input type="number" name="quantidade" value="{{ old('quantidade', 1) }}" id="quantidade" min="1" max="100" class="form-input" required>
                </div>
            </div>

            <!-- Período de Contratação -->
            <div class="order-card">
                <h2 class="text-xl font-semibold text-slate-900 mb-4">Periodo de Contratacao</h2>
                <p class="text-sm text-slate-500 mb-4">Quanto maior o periodo, melhor o preco por proxy!</p>

                <div class="grid md:grid-cols-3 gap-4">
                    <div class="price-card { old('periodo') == 30 ? 'selected' : '' }" data-period="30" data-price="20.00">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-sm font-semibold">30 dias</p>
                        </div>
                        <p class="text-2xl font-bold">R$ 20,00</p>
                        <p class="text-sm opacity-80 mt-1">por proxy</p>
                    </div>

                    <div class="price-card { old('periodo') == 60 ? 'selected' : '' }" data-period="60" data-price="35.00">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-sm font-semibold">60 dias</p>
                            <span class="price-badge">-12%</span>
                        </div>
                        <p class="text-2xl font-bold">R$ 35,00</p>
                        <p class="text-sm opacity-80 mt-1">por proxy</p>
                    </div>

                    <div class="price-card { old('periodo') == 90 ? 'selected' : '' }" data-period="90" data-price="45.00">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-sm font-semibold">90 dias</p>
                            <span class="price-badge">-25%</span>
                        </div>
                        <p class="text-2xl font-bold">R$ 45,00</p>
                        <p class="text-sm opacity-80 mt-1">por proxy</p>
                    </div>

                    <div class="price-card { old('periodo') == 180 ? 'selected' : '' }" data-period="180" data-price="80.00">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-sm font-semibold">180 dias</p>
                            <span class="price-badge">-33%</span>
                        </div>
                        <p class="text-2xl font-bold">R$ 80,00</p>
                        <p class="text-sm opacity-80 mt-1">por proxy</p>
                    </div>

                    <div class="price-card { old('periodo') == 360 ? 'selected' : '' }" data-period="360" data-price="120.00">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-sm font-semibold">360 dias</p>
                            <span class="price-badge">Melhor</span>
                        </div>
                        <p class="text-2xl font-bold">R$ 120,00</p>
                        <p class="text-sm opacity-80 mt-1">por proxy</p>
                    </div>
                </div>
                <input type="hidden" name="periodo" id="periodo" value="{{ old('periodo') }}" required>
            </div>

            <!-- Forma de Pagamento -->
            <div class="order-card">
                <h2 class="text-xl font-semibold text-slate-900 mb-4">Forma de Pagamento</h2>

                <div class="grid md:grid-cols-3 gap-4">
                    <div class="payment-method { old('metodo_pagamento') === 'pix' ? 'selected' : '' }" data-method="pix">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-qrcode text-2xl text-[#4F8BFF]"></i>
                            <div>
                                <p class="font-semibold">PIX</p>
                                <p class="text-xs text-slate-500">Instantaneo</p>
                            </div>
                        </div>
                    </div>

                    <div class="payment-method { old('metodo_pagamento') === 'cartao' ? 'selected' : '' }" data-method="cartao">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-credit-card text-2xl text-[#4F8BFF]"></i>
                            <div>
                                <p class="font-semibold">Cartao</p>
                                <p class="text-xs text-slate-500">Credito/Debito</p>
                            </div>
                        </div>
                    </div>

                    <div class="payment-method { old('metodo_pagamento') === 'usdt' ? 'selected' : '' }" data-method="usdt">
                        <div class="flex items-center gap-3">
                            <i class="fab fa-bitcoin text-2xl text-[#4F8BFF]"></i>
                            <div>
                                <p class="font-semibold">USDT</p>
                                <p class="text-xs text-slate-500">Tether</p>
                            </div>
                        </div>
                    </div>

                    <div class="payment-method { old('metodo_pagamento') === 'btc' ? 'selected' : '' }" data-method="btc">
                        <div class="flex items-center gap-3">
                            <i class="fab fa-btc text-2xl text-[#4F8BFF]"></i>
                            <div>
                                <p class="font-semibold">Bitcoin</p>
                                <p class="text-xs text-slate-500">BTC</p>
                            </div>
                        </div>
                    </div>

                    <div class="payment-method { old('metodo_pagamento') === 'ltc' ? 'selected' : '' }" data-method="ltc">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-coins text-2xl text-[#4F8BFF]"></i>
                            <div>
                                <p class="font-semibold">Litecoin</p>
                                <p class="text-xs text-slate-500">LTC</p>
                            </div>
                        </div>
                    </div>

                    <div class="payment-method { old('metodo_pagamento') === 'bnb' ? 'selected' : '' }" data-method="bnb">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-coins text-2xl text-[#4F8BFF]"></i>
                            <div>
                                <p class="font-semibold">Binance</p>
                                <p class="text-xs text-slate-500">BNB</p>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="metodo_pagamento" id="orderPaymentMethod" value="{{ old('metodo_pagamento') }}" required>
            </div>
        </div>

        <!-- Resumo do Pedido -->
        <div class="lg:col-span-1">
            <div class="order-card sticky top-24">
                <h2 class="text-xl font-semibold text-slate-900 mb-4">Resumo do Pedido</h2>

                <div class="summary-card mb-4">
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-600">Quantidade:</span>
                            <span class="font-semibold" id="summary-qty">1 proxy</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-600">Periodo:</span>
                            <span class="font-semibold" id="summary-period">Selecione</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-600">Valor Unitario:</span>
                            <span class="font-semibold" id="summary-unit">R$ 0,00</span>
                        </div>
                        <div class="border-t border-slate-200 pt-3 mt-3">
                            <div class="flex justify-between">
                                <span class="font-semibold">Total:</span>
                                <span class="text-2xl font-bold text-[#2055dd]" id="summary-total">R$ 0,00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Seu E-mail</label>
                    <input type="email" value="{{ $usuario->email ?? '' }}" class="form-input" disabled>
                </div>

                <button type="submit" class="btn-primary">
                    <i class="fas fa-shopping-cart"></i> Finalizar Pedido
                </button>

                <p class="text-xs text-slate-500 text-center mt-4">
                    Ao finalizar, voce concorda com nossos termos de servico.
                </p>
            </div>
        </div>
    </div>
</form>
