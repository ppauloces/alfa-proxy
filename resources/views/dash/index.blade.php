@extends('dash.layout')

@section('title', 'AlfaProxy - Gerenciar Proxies')

@section('styles')
<style>
.dash-section {
    display: none;
    animation: fadeIn 0.25s ease;
}
.dash-section.active {
    display: block;
}
.hidden { display: none !important; }
.section-label {
    text-transform: uppercase;
    letter-spacing: 0.35em;
    font-size: 0.75rem;
    color: #94a3b8;
}
.tab-btn {
    padding: 0.6rem 1.4rem;
    border-radius: 999px;
    border: 1px solid transparent;
    background: rgba(255,255,255,0.7);
    color: #0f172a;
    font-weight: 600;
    transition: all 0.2s ease;
    cursor: pointer;
}
.tab-btn.active {
    background: linear-gradient(120deg, var(--sf-blue-light), var(--sf-blue));
    color: #fff;
    box-shadow: 0 12px 30px rgba(32,85,221,0.25);
}
.proxy-card,
.profile-card,
.order-card,
.transactions-card,
.recharge-card,
.support-card,
.settings-card {
    background: #fff;
    border-radius: 28px;
    border: 1px solid rgba(226,232,240,0.9);
    padding: 1.5rem;
    box-shadow: 0 20px 60px rgba(15,23,42,0.08);
}
.proxy-table,
.transactions-table { width: 100%; border-collapse: collapse; }
.proxy-table th,
.transactions-table th {
    text-align: left;
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.15em;
    color: #94a3b8;
    padding-bottom: 0.65rem;
}
.proxy-table td,
.transactions-table td {
    padding: 0.85rem 0;
    border-top: 1px solid rgba(226,232,240,0.85);
    font-size: 0.9rem;
}
.address-chip {
    font-family: 'JetBrains Mono', 'Fira Code', monospace;
    background: rgba(148,163,184,0.15);
    padding: 0.3rem 0.55rem;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.8rem;
}
.action-btn {
    border: 1px solid rgba(148,163,184,0.4);
    border-radius: 999px;
    padding: 0.35rem 0.9rem;
    font-size: 0.75rem;
    font-weight: 600;
    background: #fff;
    color: #0f172a;
    transition: all 0.2s ease;
    cursor: pointer;
}
.action-btn:hover {
    border-color: var(--sf-blue);
    color: var(--sf-blue);
}
.badge {
    padding: 0.3rem 0.65rem;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 600;
}
.badge-amber { background: rgba(251,191,36,0.18); color: #b45309; }
.badge-gray { background: rgba(148,163,184,0.2); color: #475569; }
.badge-success { background: rgba(34,197,94,0.15); color: #15803d; }
.badge-pending { background: rgba(251,191,36,0.18); color: #b45309; }
.badge-failed { background: rgba(239,68,68,0.15); color: #b91c1c; }
.alert {
    padding: 1rem 1.25rem;
    border-radius: 12px;
    margin-bottom: 1.25rem;
    font-size: 0.9375rem;
    display: flex;
    gap: 0.75rem;
    align-items: center;
}
.alert-success {
    background: rgba(34,197,94,0.1);
    color: #15803d;
    border: 1px solid rgba(34,197,94,0.2);
}
.alert-error {
    background: rgba(239,68,68,0.1);
    color: #b91c1c;
    border: 1px solid rgba(239,68,68,0.2);
}
.form-group { margin-bottom: 1.5rem; }
.form-label {
    display: block;
    font-size: 0.875rem;
    font-weight: 600;
    color: #475569;
    margin-bottom: 0.4rem;
}
.form-input,
.form-select,
.form-textarea {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid rgba(226,232,240,0.9);
    border-radius: 12px;
    font-size: 0.9375rem;
    transition: all 0.2s ease;
    background: #fff;
}
.form-input:focus,
.form-select:focus,
.form-textarea:focus {
    outline: none;
    border-color: var(--sf-blue);
    box-shadow: 0 0 0 3px rgba(32,85,221,0.1);
}
.form-input:disabled {
    background: rgba(148,163,184,0.1);
    cursor: not-allowed;
}
.form-textarea {
    min-height: 150px;
    resize: vertical;
}
.btn-primary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.4rem;
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    background: linear-gradient(120deg, var(--sf-blue-light), var(--sf-blue));
    color: #fff;
    font-weight: 600;
    font-size: 0.9375rem;
    border: none;
    cursor: pointer;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 30px rgba(32,85,221,0.3);
}
.btn-primary.btn-block {
    width: 100%;
    padding: 1rem 1.5rem;
}
.btn-secondary {
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    background: rgba(148,163,184,0.15);
    color: #475569;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: background 0.2s ease;
}
.btn-secondary:hover { background: rgba(148,163,184,0.25); }
.btn-danger {
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    background: #ef4444;
    color: white;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: transform 0.2s ease, background 0.2s ease;
}
.btn-danger:hover {
    background: #dc2626;
    transform: translateY(-2px);
}
.order-card { padding: 2rem; }
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
.summary-card {
    background: rgba(248,250,252,1);
    padding: 1.5rem;
    border-radius: 20px;
    border: 1px solid rgba(226,232,240,0.9);
}
.stat-card {
    background: linear-gradient(120deg, var(--sf-blue-light), var(--sf-blue));
    color: white;
    padding: 1.5rem;
    border-radius: 20px;
    box-shadow: 0 12px 30px rgba(32,85,221,0.2);
}
.transactions-table td {
    padding: 1rem 0;
}
.filter-tabs {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
}
.filter-tab {
    padding: 0.5rem 1.25rem;
    border-radius: 999px;
    border: 1px solid rgba(226,232,240,0.9);
    background: white;
    color: #475569;
    font-size: 0.875rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}
.filter-tab.active {
    background: linear-gradient(120deg, var(--sf-blue-light), var(--sf-blue));
    color: white;
    border-color: transparent;
}
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
.amount-btn {
    padding: 1rem;
    border: 2px solid rgba(226,232,240,0.9);
    border-radius: 16px;
    cursor: pointer;
    transition: all 0.2s ease;
    text-align: center;
}
.amount-btn.selected {
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
.support-card { padding: 2rem; }
.contact-method {
    padding: 1.5rem;
    border: 2px solid rgba(226,232,240,0.9);
    border-radius: 20px;
    transition: all 0.2s ease;
    cursor: pointer;
}
.contact-method:hover {
    border-color: var(--sf-blue);
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(32,85,221,0.15);
}
.faq-item {
    border: 1px solid rgba(226,232,240,0.9);
    border-radius: 16px;
    padding: 1.25rem;
    margin-bottom: 1rem;
    cursor: pointer;
    transition: all 0.2s ease;
}
.faq-item:hover { border-color: var(--sf-blue); }
.faq-answer {
    display: none;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid rgba(226,232,240,0.5);
    color: #64748b;
}
.faq-item.open .faq-answer { display: block; }
.faq-icon { transition: transform 0.2s ease; }
.faq-item.open .faq-icon { transform: rotate(180deg); }
.setting-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.25rem 0;
    border-bottom: 1px solid rgba(226,232,240,0.5);
}
.setting-item:last-child { border-bottom: none; }
.danger-zone {
    border: 2px solid rgba(239,68,68,0.3);
    background: rgba(239,68,68,0.02);
}
.switch { position: relative; width: 50px; height: 28px; }
.switch input { opacity: 0; width: 0; height: 0; }
.slider {
    position: absolute;
    cursor: pointer;
    inset: 0;
    background-color: rgba(148,163,184,0.4);
    transition: 0.2s;
    border-radius: 999px;
}
.slider:before {
    content: "";
    position: absolute;
    height: 22px;
    width: 22px;
    left: 3px;
    top: 3px;
    background-color: white;
    transition: 0.2s;
    border-radius: 50%;
    box-shadow: 0 2px 6px rgba(15,23,42,0.15);
}
.switch input:checked + .slider { background-color: var(--sf-blue); }
.switch input:checked + .slider:before { transform: translateX(22px); }
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(8px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
@endsection

@section('content')
@php
$proxyGroups = $proxyGroups ?? [
    'SOCKS5' => [
        [
            'ip' => '191.96.12.45',
            'port' => '8080',
            'user' => 'alpha_user',
            'password' => 'R3dh##92',
            'country' => 'Brasil',
            'country_code' => 'BR',
            'purchased_at' => '2024-10-02 14:30',
            'expires_at' => '2024-11-02',
            'remaining' => '21 dias',
            'auto_renew' => true,
        ],
        [
            'ip' => '89.187.180.14',
            'port' => '6500',
            'user' => 'stack_ops',
            'password' => 'alpha##44',
            'country' => 'Estados Unidos',
            'country_code' => 'US',
            'purchased_at' => '2024-09-20 08:12',
            'expires_at' => '2024-10-20',
            'remaining' => '8 dias',
            'auto_renew' => false,
        ],
    ],
];
$pagamentos = $pagamentos ?? collect([
    (object)[
        'id' => 'TXN-001',
        'valor' => 100.00,
        'status' => 1,
        'metodo_pagamento' => 'PIX',
        'created_at' => now()->subDays(2),
    ],
    (object)[
        'id' => 'TXN-002',
        'valor' => 50.00,
        'status' => 0,
        'metodo_pagamento' => 'Cartão',
        'created_at' => now()->subHours(5),
    ],
]);
$pagamentos_aprovados = $pagamentos_aprovados ?? $pagamentos->where('status', 1);
$pagamentos_pendentes = $pagamentos_pendentes ?? $pagamentos->where('status', 0);
$totalValor = $totalValor ?? $pagamentos_aprovados->sum('valor');
$transacoes = $transacoes ?? $pagamentos;
$currentSection = $activeSection ?? 'proxies';
@endphp

<div class="space-y-10" data-sections-wrapper>
<section class="dash-section {{ $currentSection === 'proxies' ? 'active' : 'hidden' }}" data-section="proxies">
<div class="flex flex-col gap-2">
    <p class="text-sm uppercase tracking-[0.35em] text-slate-500">Proxies ativos</p>
    <div class="flex flex-wrap items-center gap-4 justify-between">
        <h1 class="text-3xl font-bold text-slate-900">Gerencie seus IPs</h1>
        <div class="flex flex-wrap gap-3">
            <button type="button" data-section-link="nova-compra" class="px-5 py-2 rounded-2xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition-colors">
                Comprar novos proxies
            </button>
        </div>
    </div>
    <p class="text-slate-500 max-w-2xl">Veja o que falta para cada contratacao expirar, teste as rotas e controle a renovacao automatica.</p>
    @if(session('proxies_success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> {{ session('proxies_success') }}
        </div>
    @endif
    @if($errors->getBag('default')->has('error'))
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i> {{ $errors->getBag('default')->first('error') }}
        </div>
    @endif
</div>

<div class="flex flex-wrap gap-3">
    @foreach ($proxyGroups as $group => $proxies)
        <button type="button" class="tab-btn {{ $loop->first ? 'active' : '' }}" data-tab="{{ $group }}">
            {{ \Illuminate\Support\Str::headline($group) }}
            <span class="text-xs font-normal opacity-70">({{ count($proxies) }})</span>
        </button>
    @endforeach
</div>

@foreach ($proxyGroups as $group => $proxies)
    <div class="proxy-card {{ $loop->first ? '' : 'hidden' }}" data-tab-panel="{{ $group }}">
        <div class="flex flex-wrap justify-between items-center gap-4 mb-6">
            <div>
                <h2 class="text-xl font-semibold text-slate-900">{{ \Illuminate\Support\Str::headline($group) }}</h2>
                <p class="text-sm text-slate-500">
                    @if(count($proxies))
                        {{ count($proxies) }} proxies listados abaixo.
                    @else
                        Nenhum proxy neste tipo ainda.
                    @endif
                </p>
            </div>
            <div class="flex items-center gap-2 text-sm text-slate-500">
                <i class="fas fa-shield-alt text-[#4F8BFF]"></i>
                Monitoramento ativo
            </div>
        </div>
        @if(count($proxies))
            <div class="overflow-x-auto">
                <table class="proxy-table">
                    <thead>
                        <tr>
                            <th>Endereco / acoes</th>
                            <th>Pais</th>
                            <th>Compra</th>
                            <th>Expiracao</th>
                            <th>Periodo</th>
                            <th>Auto renovacao</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($proxies as $proxy)
                            <tr>
                                <td class="space-y-2">
                                    <div class="address-chip">
                                        {{ $proxy['ip'] }}:{{ $proxy['port'] }} | {{ $proxy['user'] }} | {{ $proxy['password'] }}
                                    </div>
                                    <div class="flex flex-wrap gap-2">
                                        <button class="action-btn">
                                            <i class="fas fa-vial text-xs"></i>
                                            Testar proxy
                                        </button>
                                        <button class="action-btn" onclick="copyToClipboard('{{ $proxy['ip'] }}:{{ $proxy['port'] }}:{{ $proxy['user'] }}:{{ $proxy['password'] }}')">
                                            <i class="fas fa-copy text-xs"></i>
                                            Copiar
                                        </button>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xl">{{ $proxy['country_code'] ?? '🌐' }}</span>
                                        <span class="text-sm font-semibold text-slate-700">{{ $proxy['country'] }}</span>
                                    </div>
                                </td>
                                <td>
                                    <p class="text-sm font-semibold text-slate-700">{{ \Carbon\Carbon::parse($proxy['purchased_at'])->format('d/m/Y') }}</p>
                                    <p class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($proxy['purchased_at'])->format('H:i') }}</p>
                                </td>
                                <td>
                                    <p class="text-sm font-semibold text-slate-700">{{ \Carbon\Carbon::parse($proxy['expires_at'])->format('d/m/Y') }}</p>
                                    <span class="badge badge-amber">Renovar</span>
                                </td>
                                <td>
                                    <span class="badge badge-gray">{{ $proxy['remaining'] }}</span>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" {{ $proxy['auto_renew'] ? 'checked' : '' }}>
                                        <span class="slider"></span>
                                    </label>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="border border-dashed border-slate-200 rounded-2xl p-10 text-center">
                <p class="text-lg font-semibold text-slate-700 mb-2">Nenhum proxy cadastrado neste tipo.</p>
                <p class="text-sm text-slate-500 mb-4">Contrate um novo proxy para visualizar aqui.</p>
                <button type="button" data-section-link="nova-compra" class="inline-flex items-center gap-2 px-5 py-2 rounded-2xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition-colors">
                    Ver planos disponiveis
                    <i class="fas fa-arrow-right text-xs"></i>
                </button>
            </div>
        @endif
    </div>
@endforeach

</section>

<section class="dash-section {{ $currentSection === 'perfil' ? 'active' : 'hidden' }}" data-section="perfil">
    @include('dash.partials.perfil')
</section>

<section class="dash-section {{ $currentSection === 'nova-compra' ? 'active' : 'hidden' }}" data-section="nova-compra">
    @include('dash.partials.nova-compra')
</section>

<section class="dash-section {{ $currentSection === 'transacoes' ? 'active' : 'hidden' }}" data-section="transacoes">
    @if(session('transacoes_success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> {{ session('transacoes_success') }}
        </div>
    @endif
    @include('dash.partials.transacoes')
</section>

<section class="dash-section {{ $currentSection === 'saldo' ? 'active' : 'hidden' }}" data-section="saldo">
    @if(session('saldo_success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> {{ session('saldo_success') }}
        </div>
    @endif
    @include('dash.partials.saldo')
</section>

<section class="dash-section {{ $currentSection === 'suporte' ? 'active' : 'hidden' }}" data-section="suporte">
    @include('dash.partials.suporte')
</section>

<section class="dash-section {{ $currentSection === 'configuracoes' ? 'active' : 'hidden' }}" data-section="configuracoes">
    @include('dash.partials.configuracoes')
</section>
</div>
@endsection

@section('scripts')
window.initialDashSection = @json($currentSection ?? 'proxies');

(() => {
    const tabBtns = document.querySelectorAll('[data-tab]');
    const tabPanels = document.querySelectorAll('[data-tab-panel]');
    if (!tabBtns.length) return;

    tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const target = btn.dataset.tab;
            tabBtns.forEach(b => b.classList.remove('active'));
            tabPanels.forEach(panel => panel.classList.toggle('hidden', panel.dataset.tabPanel !== target));
            btn.classList.add('active');
        });
    });
})();

window.copyToClipboard = function(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('Proxy copiado para a area de transferencia!');
    });
};

(() => {
    const orderForm = document.getElementById('orderForm');
    if (!orderForm) return;

    const priceCards = orderForm.querySelectorAll('.price-card');
    const paymentMethods = orderForm.querySelectorAll('.payment-method');
    const periodInput = document.getElementById('periodo');
    const paymentInput = document.getElementById('orderPaymentMethod');
    const quantityInput = document.getElementById('quantidade');
    let selectedPrice = 0;
    let selectedPeriod = periodInput?.value || null;

    const refreshFromDefaults = () => {
        priceCards.forEach(card => {
            if (card.classList.contains('selected')) {
                selectedPeriod = card.dataset.period;
                selectedPrice = parseFloat(card.dataset.price);
            }
        });
        paymentMethods.forEach(method => {
            if (method.classList.contains('selected')) {
                paymentInput.value = method.dataset.method;
            }
        });
        updateSummary();
    };

    const updateSummary = () => {
        const qty = parseInt(quantityInput.value || '1', 10);
        const total = selectedPrice * qty;
        document.getElementById('summary-qty').textContent = `${qty} ${qty > 1 ? 'proxies' : 'proxy'}`;
        document.getElementById('summary-period').textContent = selectedPeriod ? `${selectedPeriod} dias` : 'Selecione';
        document.getElementById('summary-unit').textContent = `R$ ${selectedPrice.toFixed(2).replace('.', ',')}`;
        document.getElementById('summary-total').textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;
    };

    priceCards.forEach(card => {
        card.addEventListener('click', () => {
            priceCards.forEach(c => c.classList.remove('selected'));
            card.classList.add('selected');
            selectedPeriod = card.dataset.period;
            selectedPrice = parseFloat(card.dataset.price);
            periodInput.value = selectedPeriod;
            updateSummary();
        });
    });

    paymentMethods.forEach(method => {
        method.addEventListener('click', () => {
            if (!method.closest('#rechargeForm')) {
                paymentMethods.forEach(m => m.classList.remove('selected'));
                method.classList.add('selected');
                paymentInput.value = method.dataset.method;
            }
        });
    });

    quantityInput.addEventListener('input', updateSummary);

    orderForm.addEventListener('submit', (event) => {
        if (!selectedPeriod) {
            event.preventDefault();
            alert('Por favor, selecione um periodo de contratacao.');
            return;
        }
        if (!paymentInput.value) {
            event.preventDefault();
            alert('Por favor, selecione uma forma de pagamento.');
        }
    });

    refreshFromDefaults();
})();

(() => {
    const filterTabs = document.querySelectorAll('.filter-tab');
    const rows = document.querySelectorAll('#transactionsBody tr');
    if (!filterTabs.length || !rows.length) return;

    filterTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            filterTabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            const filter = tab.dataset.filter;

            rows.forEach(row => {
                const status = row.dataset.status;
                let visible = false;
                if (filter === 'all') visible = true;
                else if (filter === 'aprovadas' && status === '1') visible = true;
                else if (filter === 'pendentes' && status === '0') visible = true;
                else if (filter === 'falhas' && status !== '0' && status !== '1') visible = true;
                row.style.display = visible ? '' : 'none';
            });
        });
    });
})();

