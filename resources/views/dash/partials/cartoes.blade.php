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
                            @if($card->bandeira === 'visa')
                                <i class="fab fa-cc-visa text-white text-2xl"></i>
                            @elseif($card->bandeira === 'mastercard')
                                <i class="fab fa-cc-mastercard text-white text-2xl"></i>
                            @elseif($card->bandeira === 'amex')
                                <i class="fab fa-cc-amex text-white text-2xl"></i>
                            @else
                                <i class="fas fa-credit-card text-white text-xl"></i>
                            @endif
                        </div>
                        <div>
                            <p class="font-semibold text-slate-900">•••• •••• •••• {{ $card->ultimos_digitos }}</p>
                            <p class="text-sm text-slate-500">Expira em {{ $card->mes_expiracao }}/{{ $card->ano_expiracao }}</p>
                            @if($card->is_default)
                                <span class="badge badge-success mt-1">Padrão</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        @if(!$card->is_default)
                            <button type="button" class="action-btn" onclick="setDefaultCard({{ $card->id }})">
                                <i class="fas fa-star text-xs"></i>
                                Tornar padrão
                            </button>
                        @endif
                        <button type="button" class="action-btn text-red-600 border-red-300 hover:border-red-500" onclick="deleteCard({{ $card->id }})">
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

    <form id="cardForm">
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
                       name="card-number"
                       class="form-input"
                       placeholder="0000 0000 0000 0000"
                       autocomplete="off"
                       maxlength="19"
                       required>
            </div>

            <div class="form-group">
                <label for="card-name" class="form-label">Nome no Cartão</label>
                <input type="text"
                       id="card-name"
                       name="card-name"
                       class="form-input"
                       placeholder="NOME SOBRENOME"
                       autocomplete="off"
                       required>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="form-group">
                    <label for="card-expiry" class="form-label">Validade (MM/AA)</label>
                    <input type="text"
                           id="card-expiry"
                           name="card-expiry"
                           class="form-input"
                           placeholder="MM/AA"
                           autocomplete="off"
                           required>
                </div>

                <div class="form-group">
                    <label for="card-cvc" class="form-label">CVV</label>
                    <input type="text"
                           id="card-cvc"
                           name="card-cvc"
                           class="form-input"
                           placeholder="123"
                           autocomplete="off"
                           maxlength="4"
                           required>
                </div>
            </div>

            <div class="form-group">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_default" id="is_default" class="w-4 h-4 text-blue-600 rounded" @if(count($savedCards) == 0) checked @endif>
                    <span class="text-sm font-semibold text-slate-700">Tornar este cartão padrão</span>
                </label>
            </div>
        </div>

        <div class="flex gap-3 mt-6">
            <button type="button" class="btn-secondary flex-1" id="cancelCardBtn">
                Cancelar
            </button>
            <button type="submit" class="btn-primary flex-1" id="submitCardBtn">
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
                <li><i class="fas fa-check-circle text-green-600 mr-2"></i>Tokenização de dados do cartão via Aprovei</li>
                <li><i class="fas fa-check-circle text-green-600 mr-2"></i>Conformidade PCI-DSS Level 1</li>
            </ul>
        </div>
    </div>
</div>

<!-- Aprovei SDK -->
<script src="https://api.aproveipay.com.br/v1/js"></script>

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
let aproveiInitialized = false;

document.addEventListener('DOMContentLoaded', async function() {
    const addBtn = document.getElementById('addCardBtn');
    const cardForm = document.getElementById('cardForm');

    // Inicializar Aprovei SDK
    try {
        await Aprovei.setPublicKey("{{ config('services.aprovei.public_key') }}");
        aproveiInitialized = true;
    } catch (error) {
        console.error('Erro ao inicializar Aprovei SDK:', error);
        alert('Erro ao inicializar sistema de pagamento. Tente novamente mais tarde.');
    }

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

    // Submissão do formulário com tokenização
    cardForm?.addEventListener('submit', async function(e) {
        e.preventDefault();

        if (!aproveiInitialized) {
            alert('Sistema de pagamento não está pronto. Tente novamente.');
            return;
        }

        const submitBtn = document.getElementById('submitCardBtn');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processando...';

        try {
            // Obter dados do formulário
            const cardNumber = document.getElementById('card-number').value.replace(/\s/g, '');
            const holderName = document.getElementById('card-name').value;
            const expiry = document.getElementById('card-expiry').value;
            const cvv = document.getElementById('card-cvc').value;
            const isDefault = document.getElementById('is_default').checked;

            // Processar validade
            const [expMonth, expYear] = expiry.split('/');
            const fullYear = expYear.length === 2 ? `20${expYear}` : expYear;

            // Validação básica
            if (!cardNumber || cardNumber.length < 13) {
                throw new Error('Número do cartão inválido');
            }

            if (!expMonth || !expYear || parseInt(expMonth) < 1 || parseInt(expMonth) > 12) {
                throw new Error('Data de validade inválida');
            }

            if (!cvv || cvv.length < 3) {
                throw new Error('CVV inválido');
            }

            // Tokenizar com Aprovei
            const cardData = {
                number: cardNumber,
                holderName: holderName,
                expMonth: parseInt(expMonth),
                expYear: parseInt(fullYear),
                cvv: cvv
            };

            console.log('Tokenizando cartão...', { holderName, expMonth, expYear: fullYear });
            const token = await Aprovei.encrypt(cardData);
            console.log('Token gerado com sucesso');

            // Identificar bandeira
            const brand = identifyCardBrand(cardNumber);
            const last4 = cardNumber.slice(-4);

            // Enviar ao servidor
            const response = await fetch('{{ route("cartoes.store") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    card_token: token,
                    last4: last4,
                    brand: brand,
                    exp_month: parseInt(expMonth),
                    exp_year: parseInt(fullYear),
                    holder_name: holderName,
                    is_default: isDefault
                })
            });

            const data = await response.json();

            if (data.success) {
                // Redirecionar ou recarregar
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    location.reload();
                }
            } else {
                alert(data.message || 'Erro ao salvar cartão');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }

        } catch (error) {
            console.error('Erro ao processar cartão:', error);
            alert(error.message || 'Erro ao processar cartão. Verifique os dados e tente novamente.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });
});

// Identificar bandeira do cartão
function identifyCardBrand(number) {
    const digit1 = number[0];
    const digit2 = number.substring(0, 2);
    const digit4 = number.substring(0, 4);

    if (digit1 === '4') return 'visa';
    if (['51', '52', '53', '54', '55'].includes(digit2) || (digit4 >= '2221' && digit4 <= '2720')) return 'mastercard';
    if (['34', '37'].includes(digit2)) return 'amex';
    if (['6011', '6221', '6222', '6223', '6224', '6225', '6226', '6227', '6228', '6229'].includes(digit4) || digit2 === '65') return 'discover';
    if (['4011', '4312', '4389', '4514', '4573', '5041', '5066', '5067'].includes(digit4)) return 'elo';
    if (digit4 === '6062' || digit4 === '3841') return 'hipercard';
    if (['36', '38'].includes(digit2)) return 'diners';

    return 'unknown';
}

// Funções de gerenciamento de cartões
window.setDefaultCard = function(cardId) {
    if (confirm('Deseja tornar este cartão o padrão para pagamentos?')) {
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
        // Resetar formulário
        document.getElementById('cardForm').reset();
    };

    addBtn?.addEventListener('click', openModal);
    closeBtn?.addEventListener('click', closeModal);
    cancelBtn?.addEventListener('click', closeModal);
    overlay?.addEventListener('click', closeModal);
})();
</script>
