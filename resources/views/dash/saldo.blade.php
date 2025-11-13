@extends('dash.layout')

@section('title', 'AlfaProxy - Carteira')

@section('styles')
<style>
.balance-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2.5rem;
    border-radius: 28px;
    box-shadow: 0 24px 60px rgba(102,126,234,0.3);
    position: relative;
    overflow: hidden;
}
.balance-card::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 400px;
    height: 400px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
}
.recharge-card {
    background: #fff;
    border-radius: 28px;
    border: 1px solid rgba(226,232,240,0.9);
    padding: 2rem;
    box-shadow: 0 20px 60px rgba(15,23,42,0.08);
}
.amount-btn {
    padding: 1rem;
    border: 2px solid rgba(226,232,240,0.9);
    border-radius: 16px;
    cursor: pointer;
    transition: all 0.2s ease;
    text-align: center;
}
.amount-btn:hover {
    border-color: var(--sf-blue);
}
.amount-btn.selected {
    border-color: var(--sf-blue);
    background: rgba(79,139,255,0.05);
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
.switch-tabs {
    display: flex;
    background: rgba(148,163,184,0.1);
    padding: 0.25rem;
    border-radius: 12px;
    gap: 0.25rem;
}
.switch-tab {
    flex: 1;
    padding: 0.75rem 1rem;
    border-radius: 10px;
    border: none;
    background: transparent;
    color: #475569;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}
.switch-tab.active {
    background: white;
    color: var(--sf-blue);
    box-shadow: 0 2px 8px rgba(15,23,42,0.1);
}
.history-item {
    padding: 1rem;
    border-bottom: 1px solid rgba(226,232,240,0.5);
    transition: background 0.2s ease;
}
.history-item:hover {
    background: rgba(248,250,252,0.8);
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
.badge-success {
    background: rgba(34,197,94,0.15);
    color: #15803d;
    padding: 0.3rem 0.65rem;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 600;
}
.badge-pending {
    background: rgba(251,191,36,0.18);
    color: #b45309;
    padding: 0.3rem 0.65rem;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 600;
}
.form-input {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid rgba(226,232,240,0.9);
    border-radius: 12px;
    font-size: 0.9375rem;
    transition: all 0.2s ease;
}
.form-input:focus {
    outline: none;
    border-color: var(--sf-blue);
    box-shadow: 0 0 0 3px rgba(32,85,221,0.1);
}
</style>
@endsection

@section('content')
@php
$transacoes = $transacoes ?? collect();
@endphp

<div class="flex flex-col gap-2 mb-8">
    <p class="text-sm uppercase tracking-[0.35em] text-slate-500">Financeiro</p>
    <h1 class="text-3xl font-bold text-slate-900">Carteira</h1>
    <p class="text-slate-500">Gerencie seu saldo e adicione créditos para futuras compras.</p>
</div>

<!-- Cartão de Saldo -->
<div class="balance-card mb-8">
    <div class="relative z-10">
        <p class="text-sm uppercase tracking-wider opacity-80 mb-2">Saldo Disponível</p>
        <p class="text-5xl font-bold mb-6">R$ {{ number_format($usuario->saldo ?? 0, 2, ',', '.') }}</p>
        <div class="flex items-center gap-4">
            <div>
                <p class="text-xs opacity-70">Nome do Titular</p>
                <p class="font-semibold">{{ $usuario->name ?? 'Usuario' }}</p>
            </div>
            <div class="ml-auto">
                <p class="text-xs opacity-70">ID da Conta</p>
                <p class="font-semibold">#{{ $usuario->id ?? '000' }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Switch Entre Adicionar e Histórico -->
<div class="recharge-card">
    <div class="switch-tabs mb-6">
        <button class="switch-tab active" data-view="add">
            <i class="fas fa-plus-circle"></i> Adicionar Saldo
        </button>
        <button class="switch-tab" data-view="history">
            <i class="fas fa-history"></i> Histórico de Recargas
        </button>
    </div>

    <!-- View: Adicionar Saldo -->
    <div id="addBalance" class="view-content">
        <h2 class="text-xl font-semibold text-slate-900 mb-4">Adicionar Saldo à Carteira</h2>
        <p class="text-sm text-slate-500 mb-6">Escolha o valor que deseja adicionar e a forma de pagamento.</p>

        <form action="{{ route('saldo.adicionar') }}" method="POST" id="rechargeForm">
            @csrf
            <div class="mb-6">
                <label class="block text-sm font-semibold text-slate-700 mb-3">Valores Pré-definidos</label>
                <div class="grid grid-cols-3 md:grid-cols-6 gap-3">
                    <div class="amount-btn" data-amount="10">
                        <p class="text-sm text-slate-600">R$</p>
                        <p class="text-xl font-bold text-slate-900">10</p>
                    </div>
                    <div class="amount-btn" data-amount="25">
                        <p class="text-sm text-slate-600">R$</p>
                        <p class="text-xl font-bold text-slate-900">25</p>
                    </div>
                    <div class="amount-btn" data-amount="50">
                        <p class="text-sm text-slate-600">R$</p>
                        <p class="text-xl font-bold text-slate-900">50</p>
                    </div>
                    <div class="amount-btn" data-amount="100">
                        <p class="text-sm text-slate-600">R$</p>
                        <p class="text-xl font-bold text-slate-900">100</p>
                    </div>
                    <div class="amount-btn" data-amount="200">
                        <p class="text-sm text-slate-600">R$</p>
                        <p class="text-xl font-bold text-slate-900">200</p>
                    </div>
                    <div class="amount-btn" data-amount="500">
                        <p class="text-sm text-slate-600">R$</p>
                        <p class="text-xl font-bold text-slate-900">500</p>
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-slate-700 mb-3">Ou insira um valor personalizado</label>
                <input type="number" name="valor" id="customAmount" placeholder="Digite o valor" class="form-input" min="1" step="0.01" required>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-slate-700 mb-3">Método de Pagamento</label>
                <div class="grid md:grid-cols-3 gap-4">
                    <div class="payment-method" data-method="pix">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-qrcode text-2xl text-[#4F8BFF]"></i>
                            <div>
                                <p class="font-semibold">PIX</p>
                                <p class="text-xs text-slate-500">Instantâneo</p>
                            </div>
                        </div>
                    </div>

                    <div class="payment-method" data-method="cartao">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-credit-card text-2xl text-[#4F8BFF]"></i>
                            <div>
                                <p class="font-semibold">Cartão</p>
                                <p class="text-xs text-slate-500">Crédito/Débito</p>
                            </div>
                        </div>
                    </div>

                    <div class="payment-method" data-method="crypto">
                        <div class="flex items-center gap-3">
                            <i class="fab fa-bitcoin text-2xl text-[#4F8BFF]"></i>
                            <div>
                                <p class="font-semibold">Crypto</p>
                                <p class="text-xs text-slate-500">BTC/USDT/BNB</p>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="metodo_pagamento" id="metodo_pagamento" required>
            </div>

            <div class="bg-slate-50 p-4 rounded-xl mb-6">
                <div class="flex items-center gap-2 text-sm text-slate-600">
                    <i class="fas fa-info-circle text-[#4F8BFF]"></i>
                    <p>Alguns métodos de pagamento podem cobrar taxas adicionais</p>
                </div>
            </div>

            <button type="submit" class="btn-primary">
                <i class="fas fa-check-circle"></i> Confirmar Recarga
            </button>
        </form>
    </div>

    <!-- View: Histórico de Recargas -->
    <div id="historyBalance" class="view-content" style="display: none;">
        <h2 class="text-xl font-semibold text-slate-900 mb-4">Histórico de Recargas</h2>
        <p class="text-sm text-slate-500 mb-6">Visualize todas as recargas realizadas em sua carteira.</p>

        @if(count($transacoes) > 0)
            <div class="space-y-2">
                @foreach($transacoes as $transacao)
                    <div class="history-item rounded-xl">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#4F8BFF] to-[#2055dd] flex items-center justify-center">
                                    <i class="fas fa-plus text-white text-sm"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-900">Recarga de Saldo</p>
                                    <p class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($transacao->created_at)->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-slate-900">R$ {{ number_format($transacao->valor, 2, ',', '.') }}</p>
                                @if($transacao->status == 1)
                                    <span class="badge-success">Aprovada</span>
                                @else
                                    <span class="badge-pending">Pendente</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="border border-dashed border-slate-200 rounded-2xl p-10 text-center">
                <i class="fas fa-wallet text-5xl text-slate-300 mb-4"></i>
                <p class="text-lg font-semibold text-slate-700 mb-2">Nenhuma recarga realizada</p>
                <p class="text-sm text-slate-500">Adicione saldo à sua carteira para começar.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
// Switch between views
document.querySelectorAll('.switch-tab').forEach(tab => {
    tab.addEventListener('click', () => {
        document.querySelectorAll('.switch-tab').forEach(t => t.classList.remove('active'));
        tab.classList.add('active');

        const view = tab.dataset.view;
        document.getElementById('addBalance').style.display = view === 'add' ? 'block' : 'none';
        document.getElementById('historyBalance').style.display = view === 'history' ? 'block' : 'none';
    });
});

// Amount selection
document.querySelectorAll('.amount-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.amount-btn').forEach(b => b.classList.remove('selected'));
        btn.classList.add('selected');
        document.getElementById('customAmount').value = btn.dataset.amount;
    });
});

// Payment method selection
let selectedMethod = null;
document.querySelectorAll('.payment-method').forEach(method => {
    method.addEventListener('click', () => {
        document.querySelectorAll('.payment-method').forEach(m => m.classList.remove('selected'));
        method.classList.add('selected');
        selectedMethod = method.dataset.method;
        document.getElementById('metodo_pagamento').value = selectedMethod;
    });
});

// Form validation
document.getElementById('rechargeForm').addEventListener('submit', (e) => {
    if (!selectedMethod) {
        e.preventDefault();
        alert('Por favor, selecione um método de pagamento.');
        return;
    }

    const amount = parseFloat(document.getElementById('customAmount').value);
    if (!amount || amount < 1) {
        e.preventDefault();
        alert('Por favor, insira um valor válido.');
        return;
    }
});
@endsection
