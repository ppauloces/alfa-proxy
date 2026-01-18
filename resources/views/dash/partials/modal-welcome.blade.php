{{-- Modal de Boas-vindas para coletar CPF e Telefone --}}
@if(empty($usuario->cpf) || empty($usuario->phone))
<div id="modalWelcome" class="modal-overlay-welcome">
    <div class="modal-welcome-content">
        {{-- Etapa 1: Coleta de dados --}}
        <div id="welcomeStep1">
            <div class="welcome-icon">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
            </div>

            <h2 class="welcome-title">Bem-vindo, {{ explode(' ', $usuario->name)[0] }}!</h2>
            <p class="welcome-subtitle">Para uma melhor experiencia, precisamos de algumas informacoes.</p>

            <form id="formWelcome" class="welcome-form" autocomplete="off">
                @csrf
                <div class="form-group-welcome">
                    <label for="welcome_cpf">CPF ou CNPJ</label>
                    <input
                        type="text"
                        id="welcome_cpf"
                        name="welcome_cpf_field"
                        placeholder="000.000.000-00"
                        value="{{ $usuario->cpf ?? '' }}"
                        class="input-welcome"
                        autocomplete="off"
                        autocorrect="off"
                        autocapitalize="off"
                        spellcheck="false"
                        required
                    >
                    <small class="input-hint">Necessario para pagamentos via PIX</small>
                </div>

                <div class="form-group-welcome">
                    <label for="welcome_phone">Telefone (WhatsApp)</label>
                    <input
                        type="text"
                        id="welcome_phone"
                        name="welcome_phone_field"
                        placeholder="(00) 00000-0000"
                        value="{{ $usuario->phone ?? '' }}"
                        class="input-welcome"
                        autocomplete="off"
                        autocorrect="off"
                        autocapitalize="off"
                        spellcheck="false"
                        required
                    >
                    <small class="input-hint">Para suporte e notificacoes</small>
                </div>

                <button type="submit" class="btn-welcome-submit">
                    <span>Salvar e Continuar</span>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </button>
            </form>

            <p class="welcome-privacy">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
                Seus dados estao seguros e nao serao compartilhados.
            </p>
        </div>

        {{-- Etapa 2: Confirmacao --}}
        <div id="welcomeStep2" style="display: none;">
            <div class="welcome-icon success">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>

            <h2 class="welcome-title">Tudo pronto!</h2>
            <p class="welcome-subtitle">Seus dados foram salvos com sucesso.</p>

            <div class="welcome-cta-box">
                <p class="cta-text">Agora voce ja pode aproveitar todos os recursos da plataforma!</p>

                <a href="#" onclick="abrirSecaoCompra(); return false;" class="btn-welcome-cta">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                    </svg>
                    <span>Comprar Proxies Agora</span>
                </a>

                <button type="button" onclick="fecharModalWelcome()" class="btn-welcome-later">
                    Explorar o Painel
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .modal-overlay-welcome {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(15, 23, 42, 0.7);
        backdrop-filter: blur(8px);
        z-index: 99999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        margin: 0;
        box-sizing: border-box;
        animation: fadeInWelcome 0.3s ease;
    }

    @keyframes fadeInWelcome {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .modal-welcome-content {
        background: #fff;
        border-radius: 24px;
        padding: 2.5rem;
        max-width: 420px;
        width: 100%;
        box-shadow: 0 25px 80px rgba(15, 23, 42, 0.3);
        animation: slideUpWelcome 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        position: relative;
    }

    @keyframes slideUpWelcome {
        from {
            opacity: 0;
            transform: translateY(30px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .welcome-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, #2055DD, #1a44b8);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        color: #fff;
    }

    .welcome-icon.success {
        background: linear-gradient(135deg, #10b981, #059669);
    }

    .welcome-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #0f172a;
        text-align: center;
        margin-bottom: 0.5rem;
    }

    .welcome-subtitle {
        font-size: 0.95rem;
        color: #64748b;
        text-align: center;
        margin-bottom: 1.75rem;
    }

    .welcome-form {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }

    .form-group-welcome {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .form-group-welcome label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #0f172a;
    }

    .input-welcome {
        padding: 0.875rem 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        font-size: 1rem;
        transition: all 0.2s ease;
        background: #f8fafc;
    }

    .input-welcome:focus {
        outline: none;
        border-color: #2055DD;
        background: #fff;
        box-shadow: 0 0 0 4px rgba(32, 85, 221, 0.1);
    }

    .input-hint {
        font-size: 0.75rem;
        color: #94a3b8;
    }

    .btn-welcome-submit {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 1rem;
        background: linear-gradient(135deg, #2055DD, #1a44b8);
        color: #fff;
        border: none;
        border-radius: 12px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        margin-top: 0.5rem;
    }

    .btn-welcome-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(32, 85, 221, 0.3);
    }

    .btn-welcome-submit:disabled {
        opacity: 0.7;
        cursor: not-allowed;
        transform: none;
    }

    .welcome-privacy {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        font-size: 0.75rem;
        color: #94a3b8;
        margin-top: 1.5rem;
        text-align: center;
    }

    .welcome-cta-box {
        background: linear-gradient(135deg, rgba(32, 85, 221, 0.05), rgba(32, 85, 221, 0.1));
        border-radius: 16px;
        padding: 1.5rem;
        text-align: center;
    }

    .cta-text {
        font-size: 0.9rem;
        color: #475569;
        margin-bottom: 1.25rem;
    }

    .btn-welcome-cta {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        width: 100%;
        padding: 1rem;
        background: linear-gradient(135deg, #10b981, #059669);
        color: #fff;
        border: none;
        border-radius: 12px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-welcome-cta:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(16, 185, 129, 0.3);
        color: #fff;
    }

    .btn-welcome-later {
        display: block;
        width: 100%;
        padding: 0.875rem;
        background: transparent;
        color: #64748b;
        border: none;
        font-size: 0.9rem;
        font-weight: 500;
        cursor: pointer;
        margin-top: 0.75rem;
        transition: color 0.2s ease;
    }

    .btn-welcome-later:hover {
        color: #0f172a;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mascara para CPF/CNPJ
        const cpfInput = document.getElementById('welcome_cpf');
        if (cpfInput) {
            cpfInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');

                if (value.length <= 11) {
                    // CPF: 000.000.000-00
                    value = value.replace(/(\d{3})(\d)/, '$1.$2');
                    value = value.replace(/(\d{3})(\d)/, '$1.$2');
                    value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
                } else {
                    // CNPJ: 00.000.000/0001-00
                    value = value.replace(/(\d{2})(\d)/, '$1.$2');
                    value = value.replace(/(\d{3})(\d)/, '$1.$2');
                    value = value.replace(/(\d{3})(\d)/, '$1/$2');
                    value = value.replace(/(\d{4})(\d{1,2})$/, '$1-$2');
                }

                e.target.value = value;
            });
        }

        // Mascara para telefone
        const phoneInput = document.getElementById('welcome_phone');
        if (phoneInput) {
            phoneInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');

                if (value.length <= 10) {
                    // Telefone fixo: (00) 0000-0000
                    value = value.replace(/(\d{2})(\d)/, '($1) $2');
                    value = value.replace(/(\d{4})(\d{1,4})$/, '$1-$2');
                } else {
                    // Celular: (00) 00000-0000
                    value = value.replace(/(\d{2})(\d)/, '($1) $2');
                    value = value.replace(/(\d{5})(\d{1,4})$/, '$1-$2');
                }

                e.target.value = value;
            });
        }

        // Submit do formulario
        const formWelcome = document.getElementById('formWelcome');
        if (formWelcome) {
            formWelcome.addEventListener('submit', async function(e) {
                e.preventDefault();

                const btn = formWelcome.querySelector('button[type="submit"]');
                const originalText = btn.innerHTML;
                btn.innerHTML = '<span>Salvando...</span>';
                btn.disabled = true;

                const cpf = document.getElementById('welcome_cpf').value;
                const phone = document.getElementById('welcome_phone').value;

                try {
                    const response = await fetch('{{ route("perfil.salvar-dados") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ cpf, phone })
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        // Mostrar etapa 2 (confirmacao)
                        document.getElementById('welcomeStep1').style.display = 'none';
                        document.getElementById('welcomeStep2').style.display = 'block';
                    } else {
                        alert(data.error || 'Erro ao salvar dados. Tente novamente.');
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                    }
                } catch (error) {
                    console.error('Erro:', error);
                    alert('Erro ao conectar com o servidor. Tente novamente.');
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            });
        }
    });

    function fecharModalWelcome() {
        const modal = document.getElementById('modalWelcome');
        if (modal) {
            modal.style.opacity = '0';
            modal.style.transition = 'opacity 0.3s ease';
            setTimeout(() => {
                modal.style.display = 'none';
            }, 300);
        }
    }

    function abrirSecaoCompra() {
        fecharModalWelcome();
        // Navegar para a secao de nova compra
        setTimeout(() => {
            const tabBtn = document.querySelector('[data-section-link="nova-compra"]');
            if (tabBtn) {
                tabBtn.click();
            } else {
                window.location.href = '{{ route("dash.show") }}?section=nova-compra';
            }
        }, 350);
    }
</script>
@endif
