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
                <input type="number" name="valor" id="customAmount" placeholder="Digite o valor" class="form-input" min="1" step="0.01" value="{{ old('valor') }}" required>
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
                <input type="hidden" name="metodo_pagamento" id="walletPaymentMethod" value="{{ old('metodo_pagamento') }}" required>
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
