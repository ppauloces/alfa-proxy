@extends('dash.layout')

@section('title', 'AlfaProxy - Nova Compra')

@section('styles')
<style>
.order-card {
    background: #fff;
    border-radius: 28px;
    border: 1px solid rgba(226,232,240,0.9);
    padding: 2rem;
    box-shadow: 0 20px 60px rgba(15,23,42,0.08);
}
.form-group {
    margin-bottom: 1.5rem;
}
.form-label {
    display: block;
    font-size: 0.875rem;
    font-weight: 600;
    color: #475569;
    margin-bottom: 0.5rem;
}
.form-input, .form-select {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid rgba(226,232,240,0.9);
    border-radius: 12px;
    font-size: 0.9375rem;
    transition: all 0.2s ease;
}
.form-input:focus, .form-select:focus {
    outline: none;
    border-color: var(--sf-blue);
    box-shadow: 0 0 0 3px rgba(32,85,221,0.1);
}
.price-card {
    background: linear-gradient(120deg, var(--sf-blue-light), var(--sf-blue));
    color: white;
    padding: 1.5rem;
    border-radius: 20px;
    cursor: pointer;
    transition: all 0.2s ease;
    border: 3px solid transparent;
}
.price-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(32,85,221,0.3);
}
.price-card.selected {
    border-color: #fbbf24;
    box-shadow: 0 12px 30px rgba(251,191,36,0.4);
}
.price-badge {
    background: rgba(255,255,255,0.2);
    padding: 0.25rem 0.75rem;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 600;
    margin-left: 0.5rem;
}
.payment-method {
    padding: 1rem;
    border: 2px solid rgba(226,232,240,0.9);
    border-radius: 16px;
    cursor: pointer;
    transition: all 0.2s ease;
}
.payment-method:hover {
    border-color: var(--sf-blue);
}
.payment-method.selected {
    border-color: var(--sf-blue);
    background: rgba(79,139,255,0.05);
}
.btn-primary {
    width: 100%;
    padding: 1rem 1.5rem;
    border-radius: 12px;
    background: linear-gradient(120deg, var(--sf-blue-light), var(--sf-blue));
    color: #fff;
    font-weight: 600;
    font-size: 1rem;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
}
.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 30px rgba(32,85,221,0.3);
}
.summary-card {
    background: rgba(248,250,252,1);
    padding: 1.5rem;
    border-radius: 20px;
    border: 1px solid rgba(226,232,240,0.9);
}
</style>
@endsection

@section('content')
<div class="flex flex-col gap-2 mb-8">
    <p class="text-sm uppercase tracking-[0.35em] text-slate-500">Adquirir proxies</p>
    <h1 class="text-3xl font-bold text-slate-900">Nova Compra</h1>
    <p class="text-slate-500">Configure e adquira novos proxies para suas necessidades.</p>
