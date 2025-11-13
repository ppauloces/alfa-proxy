@extends('dash.layout')

@section('title', 'AlfaProxy - Histórico de Transações')

@section('styles')
<style>
.transactions-card {
    background: #fff;
    border-radius: 28px;
    border: 1px solid rgba(226,232,240,0.9);
    padding: 2rem;
    box-shadow: 0 20px 60px rgba(15,23,42,0.08);
}
.stat-card {
    background: linear-gradient(120deg, var(--sf-blue-light), var(--sf-blue));
    color: white;
    padding: 1.5rem;
    border-radius: 20px;
    box-shadow: 0 12px 30px rgba(32,85,221,0.2);
}
.transactions-table { width: 100%; border-collapse: collapse; }
.transactions-table th {
    text-align: left;
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.15em;
    color: #94a3b8;
    padding-bottom: 0.65rem;
}
.transactions-table td {
    padding: 1rem 0;
    border-top: 1px solid rgba(226,232,240,0.85);
    font-size: 0.9rem;
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
.badge-failed {
    background: rgba(239,68,68,0.15);
    color: #b91c1c;
    padding: 0.3rem 0.65rem;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 600;
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
.filter-tab:hover {
    border-color: var(--sf-blue);
}
.filter-tab.active {
    background: linear-gradient(120deg, var(--sf-blue-light), var(--sf-blue));
    color: white;
    border-color: transparent;
}
</style>
@endsection

@section('content')
@php
// Dados de exemplo caso não venham do controller
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
$pagamentos_aprovados = $pagamentos_aprovados ?? collect();
$pagamentos_pendentes = $pagamentos_pendentes ?? collect();
$totalValor = $totalValor ?? 0;
@endphp

<div class="flex flex-col gap-2 mb-8">
    <p class="text-sm uppercase tracking-[0.35em] text-slate-500">Financeiro</p>
    <h1 class="text-3xl font-bold text-slate-900">Histórico de Transações</h1>
    <p class="text-slate-500">Acompanhe todas as suas transações e pagamentos realizados.</p>
</div>

<!-- Cards de Estatísticas -->
<div class="grid md:grid-cols-3 gap-6 mb-8">
    <div class="stat-card">
        <div class="flex items-center justify-between mb-2">
            <p class="text-sm uppercase tracking-wider opacity-80">Total Gasto</p>
            <i class="fas fa-chart-line text-2xl opacity-60"></i>
        </div>
        <p class="text-3xl font-bold">R$ {{ number_format($totalValor, 2, ',', '.') }}</p>
        <p class="text-sm opacity-80 mt-1">Em todas as transações aprovadas</p>
    </div>

    <div class="stat-card" style="background: linear-gradient(120deg, #10b981, #059669);">
        <div class="flex items-center justify-between mb-2">
            <p class="text-sm uppercase tracking-wider opacity-80">Aprovadas</p>
            <i class="fas fa-check-circle text-2xl opacity-60"></i>
        </div>
        <p class="text-3xl font-bold">{{ count($pagamentos_aprovados) }}</p>
        <p class="text-sm opacity-80 mt-1">Transações concluídas</p>
    </div>

    <div class="stat-card" style="background: linear-gradient(120deg, #f59e0b, #d97706);">
        <div class="flex items-center justify-between mb-2">
            <p class="text-sm uppercase tracking-wider opacity-80">Pendentes</p>
            <i class="fas fa-clock text-2xl opacity-60"></i>
        </div>
        <p class="text-3xl font-bold">{{ count($pagamentos_pendentes) }}</p>
        <p class="text-sm opacity-80 mt-1">Aguardando pagamento</p>
    </div>
</div>

<!-- Lista de Transações -->
<div class="transactions-card">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-slate-900">Todas as Transações</h2>
        <button onclick="window.location.reload()" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:border-slate-400 transition-colors">
            <i class="fas fa-sync-alt"></i> Atualizar
        </button>
    </div>

    <div class="filter-tabs">
        <button class="filter-tab active" data-filter="all">Todas</button>
        <button class="filter-tab" data-filter="aprovadas">Aprovadas</button>
        <button class="filter-tab" data-filter="pendentes">Pendentes</button>
        <button class="filter-tab" data-filter="falhas">Falhas</button>
    </div>

    @if(count($pagamentos) > 0)
        <div class="overflow-x-auto">
            <table class="transactions-table">
                <thead>
                    <tr>
                        <th>ID da Transação</th>
                        <th>Método de Pagamento</th>
                        <th>Valor</th>
                        <th>Data</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="transactionsBody">
                    @foreach ($pagamentos as $pagamento)
                        <tr data-status="{{ $pagamento->status }}">
                            <td>
                                <p class="font-mono text-sm font-semibold text-slate-900">#{{ $pagamento->id }}</p>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    @if($pagamento->metodo_pagamento == 'PIX')
                                        <i class="fas fa-qrcode text-[#4F8BFF]"></i>
                                    @elseif($pagamento->metodo_pagamento == 'Cartao' || $pagamento->metodo_pagamento == 'Cartão')
                                        <i class="fas fa-credit-card text-[#4F8BFF]"></i>
                                    @else
                                        <i class="fab fa-bitcoin text-[#4F8BFF]"></i>
                                    @endif
                                    <span class="font-semibold text-slate-700">{{ $pagamento->metodo_pagamento }}</span>
                                </div>
                            </td>
                            <td>
                                <p class="font-semibold text-lg text-slate-900">R$ {{ number_format($pagamento->valor, 2, ',', '.') }}</p>
                            </td>
                            <td>
                                <p class="text-sm font-semibold text-slate-700">{{ \Carbon\Carbon::parse($pagamento->created_at)->format('d/m/Y') }}</p>
                                <p class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($pagamento->created_at)->format('H:i') }}</p>
                            </td>
                            <td>
                                @if($pagamento->status == 1)
                                    <span class="badge-success">
                                        <i class="fas fa-check"></i> Aprovada
                                    </span>
                                @elseif($pagamento->status == 0)
                                    <span class="badge-pending">
                                        <i class="fas fa-clock"></i> Pendente
                                    </span>
                                @else
                                    <span class="badge-failed">
                                        <i class="fas fa-times"></i> Falha
                                    </span>
                                @endif
                            </td>
                            <td>
                                <button class="text-[#4F8BFF] hover:text-[#2055dd] font-semibold text-sm" onclick="viewTransaction('{{ $pagamento->id }}')">
                                    Ver Detalhes
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="border border-dashed border-slate-200 rounded-2xl p-10 text-center">
            <i class="fas fa-receipt text-5xl text-slate-300 mb-4"></i>
            <p class="text-lg font-semibold text-slate-700 mb-2">Nenhuma transação encontrada</p>
            <p class="text-sm text-slate-500 mb-4">Você ainda não realizou nenhuma transação.</p>
            <a href="{{ route('compra.nova') }}" class="inline-flex items-center gap-2 px-5 py-2 rounded-2xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition-colors">
                Fazer primeira compra
                <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>
    @endif
</div>
@endsection

@section('scripts')
// Filter tabs
document.querySelectorAll('.filter-tab').forEach(tab => {
    tab.addEventListener('click', () => {
        document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
        tab.classList.add('active');

        const filter = tab.dataset.filter;
        const rows = document.querySelectorAll('#transactionsBody tr');

        rows.forEach(row => {
            const status = row.dataset.status;

            if (filter === 'all') {
                row.style.display = '';
            } else if (filter === 'aprovadas' && status === '1') {
                row.style.display = '';
            } else if (filter === 'pendentes' && status === '0') {
                row.style.display = '';
            } else if (filter === 'falhas' && status !== '0' && status !== '1') {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
});

function viewTransaction(id) {
    alert('Ver detalhes da transação #' + id);
    // Implementar modal ou navegação para detalhes
}
@endsection
