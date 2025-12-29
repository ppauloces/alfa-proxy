<div class="flex flex-col gap-6">
    {{-- Header da Seção --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div class="space-y-1">
            <p class="text-[10px] font-bold text-[#448ccb] uppercase tracking-[0.3em]">Formas de Pagamento</p>
            <h1 class="text-4xl font-black text-slate-900 tracking-tight">Meus <span class="text-[#23366f]">Cartões</span></h1>
            <p class="text-slate-500 font-medium max-w-xl">Gerencie seus cartões de crédito e débito para pagamentos rápidos.</p>
        </div>

        <button type="button" id="addCardBtn" class="px-6 py-3 rounded-2xl bg-[#23366f] text-white text-sm font-bold shadow-lg shadow-blue-900/20 hover:scale-[1.02] transition-all flex items-center gap-2">
            <i class="fas fa-plus"></i> Adicionar Cartão
        </button>
    </div>

    @if(session('cartoes_success'))
        <div class="alert alert-success bg-green-50 text-green-700 border-green-100 rounded-2xl p-4 font-semibold flex items-center gap-3">
            <i class="fas fa-check-circle"></i> {{ session('cartoes_success') }}
        </div>
    @endif

    @if(session('cartoes_error'))
        <div class="alert alert-error bg-red-50 text-red-700 border-red-100 rounded-2xl p-4 font-semibold flex items-center gap-3">
            <i class="fas fa-exclamation-circle"></i> {{ session('cartoes_error') }}
        </div>
    @endif

    <!-- Lista de Cartões -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @php
            $savedCards = $savedCards ?? [];
        @endphp

        @forelse($savedCards as $card)
            <div class="group relative bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm hover:shadow-xl hover:shadow-blue-900/5 transition-all overflow-hidden">
                <div class="relative z-10 flex flex-col h-full">
                    <div class="flex justify-between items-start mb-10">
                        <div class="w-14 h-10 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-2xl">
                            @if($card->bandeira === 'visa')
                                <i class="fab fa-cc-visa text-[#1a1f71]"></i>
                            @elseif($card->bandeira === 'mastercard')
                                <i class="fab fa-cc-mastercard text-[#eb001b]"></i>
                            @elseif($card->bandeira === 'amex')
                                <i class="fab fa-cc-amex text-[#2e77bb]"></i>
                            @else
                                <i class="fas fa-credit-card text-slate-400"></i>
                            @endif
                        </div>
                        @if($card->is_default)
                            <span class="px-3 py-1 rounded-lg bg-green-50 text-green-600 text-[10px] font-black uppercase tracking-widest">Padrão</span>
                        @endif
                    </div>

                    <div class="space-y-4 mb-8">
                        <p class="text-xl font-black text-slate-900 tracking-[0.15em]">•••• •••• •••• {{ $card->ultimos_digitos }}</p>
                        <div class="flex items-center gap-6">
                            <div>
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1">Expiração</p>
                                <p class="text-sm font-bold text-slate-700">{{ str_pad($card->mes_expiracao, 2, '0', STR_PAD_LEFT) }}/{{ substr($card->ano_expiracao, -2) }}</p>
                            </div>
                            @if(isset($card->cpf) && $card->cpf)
                                <div>
                                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1">Documento</p>
                                    <p class="text-sm font-bold text-slate-700">{{ $card->masked_cpf ?? '***.***.***-**' }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-auto flex items-center gap-2 pt-6 border-t border-slate-50">
                        @if(!$card->is_default)
                            <button type="button" class="flex-1 px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest text-[#23366f] hover:bg-blue-50 transition-all" onclick="setDefaultCard({{ $card->id }})">
                                <i class="fas fa-star mr-1"></i> Padrão
                            </button>
                        @endif
                        <button type="button" class="flex-1 px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest text-red-500 hover:bg-red-50 transition-all" onclick="deleteCard({{ $card->id }})">
                            <i class="fas fa-trash mr-1"></i> Remover
                        </button>
                    </div>
                </div>

                {{-- Elemento decorativo --}}
                <div class="absolute -bottom-10 -right-10 w-32 h-32 bg-slate-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-700"></div>
            </div>
        @empty
            <div class="md:col-span-2 xl:col-span-3 bg-white border-2 border-dashed border-slate-100 rounded-[3rem] p-20 text-center">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-credit-card text-3xl text-slate-200"></i>
                </div>
                <h3 class="text-xl font-black text-slate-900 mb-2">Nenhum cartão cadastrado</h3>
                <p class="text-slate-400 text-sm font-medium mb-8">Adicione um cartão para realizar suas compras com um clique.</p>
                <button type="button" onclick="document.getElementById('addCardBtn').click()" class="inline-flex items-center gap-3 px-8 py-4 rounded-2xl bg-[#23366f] text-white font-bold hover:scale-105 transition-all">
                    Adicionar Primeiro Cartão
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        @endforelse
    </div>

    <!-- Informações de Segurança -->
    <div class="bg-[#23366f]/5 border border-[#23366f]/10 rounded-[2.5rem] p-10 mt-6">
        <div class="flex flex-col md:flex-row items-center gap-10">
            <div class="w-24 h-24 rounded-3xl bg-white flex items-center justify-center text-[#23366f] text-4xl shadow-xl shadow-blue-900/5">
            <i class="fas fa-shield-alt"></i>
            </div>
            <div class="flex-1 text-center md:text-left">
                <h3 class="text-xl font-black text-[#23366f] mb-3"><i class="fas fa-lock"></i> Seus dados estão 100% seguros</h3>
                <p class="text-slate-500 font-medium leading-relaxed mb-6">
                    Utilizamos tecnologia de tokenização de ponta. Seus dados sensíveis nunca tocam nossos servidores, sendo processados com criptografia bancária PCI-DSS Level 1.
                </p>
                <div class="flex flex-wrap justify-center md:justify-start gap-6">
                    <div class="flex items-center gap-2 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                    <i class="fa-brands fa-expeditedssl text-green-500 text-xs"></i> SSL 256-bit
                    </div>
                    <div class="flex items-center gap-2 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                        <i class="fas fa-check-circle text-green-500"></i> Tokenização AES
                    </div>
                    <div class="flex items-center gap-2 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                        <i class="fas fa-user-shield text-green-500"></i> PCI-DSS Compliance
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Adicionar Cartão -->
<div id="addCardModalOverlay" class="admin-modal-overlay">
    <div id="addCardModal" class="admin-modal card-modal" style="max-width: 600px;">
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

                <div class="form-group">
                    <label for="card-cpf" class="form-label">CPF do Titular</label>
                    <input type="text"
                           id="card-cpf"
                           name="card-cpf"
                           class="form-input"
                           placeholder="000.000.000-00"
                           autocomplete="off"
                           maxlength="14"
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
                        <input type="checkbox" name="is_default" id="is_default" class="w-4 h-4 text-[#23366f] rounded" @if(count($savedCards) == 0) checked @endif>
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

    // Máscara de CPF
    const cpfInput = document.getElementById('card-cpf');
    cpfInput?.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 11) value = value.slice(0, 11);

        // Aplicar máscara: 000.000.000-00
        if (value.length > 9) {
            e.target.value = value.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
        } else if (value.length > 6) {
            e.target.value = value.replace(/(\d{3})(\d{3})(\d{1,3})/, '$1.$2.$3');
        } else if (value.length > 3) {
            e.target.value = value.replace(/(\d{3})(\d{1,3})/, '$1.$2');
        } else {
            e.target.value = value;
        }
    });

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
            const cpf = document.getElementById('card-cpf').value.replace(/\D/g, '');
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

            if (!cpf || cpf.length !== 11) {
                throw new Error('CPF inválido');
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
                    cpf: cpf,
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