</div>

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
                        <option value="BR">🇧🇷 Brasil</option>
                        <option value="US">🇺🇸 Estados Unidos</option>
                        <option value="GB">🇬🇧 Reino Unido</option>
                        <option value="DE">🇩🇪 Alemanha</option>
                        <option value="FR">🇫🇷 Franca</option>
                        <option value="CA">🇨🇦 Canada</option>
                        <option value="JP">🇯🇵 Japao</option>
                        <option value="AU">🇦🇺 Australia</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Motivo do Uso</label>
                    <select name="motivo" class="form-select" required>
                        <option value="">Selecione o motivo</option>
                        <option value="Facebook">Facebook</option>
                        <option value="Google">Google</option>
                        <option value="TikTok">TikTok</option>
                        <option value="Bet">Bet / Sites de Aposta</option>
                        <option value="Kwai">Kwai</option>
                        <option value="Instagram">Instagram</option>
                        <option value="Twitter">Twitter / X</option>
                        <option value="Outros">Outros</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Quantidade de Proxies</label>
                    <input type="number" name="quantidade" id="quantidade" value="1" min="1" max="100" class="form-input" required>
                </div>
            </div>

            <!-- Período de Contratação -->
            <div class="order-card">
                <h2 class="text-xl font-semibold text-slate-900 mb-4">Periodo de Contratacao</h2>
                <p class="text-sm text-slate-500 mb-4">Quanto maior o periodo, melhor o preco por proxy!</p>

                <div class="grid md:grid-cols-3 gap-4">
                    <div class="price-card" data-period="30" data-price="20.00">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-sm font-semibold">30 dias</p>
                        </div>
                        <p class="text-2xl font-bold">R$ 20,00</p>
                        <p class="text-sm opacity-80 mt-1">por proxy</p>
                    </div>

                    <div class="price-card" data-period="60" data-price="35.00">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-sm font-semibold">60 dias</p>
                            <span class="price-badge">-12%</span>
                        </div>
                        <p class="text-2xl font-bold">R$ 35,00</p>
                        <p class="text-sm opacity-80 mt-1">por proxy</p>
                    </div>

                    <div class="price-card" data-period="90" data-price="45.00">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-sm font-semibold">90 dias</p>
                            <span class="price-badge">-25%</span>
                        </div>
                        <p class="text-2xl font-bold">R$ 45,00</p>
                        <p class="text-sm opacity-80 mt-1">por proxy</p>
                    </div>

                    <div class="price-card" data-period="180" data-price="80.00">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-sm font-semibold">180 dias</p>
                            <span class="price-badge">-33%</span>
                        </div>
                        <p class="text-2xl font-bold">R$ 80,00</p>
                        <p class="text-sm opacity-80 mt-1">por proxy</p>
                    </div>

                    <div class="price-card" data-period="360" data-price="120.00">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-sm font-semibold">360 dias</p>
                            <span class="price-badge">Melhor</span>
                        </div>
                        <p class="text-2xl font-bold">R$ 120,00</p>
                        <p class="text-sm opacity-80 mt-1">por proxy</p>
                    </div>
                </div>
                <input type="hidden" name="periodo" id="periodo" required>
            </div>

            <!-- Forma de Pagamento -->
            <div class="order-card">
                <h2 class="text-xl font-semibold text-slate-900 mb-4">Forma de Pagamento</h2>

                <div class="grid md:grid-cols-3 gap-4">
                    <div class="payment-method" data-method="pix">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-qrcode text-2xl text-[#4F8BFF]"></i>
                            <div>
                                <p class="font-semibold">PIX</p>
                                <p class="text-xs text-slate-500">Instantaneo</p>
                            </div>
                        </div>
                    </div>

                    <div class="payment-method" data-method="cartao">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-credit-card text-2xl text-[#4F8BFF]"></i>
                            <div>
                                <p class="font-semibold">Cartao</p>
                                <p class="text-xs text-slate-500">Credito/Debito</p>
                            </div>
                        </div>
                    </div>

                    <div class="payment-method" data-method="usdt">
                        <div class="flex items-center gap-3">
                            <i class="fab fa-bitcoin text-2xl text-[#4F8BFF]"></i>
                            <div>
                                <p class="font-semibold">USDT</p>
                                <p class="text-xs text-slate-500">Tether</p>
                            </div>
                        </div>
                    </div>

                    <div class="payment-method" data-method="btc">
                        <div class="flex items-center gap-3">
                            <i class="fab fa-btc text-2xl text-[#4F8BFF]"></i>
                            <div>
                                <p class="font-semibold">Bitcoin</p>
                                <p class="text-xs text-slate-500">BTC</p>
                            </div>
                        </div>
                    </div>

                    <div class="payment-method" data-method="ltc">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-coins text-2xl text-[#4F8BFF]"></i>
                            <div>
                                <p class="font-semibold">Litecoin</p>
                                <p class="text-xs text-slate-500">LTC</p>
                            </div>
                        </div>
                    </div>

                    <div class="payment-method" data-method="bnb">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-coins text-2xl text-[#4F8BFF]"></i>
                            <div>
                                <p class="font-semibold">Binance</p>
                                <p class="text-xs text-slate-500">BNB</p>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="metodo_pagamento" id="metodo_pagamento" required>
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
@endsection

@section('scripts')
let selectedPeriod = null;
let selectedPrice = 0;
let selectedMethod = null;

// Price card selection
document.querySelectorAll('.price-card').forEach(card => {
    card.addEventListener('click', () => {
        document.querySelectorAll('.price-card').forEach(c => c.classList.remove('selected'));
        card.classList.add('selected');
        selectedPeriod = card.dataset.period;
        selectedPrice = parseFloat(card.dataset.price);
        document.getElementById('periodo').value = selectedPeriod;
        updateSummary();
    });
});

// Payment method selection
document.querySelectorAll('.payment-method').forEach(method => {
    method.addEventListener('click', () => {
        document.querySelectorAll('.payment-method').forEach(m => m.classList.remove('selected'));
        method.classList.add('selected');
        selectedMethod = method.dataset.method;
        document.getElementById('metodo_pagamento').value = selectedMethod;
    });
});

// Quantity change
document.getElementById('quantidade').addEventListener('input', updateSummary);

function updateSummary() {
    const qty = parseInt(document.getElementById('quantidade').value) || 1;
    const total = selectedPrice * qty;

    document.getElementById('summary-qty').textContent = qty + (qty > 1 ? ' proxies' : ' proxy');
    document.getElementById('summary-period').textContent = selectedPeriod ? selectedPeriod + ' dias' : 'Selecione';
    document.getElementById('summary-unit').textContent = 'R$ ' + selectedPrice.toFixed(2).replace('.', ',');
    document.getElementById('summary-total').textContent = 'R$ ' + total.toFixed(2).replace('.', ',');
}

// Form validation
document.getElementById('orderForm').addEventListener('submit', (e) => {
    if (!selectedPeriod) {
        e.preventDefault();
        alert('Por favor, selecione um periodo de contratacao.');
        return;
    }
    if (!selectedMethod) {
        e.preventDefault();
        alert('Por favor, selecione uma forma de pagamento.');
        return;
    }
});
@endsection
