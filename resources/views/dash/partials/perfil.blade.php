<div class="flex flex-col gap-2 mb-8">
    <p class="text-sm uppercase tracking-[0.35em] text-slate-500">Configuracoes de conta</p>
    <h1 class="text-3xl font-bold text-slate-900">Meu Perfil</h1>
    <p class="text-slate-500">Gerencie suas informacoes pessoais e configuracoes de seguranca.</p>
</div>

@if(session('perfil_success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> {{ session('perfil_success') }}
    </div>
@endif

<div class="grid gap-6">
    <!-- Informações do Perfil -->
    <div class="profile-card">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-[#4F8BFF] to-[#2055dd] flex items-center justify-center text-white font-semibold text-2xl">
                {{ strtoupper(substr($usuario->name ?? 'U', 0, 2)) }}
            </div>
            <div>
                <h2 class="text-xl font-semibold text-slate-900">{{ $usuario->name ?? 'Usuario' }}</h2>
                <p class="text-sm text-slate-500">{{ $usuario->email ?? 'email@exemplo.com' }}</p>
            </div>
        </div>

        @if($errors->perfil->any())
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <div>
                    @foreach($errors->perfil->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        @endif

        <form action="{{ route('perfil.atualizar') }}" method="POST" id="perfilForm">
            @csrf
            <div class="grid md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label">Nome Completo</label>
                    <input type="text" name="name" value="{{ old('name', $usuario->name ?? '') }}" class="form-input" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" id="perfil_username" value="{{ old('username', $usuario->username ?? '') }}" class="form-input" minlength="3" maxlength="30" required>
                    <small class="perfil-hint" id="username_hint">Apenas letras, números e _</small>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">E-mail</label>
                <input type="email" value="{{ $usuario->email ?? '' }}" class="form-input" disabled>
                <small class="perfil-hint">O e-mail não pode ser alterado.</small>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label">CPF ou CNPJ</label>
                    <input type="text" name="cpf" id="perfil_cpf" value="{{ old('cpf', $usuario->cpf ? preg_replace(['/(\d{3})(\d{3})(\d{3})(\d{2})/', '/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/'], ['$1.$2.$3-$4', '$1.$2.$3/$4-$5'], $usuario->cpf) : '') }}" class="form-input" maxlength="18" required>
                    <small class="perfil-hint" id="cpf_perfil_hint">Necessário para pagamentos via PIX</small>
                </div>

                <div class="form-group">
                    <label class="form-label">Telefone (WhatsApp)</label>
                    @php
                        $phoneFormatted = '';
                        if (!empty($usuario->phone)) {
                            $p = $usuario->phone;
                            if (strlen($p) === 11) {
                                $phoneFormatted = '(' . substr($p,0,2) . ') ' . substr($p,2,5) . '-' . substr($p,7);
                            } elseif (strlen($p) === 10) {
                                $phoneFormatted = '(' . substr($p,0,2) . ') ' . substr($p,2,4) . '-' . substr($p,6);
                            } else {
                                $phoneFormatted = $p;
                            }
                        }
                    @endphp
                    <input type="text" name="phone" id="perfil_phone" value="{{ old('phone', $phoneFormatted) }}" class="form-input" placeholder="(00) 00000-0000" maxlength="15" required>
                    <small class="perfil-hint">Para suporte e notificações</small>
                </div>
            </div>

            <div class="flex gap-3 mt-2">
                <button type="submit" class="btn-primary" id="perfilSubmitBtn">
                    <i class="fas fa-save"></i> Salvar Alteracoes
                </button>
            </div>
        </form>
    </div>

    <!-- Alterar Senha -->
    <div class="profile-card">
        <h2 class="text-xl font-semibold text-slate-900 mb-4">Alterar Senha</h2>
        <p class="text-sm text-slate-500 mb-6">Atualize sua senha para manter sua conta segura.</p>

        @if($errors->alterarSenha->any())
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                @foreach($errors->alterarSenha->all() as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif

        <form action="{{ route('perfil.senha') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Senha Atual</label>
                <input type="password" name="senha_atual" class="form-input" required>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label">Nova Senha</label>
                    <input type="password" name="nova_senha" class="form-input" minlength="6" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Confirmar Nova Senha</label>
                    <input type="password" name="nova_senha_confirmation" class="form-input" minlength="6" required>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-key"></i> Alterar Senha
                </button>
            </div>
        </form>
    </div>

    <!-- Informações da Conta -->
    <div class="profile-card">
        <h2 class="text-xl font-semibold text-slate-900 mb-4">Informacoes da Conta</h2>
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-slate-500">Plano Atual</p>
                <p class="text-lg font-semibold text-slate-900">{{ $usuario->plano ?? 'Grátis' }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-500">Cargo</p>
                <p class="text-lg font-semibold text-slate-900">{{ ucfirst($usuario->cargo ?? 'usuario') }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-500">Saldo Disponivel</p>
                <p class="text-lg font-semibold text-slate-900">R$ {{ number_format($usuario->saldo ?? 0, 2, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-500">Status da Conta</p>
                <p class="text-lg font-semibold {{ $usuario->status ? 'text-green-600' : 'text-red-600' }}">
                    {{ $usuario->status ? 'Ativa' : 'Inativa' }}
                </p>
            </div>
        </div>
    </div>
</div>

<style>
    .perfil-hint {
        display: block;
        font-size: 0.75rem;
        color: #94a3b8;
        margin-top: 0.35rem;
    }
    .perfil-hint.hint-valid {
        color: #10b981;
        font-weight: 600;
    }
    .perfil-hint.hint-invalid {
        color: #ef4444;
        font-weight: 600;
    }
    .form-input.input-valid {
        border-color: #10b981 !important;
        background: #f0fdf4;
    }
    .form-input.input-invalid {
        border-color: #ef4444 !important;
        background: #fef2f2;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ==========================================
    // Validação CPF/CNPJ (reusar funções globais se existirem)
    // ==========================================
    function _validarCPF(cpf) {
        cpf = cpf.replace(/\D/g, '');
        if (cpf.length !== 11) return false;
        if (/^(\d)\1{10}$/.test(cpf)) return false;
        for (let t = 9; t < 11; t++) {
            let sum = 0;
            for (let i = 0; i < t; i++) {
                sum += parseInt(cpf[i]) * ((t + 1) - i);
            }
            let digit = ((10 * sum) % 11) % 10;
            if (parseInt(cpf[t]) !== digit) return false;
        }
        return true;
    }

    function _validarCNPJ(cnpj) {
        cnpj = cnpj.replace(/\D/g, '');
        if (cnpj.length !== 14) return false;
        if (/^(\d)\1{13}$/.test(cnpj)) return false;
        const w1 = [5,4,3,2,9,8,7,6,5,4,3,2];
        const w2 = [6,5,4,3,2,9,8,7,6,5,4,3,2];
        let sum = 0;
        for (let i = 0; i < 12; i++) sum += parseInt(cnpj[i]) * w1[i];
        let d1 = sum % 11 < 2 ? 0 : 11 - (sum % 11);
        if (parseInt(cnpj[12]) !== d1) return false;
        sum = 0;
        for (let i = 0; i < 13; i++) sum += parseInt(cnpj[i]) * w2[i];
        let d2 = sum % 11 < 2 ? 0 : 11 - (sum % 11);
        if (parseInt(cnpj[13]) !== d2) return false;
        return true;
    }

    // ==========================================
    // CPF/CNPJ - Máscara + validação em tempo real
    // ==========================================
    const cpfInput = document.getElementById('perfil_cpf');
    const cpfHint = document.getElementById('cpf_perfil_hint');
    let cpfValido = false;

    // Validação silenciosa (sem alterar UI) — usada no carregamento inicial
    function _checarCpfSilencioso(valor) {
        const nums = valor.replace(/\D/g, '');
        if (nums.length === 11) return _validarCPF(nums);
        if (nums.length === 14) return _validarCNPJ(nums);
        return false;
    }

    if (cpfInput) {
        const atualizarCpf = (mostrarUI = true) => {
            const nums = cpfInput.value.replace(/\D/g, '');

            if (!mostrarUI) {
                // Apenas atualizar o estado sem alterar visual
                cpfValido = _checarCpfSilencioso(cpfInput.value);
                return;
            }

            cpfInput.classList.remove('input-valid', 'input-invalid');
            cpfHint.classList.remove('hint-valid', 'hint-invalid');

            if (nums.length === 0) {
                cpfHint.textContent = 'Necessário para pagamentos via PIX';
                cpfValido = false;
                return;
            }

            if (nums.length === 11) {
                cpfValido = _validarCPF(nums);
                cpfInput.classList.add(cpfValido ? 'input-valid' : 'input-invalid');
                cpfHint.classList.add(cpfValido ? 'hint-valid' : 'hint-invalid');
                cpfHint.textContent = cpfValido ? 'CPF válido' : 'CPF inválido — verifique os dígitos';
            } else if (nums.length === 14) {
                cpfValido = _validarCNPJ(nums);
                cpfInput.classList.add(cpfValido ? 'input-valid' : 'input-invalid');
                cpfHint.classList.add(cpfValido ? 'hint-valid' : 'hint-invalid');
                cpfHint.textContent = cpfValido ? 'CNPJ válido' : 'CNPJ inválido — verifique os dígitos';
            } else {
                const tipo = nums.length > 11 ? 'CNPJ' : 'CPF';
                cpfHint.textContent = `Digitando ${tipo}...`;
                cpfValido = false;
            }
        };

        cpfInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 11) {
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            } else {
                value = value.substring(0, 14);
                value = value.replace(/(\d{2})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d)/, '$1/$2');
                value = value.replace(/(\d{4})(\d{1,2})$/, '$1-$2');
            }
            e.target.value = value;
            atualizarCpf(true);
        });

        // No carregamento: validar silenciosamente (sem feedback visual)
        // O usuário só vê verde/vermelho quando interagir com o campo
        if (cpfInput.value) atualizarCpf(false);
    }

    // ==========================================
    // Telefone - Máscara
    // ==========================================
    const phoneInput = document.getElementById('perfil_phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 10) {
                value = value.replace(/(\d{2})(\d)/, '($1) $2');
                value = value.replace(/(\d{4})(\d{1,4})$/, '$1-$2');
            } else {
                value = value.replace(/(\d{2})(\d)/, '($1) $2');
                value = value.replace(/(\d{5})(\d{1,4})$/, '$1-$2');
            }
            e.target.value = value;
        });
    }

    // ==========================================
    // Username - Verificação de disponibilidade
    // ==========================================
    const usernameInput = document.getElementById('perfil_username');
    const usernameHint = document.getElementById('username_hint');
    let usernameValido = true; // começa válido (valor atual do user)
    let usernameTimer = null;
    const usernameOriginal = usernameInput ? usernameInput.value : '';

    if (usernameInput) {
        usernameInput.addEventListener('input', function() {
            const val = usernameInput.value.trim();

            usernameInput.classList.remove('input-valid', 'input-invalid');
            usernameHint.classList.remove('hint-valid', 'hint-invalid');

            if (val.length < 3) {
                usernameHint.textContent = 'Mínimo 3 caracteres';
                usernameHint.classList.add('hint-invalid');
                usernameValido = false;
                return;
            }

            if (!/^[a-zA-Z0-9_]+$/.test(val)) {
                usernameHint.textContent = 'Apenas letras, números e _';
                usernameHint.classList.add('hint-invalid');
                usernameInput.classList.add('input-invalid');
                usernameValido = false;
                return;
            }

            // Se é o mesmo que já tem, não precisa checar
            if (val === usernameOriginal) {
                usernameHint.textContent = 'Seu username atual';
                usernameValido = true;
                return;
            }

            usernameHint.textContent = 'Verificando...';
            usernameValido = false;

            clearTimeout(usernameTimer);
            usernameTimer = setTimeout(async () => {
                try {
                    const res = await fetch('{{ route("perfil.checar-username") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ username: val })
                    });
                    const data = await res.json();

                    // Só atualizar se o valor ainda for o mesmo
                    if (usernameInput.value.trim() !== val) return;

                    usernameInput.classList.remove('input-valid', 'input-invalid');
                    usernameHint.classList.remove('hint-valid', 'hint-invalid');

                    if (data.available) {
                        usernameInput.classList.add('input-valid');
                        usernameHint.classList.add('hint-valid');
                        usernameHint.textContent = 'Username disponível';
                        usernameValido = true;
                    } else {
                        usernameInput.classList.add('input-invalid');
                        usernameHint.classList.add('hint-invalid');
                        usernameHint.textContent = data.message || 'Username já está em uso';
                        usernameValido = false;
                    }
                } catch (err) {
                    usernameHint.textContent = 'Erro ao verificar';
                }
            }, 400);
        });
    }

    // ==========================================
    // Validação no submit
    // ==========================================
    const perfilForm = document.getElementById('perfilForm');
    if (perfilForm) {
        perfilForm.addEventListener('submit', function(e) {
            const errors = [];

            if (!usernameValido) {
                errors.push('Username inválido ou já em uso.');
                usernameInput?.focus();
            }

            if (!cpfValido) {
                errors.push('CPF/CNPJ inválido.');
                if (!errors.length || errors.length === 1) cpfInput?.focus();
            }

            if (errors.length) {
                e.preventDefault();
                alert(errors.join('\n'));
            }
        });
    }
});
</script>
