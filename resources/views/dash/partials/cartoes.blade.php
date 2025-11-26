<div class="flex flex-col gap-2 mb-8">
    <p class="text-sm uppercase tracking-[0.35em] text-slate-500">Formas de Pagamento</p>
    <h1 class="text-3xl font-bold text-slate-900">Meus Cartões</h1>
    <p class="text-slate-500">Gerencie seus cartões de crédito e débito para pagamentos rápidos.</p>
</div>

@if(session('cartoes_success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> {{ session('cartoes_success') }}
    </div>
@endif

@if(session('cartoes_error'))
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i> {{ session('cartoes_error') }}
    </div>
@endif

<!-- Lista de Cartões -->
<div class="settings-card mb-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-xl font-semibold text-slate-900">Cartões Salvos</h2>
            <p class="text-sm text-slate-500">Você pode salvar até 3 cartões</p>
        </div>
        <button type="button" id="addCardBtn" class="btn-primary" style="width: auto; padding: 0.75rem 1.5rem;">
            <i class="fas fa-plus"></i> Adicionar Cartão
        </button>
    </div>

    <div id="savedCardsList" class="grid gap-4">
        @php
            $savedCards = $savedCards ?? [];
        @endphp

        @forelse($savedCards as $card)
            <div class="border border-slate-200 rounded-2xl p-4 hover:border-blue-300 transition-colors">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-slate-700 to-slate-900 flex items-center justify-center">
                            @if($card['brand'] === 'visa')
                                <i class="fab fa-cc-visa text-white text-2xl"></i>
                            @elseif($card['brand'] === 'mastercard')
                                <i class="fab fa-cc-mastercard text-white text-2xl"></i>
                            @elseif($card['brand'] === 'amex')
                                <i class="fab fa-cc-amex text-white text-2xl"></i>
                            @else
                                <i class="fas fa-credit-card text-white text-xl"></i>
                            @endif
                        </div>
                        <div>
                            <p class="font-semibold text-slate-900">•••• •••• •••• {{ $card['last4'] }}</p>
                            <p class="text-sm text-slate-500">Expira em {{ $card['exp_month'] }}/{{ $card['exp_year'] }}</p>
                            @if($card['is_default'])
                                <span class="badge badge-success mt-1">Padrão</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        @if(!$card['is_default'])
                            <button type="button" class="action-btn" onclick="setDefaultCard({{ $card['id'] }})">
                                <i class="fas fa-star text-xs"></i>
                                Tornar padrão
                            </button>
                        @endif
                        <button type="button" class="action-btn text-red-600 border-red-300 hover:border-red-500" onclick="deleteCard({{ $card['id'] }})">
                            <i class="fas fa-trash text-xs"></i>
                            Remover
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="border border-dashed border-slate-200 rounded-2xl p-10 text-center">
                <i class="fas fa-credit-card text-4xl text-slate-300 mb-4"></i>
                <p class="text-lg font-semibold text-slate-700 mb-2">Nenhum cartão cadastrado</p>
                <p class="text-sm text-slate-500">Adicione um cartão para pagamentos mais rápidos</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Modal de Adicionar Cartão -->
<div id="addCardModalOverlay" class="admin-modal-overlay"></div>
<div id="addCardModal" class="admin-modal" style="max-width: 600px;">
    <div class="flex justify-between items-start mb-6">
        <div>
            <h3 class="text-2xl font-bold text-slate-900">Adicionar Cartão</h3>
            <p class="text-sm text-slate-500">Preencha os dados do seu cartão</p>
        </div>
        <button type="button" class="text-slate-400 hover:text-slate-900" id="closeCardModal">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <form id="cardForm" method="POST" action="{{ route('cartoes.store') }}">
        @csrf

        <!-- Card Preview -->
        <div class="mb-6">
            <div id="card-wrapper"></div>
        </div>

        <!-- Formulário -->
        <div class="space-y-4">
            <div class="form-group">
                <label for="card-number" class="form-label">Número do Cartão</label>
                <input type="text"
                       id="card-number"
                       name="number"
                       class="form-input"
                       placeholder="0000 0000 0000 0000"
                       autocomplete="cc-number"
                       required>
            </div>

            <div class="form-group">
                <label for="card-name" class="form-label">Nome no Cartão</label>
                <input type="text"
                       id="card-name"
                       name="name"
                       class="form-input"
                       placeholder="NOME SOBRENOME"
                       autocomplete="cc-name"
                       required>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="form-group">
                    <label for="card-expiry" class="form-label">Validade (MM/AA)</label>
                    <input type="text"
                           id="card-expiry"
                           name="expiry"
                           class="form-input"
                           placeholder="MM/AA"
                           autocomplete="cc-exp"
                           required>
                </div>

                <div class="form-group">
                    <label for="card-cvc" class="form-label">CVV</label>
                    <input type="text"
                           id="card-cvc"
                           name="cvc"
                           class="form-input"
                           placeholder="123"
                           autocomplete="cc-csc"
                           required>
                </div>
            </div>

            <div class="form-group">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_default" id="is_default" class="w-4 h-4 text-blue-600 rounded">
                    <span class="text-sm font-semibold text-slate-700">Tornar este cartão padrão</span>
                </label>
            </div>
        </div>

        <div class="flex gap-3 mt-6">
            <button type="button" class="btn-secondary flex-1" id="cancelCardBtn">
                Cancelar
            </button>
            <button type="submit" class="btn-primary flex-1">
                <i class="fas fa-save"></i> Salvar Cartão
            </button>
        </div>
    </form>
</div>

<!-- Informações de Segurança -->
<div class="settings-card">
    <div class="flex items-start gap-4">
        <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0">
            <i class="fas fa-shield-alt text-green-600 text-xl"></i>
        </div>
        <div>
            <h3 class="font-semibold text-slate-900 mb-2">Seus dados estão seguros</h3>
            <p class="text-sm text-slate-600 mb-3">
                Utilizamos criptografia de ponta a ponta e seguimos os mais altos padrões de segurança PCI-DSS para proteger suas informações de pagamento.
            </p>
            <ul class="text-sm text-slate-600 space-y-1">
                <li><i class="fas fa-check-circle text-green-600 mr-2"></i>Criptografia SSL/TLS 256-bit</li>
                <li><i class="fas fa-check-circle text-green-600 mr-2"></i>Tokenização de dados do cartão</li>
                <li><i class="fas fa-check-circle text-green-600 mr-2"></i>Conformidade PCI-DSS Level 1</li>
            </ul>
        </div>
    </div>
</div>

<style>
#card-wrapper {
    height: 250px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.jp-card-container {
    margin: 0 auto !important;
}

.jp-card {
    min-width: 350px !important;
    transform: scale(0.95);
}

@media (max-width: 640px) {
    .jp-card {
        min-width: 280px !important;
        transform: scale(0.8);
    }
}
</style>

<script>
// Inicializar Card.js quando o modal abrir
let cardInstance = null;

document.addEventListener('DOMContentLoaded', function() {
    const addBtn = document.getElementById('addCardBtn');

    addBtn?.addEventListener('click', function() {
        // Aguardar o modal aparecer
        setTimeout(() => {
            if (!cardInstance) {
                cardInstance = new Card({
                    form: '#cardForm',
                    container: '#card-wrapper',
                    formSelectors: {
                        numberInput: '#card-number',
                        expiryInput: '#card-expiry',
                        cvcInput: '#card-cvc',
                        nameInput: '#card-name'
                    },
                    width: 350,
                    formatting: true,
                    messages: {
                        validDate: 'valido\nate',
                        monthYear: 'mm/aa',
                    },
                    placeholders: {
                        number: '•••• •••• •••• ••••',
                        name: 'Nome Completo',
                        expiry: '••/••',
                        cvc: '•••'
                    }
                });
            }
        }, 100);
    });
});

// Funções de gerenciamento de cartões
window.setDefaultCard = function(cardId) {
    if (confirm('Deseja tornar este cartão o padrão para pagamentos?')) {
        // Aqui você implementaria a chamada AJAX para definir como padrão
        fetch(`/cartoes/${cardId}/default`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erro ao definir cartão padrão');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao processar solicitação');
        });
    }
};

window.deleteCard = function(cardId) {
    if (confirm('Tem certeza que deseja remover este cartão?')) {
        fetch(`/cartoes/${cardId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erro ao remover cartão');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao processar solicitação');
        });
    }
};

// Modal de adicionar cartão
(() => {
    const overlay = document.getElementById('addCardModalOverlay');
    const modal = document.getElementById('addCardModal');
    const addBtn = document.getElementById('addCardBtn');
    const closeBtn = document.getElementById('closeCardModal');
    const cancelBtn = document.getElementById('cancelCardBtn');

    const openModal = () => {
        overlay.classList.add('active');
        modal.classList.add('active');
    };

    const closeModal = () => {
        overlay.classList.remove('active');
        modal.classList.remove('active');
    };

    addBtn?.addEventListener('click', openModal);
    closeBtn?.addEventListener('click', closeModal);
    cancelBtn?.addEventListener('click', closeModal);
    overlay?.addEventListener('click', closeModal);
})();
</script>