(() => {
    const rechargeForm = document.getElementById('rechargeForm');
    if (!rechargeForm) return;

    const switchTabs = document.querySelectorAll('.switch-tab');
    const addBalance = document.getElementById('addBalance');
    const historyBalance = document.getElementById('historyBalance');
    const amountButtons = rechargeForm.querySelectorAll('.amount-btn');
    const paymentMethods = rechargeForm.querySelectorAll('.payment-method');
    const paymentInput = document.getElementById('walletPaymentMethod');
    const customAmount = document.getElementById('customAmount');

    switchTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            switchTabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            const view = tab.dataset.view;
            addBalance.style.display = view === 'add' ? 'block' : 'none';
            historyBalance.style.display = view === 'history' ? 'block' : 'none';
        });
    });

    amountButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            amountButtons.forEach(b => b.classList.remove('selected'));
            btn.classList.add('selected');
            customAmount.value = btn.dataset.amount;
        });
    });

    paymentMethods.forEach(method => {
        if (method.closest('#rechargeForm')) {
            method.addEventListener('click', () => {
                paymentMethods.forEach(m => m.classList.remove('selected'));
                method.classList.add('selected');
                paymentInput.value = method.dataset.method;
            });
        }
    });

    rechargeForm.addEventListener('submit', (event) => {
        const amount = parseFloat(customAmount.value || '0');
        if (!paymentInput.value) {
            event.preventDefault();
            alert('Selecione um metodo de pagamento.');
            return;
        }
        if (!amount || amount < 1) {
            event.preventDefault();
            alert('Informe um valor valido.');
        }
    });
})();

(() => {
    document.querySelectorAll('.faq-item').forEach(item => {
        item.addEventListener('click', () => {
            item.classList.toggle('open');
        });
    });
})();

(() => {
    const saveSettingsBtn = document.querySelector('[data-settings-save]');
    if (!saveSettingsBtn) return;
    saveSettingsBtn.addEventListener('click', () => {
        alert('Configuracoes salvas com sucesso!');
    });
})();
@endsection

