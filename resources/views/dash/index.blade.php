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
.transactions-table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
}
.proxy-table th,
.transactions-table th {
    text-align: left;
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.15em;
    color: #94a3b8;
    padding-bottom: 0.65rem;
}
.proxy-table th:nth-child(1) { width: 35%; } /* Endereço / Ações */
.proxy-table th:nth-child(2) { width: 8%; }  /* País */
.proxy-table th:nth-child(3) { width: 13%; } /* Compra */
.proxy-table th:nth-child(4) { width: 14%; } /* Expiração */
.proxy-table th:nth-child(5) { width: 15%; } /* Período */
.proxy-table th:nth-child(6) { width: 15%; } /* Auto Renovação */
.proxy-table td,
.transactions-table td {
    padding: 0.85rem 0.5rem;
    border-top: 1px solid rgba(226,232,240,0.85);
    font-size: 0.9rem;
    vertical-align: middle;
}
.address-chip {
    font-family: 'JetBrains Mono', 'Fira Code', monospace;
    background: rgba(148,163,184,0.15);
    padding: 0.4rem 0.7rem;
    border-radius: 12px;
    display: inline-block;
    font-size: 0.75rem;
    line-height: 1.5;
    word-break: break-all;
    max-width: 100%;
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
.rotate-180 { transform: rotate(180deg); }
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
.admin-grid { display: grid; gap: 1.5rem; }
.admin-card,
.vps-card,
.finance-card {
    background: #fff;
    border-radius: 24px;
    border: 1px solid rgba(226,232,240,0.9);
    padding: 1.5rem;
    box-shadow: 0 12px 40px rgba(15,23,42,0.08);
}
.admin-card h2 { margin-bottom: 0.85rem; }
.admin-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.2rem 0.8rem;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 600;
    background: rgba(79,139,255,0.1);
    color: var(--sf-blue);
}
.admin-table {
    width: 100%;
    border-collapse: collapse;
}
.admin-table th {
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.2em;
    color: #94a3b8;
    text-align: left;
    padding-bottom: 0.75rem;
}
.admin-table td {
    padding: 0.9rem 0;
    border-top: 1px solid rgba(226,232,240,0.8);
    font-size: 0.92rem;
}
.badge-status {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.2rem 0.75rem;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: capitalize;
    background: rgba(148,163,184,0.15);
    color: #475569;
}
.badge-status[data-status="disponivel"] { background: rgba(16,185,129,0.12); color: #047857; }
.badge-status[data-status="vendida"] { background: rgba(59,130,246,0.12); color: #1d4ed8; }
.badge-status[data-status="bloqueada"] { background: rgba(248,113,113,0.15); color: #b91c1c; }
.badge-status[data-status="inativa"] { background: rgba(148,163,184,0.2); color: #475569; }
.status-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
}
.status-dot[data-status="disponivel"] { background: #10b981; }
.status-dot[data-status="vendida"] { background: #2563eb; }
.status-dot[data-status="bloqueada"] { background: #ef4444; }
.status-dot[data-status="caida"] { background: #f97316; }
.vps-header {
    display: flex;
    justify-content: space-between;
    gap: 1rem;
    align-items: center;
}
.vps-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    font-size: 0.85rem;
    color: #64748b;
}
.vps-body {
    margin-top: 1.25rem;
    border-top: 1px dashed rgba(148,163,184,0.4);
    padding-top: 1rem;
}
.proxy-pill {
    display: flex;
    justify-content: space-between;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    border-radius: 16px;
    border: 1px solid rgba(226,232,240,0.9);
}
.admin-modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15,23,42,0.6);
    backdrop-filter: blur(2px);
    display: none;
    z-index: 40;
}
.admin-modal-overlay.active { display: block; }
.admin-modal {
    position: fixed;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    width: min(480px, calc(100% - 2rem));
    background: #fff;
    border-radius: 24px;
    padding: 1.75rem;
    box-shadow: 0 40px 80px rgba(15,23,42,0.2);
    z-index: 41;
    display: none;
}
.admin-modal.active { display: block; }
.timeline {
    display: grid;
    gap: 1rem;
    margin-top: 1rem;
}
.timeline-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.8rem 1rem;
    border-radius: 16px;
    border: 1px solid rgba(226,232,240,0.8);
}
.trend-up { color: #059669; }
.trend-down { color: #dc2626; }
.finance-card .chart-bar {
    height: 6px;
    border-radius: 999px;
    background: rgba(148,163,184,0.3);
    overflow: hidden;
}
.finance-card .chart-bar span {
    display: block;
    height: 100%;
    background: linear-gradient(90deg, var(--sf-blue-light), var(--sf-blue));
}
.replace-panel {
    border-radius: 16px;
    border: 1px dashed rgba(148,163,184,0.6);
    padding: 0.9rem;
    margin-top: 0.75rem;
    background: rgba(248,250,252,0.8);
}
</style>
@endsection

@section('content')
@php
use App\Models\User;
use App\Models\Vps;

// Função helper para obter URL da bandeira do país
function getCountryFlag($countryCode) {
    if (empty($countryCode)) {
        return null;
    }
    $code = strtolower($countryCode);
    return "https://flagcdn.com/72x54/{$code}.webp";
}

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
$clientLeads = [];
$collaborators = [];

if (Auth::user()?->isAdmin()) {

    
    $clientLeads = User::where('cargo', 'usuario')->get();

    // Definir $vpsFarm se não foi passada pelo controller
    if (!isset($vpsFarm)) {
        $vpsList = Vps::with('proxies')->orderBy('created_at', 'desc')->get();
        $vpsFarm = $vpsList->map(function ($vps) {
            return [
                'id' => $vps->id,
                'apelido' => $vps->apelido,
                'ip' => $vps->ip,
                'pais' => $vps->pais,
                'hospedagem' => $vps->hospedagem,
                'valor' => 'R$ ' . number_format($vps->valor, 2, ',', '.'),
                'periodo' => $vps->periodo_dias . ' dias',
                'contratada' => $vps->data_contratacao->format('d/m/Y'),
                'status' => $vps->status,
                'proxies' => $vps->proxies->map(function ($proxy) use ($vps) {
                    return [
                        'codigo' => '#' . str_pad($proxy->id, 3, '0', STR_PAD_LEFT),
                        'endpoint' => $vps->ip . ':' . $proxy->porta,
                        'status' => $proxy->disponibilidade ? 'disponivel' : 'vendida',
                    ];
                })->toArray(),
            ];
        })->toArray();
    }

    $adminOverview = [
        ['icon' => 'fa-diagram-project', 'label' => 'Proxies geradas', 'value' => '1.284', 'chip' => '+12% esta semana'],
        ['icon' => 'fa-server', 'label' => 'VPS operacionais', 'value' => '18', 'chip' => '3 renovações próximas'],
        ['icon' => 'fa-sack-dollar', 'label' => 'Receita mensal', 'value' => 'R$ 74.250', 'chip' => '+8% vs. mês anterior'],
        ['icon' => 'fa-shield-halved', 'label' => 'Proxies ativas', 'value' => '742', 'chip' => '96 aguardando health-check'],
    ];

    $generatedProxies = [
        ['numero' => '#001', 'endereco' => '201.94.10.12:7010', 'user' => 'farm_br01', 'senha' => 'Lq9#22A', 'vps' => 'BR-ALFA 01', 'status' => 'Disponivel'],
        ['numero' => '#002', 'endereco' => '201.94.10.12:7011', 'user' => 'farm_br01', 'senha' => 'Ts8@16P', 'vps' => 'BR-ALFA 01', 'status' => 'Disponivel'],
        ['numero' => '#045', 'endereco' => '138.199.54.20:6201', 'user' => 'farm_us03', 'senha' => 'Hg7$33L', 'vps' => 'US-NODE 03', 'status' => 'Vendida'],
        ['numero' => '#078', 'endereco' => '45.152.12.90:8870', 'user' => 'farm_eu02', 'senha' => 'Pa5!90X', 'vps' => 'EU-SCALA 02', 'status' => 'Porta bloqueada'],
    ];

  

    $soldProxyCards = [
        ['label' => 'Proxies vendidas (24h)', 'value' => '86', 'chip' => '+18% vs. ontem'],
        ['label' => 'Ativas x Inativas', 'value' => '712 / 34', 'chip' => '4.5% inativas'],
        ['label' => 'Receita acumulada', 'value' => 'R$ 212.400', 'chip' => '+R$ 9.800 esta semana'],
        ['label' => 'Saldo em carteira', 'value' => 'R$ 18.920', 'chip' => 'Saldo disponível'],
    ];

    $soldProxies = [
        [
            'data' => '13/11 14:20',
            'endereco' => '201.94.10.12:7014 | farm_br01 | Jk8#12S',
            'porta' => '7014',
            'comprador' => 'João Henrique',
            'email' => 'joao@agenciaalpha.com',
            'periodo' => '23 dias',
            'status' => 'disponivel',
            'valor' => 'R$ 210,00',
            'gasto_cliente' => 'R$ 740,00',
            'pedidos' => 4,
        ],
        [
            'data' => '13/11 13:05',
            'endereco' => '138.199.54.20:6202 | farm_us03 | Pl9!00W',
            'porta' => '6202',
            'comprador' => 'Camila Duarte',
            'email' => 'camila@betmind.ai',
            'periodo' => '11 dias',
            'status' => 'vendida',
            'valor' => 'US$ 52,00',
            'gasto_cliente' => 'US$ 1.200,00',
            'pedidos' => 9,
        ],
        [
            'data' => '13/11 11:47',
            'endereco' => '45.152.12.90:8869 | farm_eu02 | Wq1@77L',
            'porta' => '8869',
            'comprador' => 'Pedro Azevedo',
            'email' => 'pedro@tradingpro.io',
            'periodo' => '5 dias',
            'status' => 'bloqueada',
            'valor' => '€ 39,00',
            'gasto_cliente' => '€ 230,00',
            'pedidos' => 3,
        ],
    ];



    
    $financeCards = [
        ['label' => 'Entradas (mês)', 'value' => 'R$ 64.200', 'trend' => '+14%', 'bar' => 82],
        ['label' => 'Saídas (mês)', 'value' => 'R$ 18.450', 'trend' => '-5%', 'bar' => 48],
        ['label' => 'Ticket médio', 'value' => 'R$ 242,00', 'trend' => '+6%', 'bar' => 66],
    ];

    $financeExtract = [
        'saida' => [
            ['descricao' => 'Renovação VPS BR-ALFA', 'categoria' => 'VPS', 'valor' => '-R$ 480,00', 'data' => '12/11 09:15'],
            ['descricao' => 'Licença painel admin', 'categoria' => 'Software', 'valor' => '-R$ 210,00', 'data' => '11/11 17:41'],
            ['descricao' => 'Anúncios performance', 'categoria' => 'Marketing', 'valor' => '-R$ 950,00', 'data' => '10/11 15:03'],
        ],
        'entrada' => [
            ['descricao' => 'Compra João Henrique', 'categoria' => 'PIX', 'valor' => '+R$ 210,00', 'data' => '13/11 14:20'],
            ['descricao' => 'Saldo ApostaPrime', 'categoria' => 'Cartão', 'valor' => '+R$ 1.500,00', 'data' => '13/11 12:08'],
            ['descricao' => 'Recarga GrowthX', 'categoria' => 'USDT', 'valor' => '+US$ 320,00', 'data' => '13/11 09:50'],
        ],
    ];

    $forecast = [
        ['title' => 'Disponíveis para venda', 'value' => '312 proxies', 'detail' => 'Estoque garante 18 dias de operação'],
        ['title' => 'Previsão receita 30d', 'value' => 'R$ 198K', 'detail' => 'Baseada na taxa média de recompra'],
        ['title' => 'Capacidade de VPS', 'value' => '82%', 'detail' => 'Adicionar 2 VPS em dezembro'],
    ];

    $couponCampaigns = [
        ['nome' => 'BLACKWEEK', 'desconto' => '25%', 'uso' => '182 usos', 'validade' => '30/11', 'status' => 'Ativa'],
        ['nome' => 'RESELLER10', 'desconto' => '10%', 'uso' => '58 usos', 'validade' => '31/12', 'status' => 'Ativa'],
        ['nome' => 'WELCOME50', 'desconto' => '50% primeira compra', 'uso' => '412 usos', 'validade' => 'Indefinido', 'status' => 'Pausada'],
    ];
}
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

<div class="flex flex-wrap gap-3 mb-4 max-w-2xl">
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
                            <th class="text-center">Pais</th>
                            <th class="text-center">Compra</th>
                            <th class="text-center">Expiracao</th>
                            <th class="text-center">Periodo</th>
                            <th class="text-center">Auto renovacao</th>
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
                                    <div class="flex items-center justify-center gap-2">
                                        @php
                                            $flagUrl = getCountryFlag($proxy['country_code'] ?? null);
                                        @endphp
                                        @if($flagUrl)
                                            <img src="{{ $flagUrl }}"
                                                 alt="{{ $proxy['country'] ?? 'País' }}"
                                                 class="w-8 h-8 rounded-md object-cover shadow-sm"
                                                 style="image-rendering: -webkit-optimize-contrast; image-rendering: crisp-edges;"
                                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
                                            <span class="text-2xl" style="display:none;">🌐</span>
                                        @else
                                            <span class="text-2xl">🌐</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="text-center">
                                        <p class="text-sm font-semibold text-slate-700">{{ \Carbon\Carbon::parse($proxy['purchased_at'])->format('d/m/Y') }}</p>
                                        <p class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($proxy['purchased_at'])->format('H:i') }}</p>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-center">
                                        <p class="text-sm font-semibold text-slate-700 mb-1">{{ \Carbon\Carbon::parse($proxy['expires_at'])->format('d/m/Y') }}</p>
                                        <span class="badge badge-amber">Renovar</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-gray">{{ $proxy['remaining'] }}</span>
                                </td>
                                <td>
                                    <div class="flex justify-center">
                                        <label class="switch">
                                            <input type="checkbox" {{ $proxy['auto_renew'] ? 'checked' : '' }}>
                                            <span class="slider"></span>
                                        </label>
                                    </div>
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
                Comprar novos proxies
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

<section class="dash-section {{ $currentSection === 'cartoes' ? 'active' : 'hidden' }}" data-section="cartoes">
    @include('dash.partials.cartoes')
</section>

@if(Auth::user()->isAdmin())
    <section class="dash-section {{ $currentSection === 'admin-dashboard' ? 'active' : 'hidden' }}" data-section="admin-dashboard">
        @include('dash.partials.admin.dashboard')
    </section>

    <section class="dash-section {{ $currentSection === 'admin-proxies' ? 'active' : 'hidden' }}" data-section="admin-proxies">
        @include('dash.partials.admin.proxies')
    </section>

    <section class="dash-section {{ $currentSection === 'admin-transacoes' ? 'active' : 'hidden' }}" data-section="admin-transacoes">
        @include('dash.partials.admin.transacoes')
    </section>

    <section class="dash-section {{ $currentSection === 'admin-usuarios' ? 'active' : 'hidden' }}" data-section="admin-usuarios">
        @include('dash.partials.admin.usuarios')
    </section>

    <section class="dash-section {{ $currentSection === 'admin-relatorios' ? 'active' : 'hidden' }}" data-section="admin-relatorios">
        @include('dash.partials.admin.relatorios')
    </section>

    <section class="dash-section {{ $currentSection === 'admin-cupons' ? 'active' : 'hidden' }}" data-section="admin-cupons">
        @include('dash.partials.admin.cupons')
    </section>

    <div id="buyerModalOverlay" class="admin-modal-overlay"></div>
    <div id="buyerModal" class="admin-modal">
        <div class="flex justify-between items-start mb-4">
            <div>
                <p class="text-sm uppercase tracking-[0.3em] text-slate-400">Cliente</p>
                <h3 class="text-2xl font-bold text-slate-900" data-buyer-name>---</h3>
                <p class="text-sm text-slate-500" data-buyer-email>---</p>
            </div>
            <button type="button" class="text-slate-400 hover:text-slate-900" data-close-buyer>
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Pedidos</p>
                <p class="text-lg font-semibold text-slate-900" data-buyer-orders>--</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Gasto total</p>
                <p class="text-lg font-semibold text-slate-900" data-buyer-spent>--</p>
            </div>
        </div>
        <div class="bg-slate-50 rounded-2xl p-4 mb-4 text-sm text-slate-600" data-buyer-note>
            Histórico recente indisponível.
        </div>
        <div class="flex gap-3">
            <button type="button" class="btn-primary flex-1" data-close-buyer>
                <i class="fas fa-envelope"></i> Enviar mensagem
            </button>
            <button type="button" class="btn-secondary flex-1" data-close-buyer>
                <i class="fas fa-user-shield"></i> Ver perfil completo
            </button>
        </div>
    </div>
@endif
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

(() => {
    document.querySelectorAll('[data-admin-accordion]').forEach(trigger => {
        trigger.addEventListener('click', () => {
            const targetId = trigger.getAttribute('data-admin-accordion');
            const target = document.getElementById(targetId);
            target?.classList.toggle('hidden');
            trigger.querySelector('.fa-chevron-down')?.classList.toggle('rotate-180');
        });
    });
})();

(() => {
    const overlay = document.getElementById('buyerModalOverlay');
    const modal = document.getElementById('buyerModal');
    if (!overlay || !modal) return;

    const setModal = (btn) => {
        modal.querySelector('[data-buyer-name]').textContent = btn.dataset.buyerName ?? 'Cliente';
        modal.querySelector('[data-buyer-email]').textContent = btn.dataset.buyerEmail ?? '---';
        modal.querySelector('[data-buyer-orders]').textContent = btn.dataset.buyerOrders ?? '--';
        modal.querySelector('[data-buyer-spent]').textContent = btn.dataset.buyerSpent ?? '--';
        modal.querySelector('[data-buyer-note]').textContent = `Histórico consolidado: ${btn.dataset.buyerOrders ?? '--'} pedidos • ${btn.dataset.buyerSpent ?? '--'} em proxies.`;
    };

    const closeModal = () => {
        overlay.classList.remove('active');
        modal.classList.remove('active');
    };

    document.querySelectorAll('[data-open-buyer]').forEach(btn => {
        btn.addEventListener('click', () => {
            setModal(btn);
            overlay.classList.add('active');
            modal.classList.add('active');
        });
    });

    overlay.addEventListener('click', closeModal);
    modal.querySelectorAll('[data-close-buyer]').forEach(el => el.addEventListener('click', closeModal));
})();

(() => {
    document.querySelectorAll('[data-toggle-port]').forEach(btn => {
        btn.addEventListener('click', () => {
            const isBlocked = btn.dataset.state === 'blocked';
            btn.dataset.state = isBlocked ? 'open' : 'blocked';
            btn.innerHTML = isBlocked
                ? '<i class="fas fa-ban"></i> Bloquear'
                : '<i class="fas fa-lock-open"></i> Desbloquear';
            const target = document.querySelector(btn.dataset.target);
            if (target) {
                const statusLabel = isBlocked ? 'disponivel' : 'bloqueada';
                target.dataset.status = statusLabel;
                target.textContent = statusLabel === 'disponivel' ? 'Disponivel' : 'Porta bloqueada';
            }
        });
    });
})();

(() => {
    document.querySelectorAll('[data-action="test-proxy"]').forEach(btn => {
        btn.addEventListener('click', () => {
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Testando';
            btn.disabled = true;
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
                alert('Teste disparado! Aguarde o retorno do health-check.');
            }, 1200);
        });
    });
})();

(() => {
    document.querySelectorAll('[data-replace-toggle]').forEach(btn => {
        btn.addEventListener('click', () => {
            const target = document.querySelector(btn.dataset.replaceToggle);
            target?.classList.toggle('hidden');
        });
    });
})();

// Modal PIX
@if(session('pix_modal'))
(() => {
    const pixData = @json(session('pix_modal'));

    // Criar modal PIX
    const modalHTML = `
        <div id="pixModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="animation: fadeIn 0.3s;">
            <div class="bg-white rounded-3xl p-8 max-w-md w-full mx-4" style="animation: slideUp 0.3s;">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-slate-900 mb-2">Pagamento PIX</h2>
                    <p class="text-slate-600">Escaneie o QR Code ou copie o código</p>
                </div>

                <div class="bg-slate-50 rounded-2xl p-6 mb-6">
                    <div class="text-center mb-4">
                        <p class="text-sm text-slate-600 mb-2">Valor a pagar</p>
                        <p class="text-3xl font-bold text-slate-900">R$ ${pixData.valor.toFixed(2).replace('.', ',')}</p>
                    </div>

                    <!-- QR Code -->
                    <div class="bg-white p-4 rounded-xl mb-4 flex items-center justify-center" style="min-height: 200px;">
                        ${pixData.qr_code_base64
                            ? `<img src="${pixData.qr_code_base64}" alt="QR Code PIX" class="max-w-full h-auto" style="max-height: 250px;">`
                            : `<div class="text-center">
                                <svg class="w-32 h-32 mx-auto mb-2 text-slate-300" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M3 3h8v8H3V3zm10 0h8v8h-8V3zM3 13h8v8H3v-8zm10 0h8v8h-8v-8z"/>
                                </svg>
                                <p class="text-xs text-slate-500">QR Code PIX</p>
                            </div>`
                        }
                    </div>

                    <!-- Código Copia e Cola -->
                    <div class="bg-white rounded-xl p-4 mb-4">
                        <label class="block text-xs font-semibold text-slate-600 mb-2">PIX Copia e Cola</label>
                        <div class="flex gap-2">
                            <input type="text" id="pixCode" value="${pixData.copia_e_cola}" readonly
                                   class="flex-1 px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm font-mono">
                            <button onclick="copiarPixCode()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                Copiar
                            </button>
                        </div>
                    </div>

                    <!-- Temporizador -->
                    <div class="text-center text-sm text-slate-600 mb-4">
                        <p>Expira em: <span id="pixTimer" class="font-semibold text-amber-600"></span></p>
                    </div>

                    <button onclick="fecharModalPix()" class="w-full py-3 bg-slate-200 text-slate-700 rounded-xl font-semibold hover:bg-slate-300 transition-colors">
                        Fechar
                    </button>
                </div>

                <p class="text-xs text-center text-slate-500">
                    ID da Transação: <span class="font-mono">${pixData.transaction_code}</span>
                </p>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHTML);

    // Funções do modal
    window.copiarPixCode = () => {
        const input = document.getElementById('pixCode');
        input.select();
        document.execCommand('copy');
        alert('Código PIX copiado!');
    };

    window.fecharModalPix = () => {
        document.getElementById('pixModal').remove();
    };

    // Temporizador
    const expiresAt = pixData.expira_timestamp * 1000;
    const updateTimer = () => {
        const now = Date.now();
        const diff = expiresAt - now;

        if (diff <= 0) {
            document.getElementById('pixTimer').textContent = 'Expirado';
            return;
        }

        const minutes = Math.floor(diff / 60000);
        const seconds = Math.floor((diff % 60000) / 1000);
        document.getElementById('pixTimer').textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
    };

    updateTimer();
    setInterval(updateTimer, 1000);
})();
@endif
@endsection

