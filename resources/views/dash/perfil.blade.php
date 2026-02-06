@extends('dash.layout')

@section('title', 'AlfaProxy - Meu Perfil')

@section('styles')
<style>
.profile-card {
    background: #fff;
    border-radius: 28px;
    border: 1px solid rgba(226,232,240,0.9);
    padding: 2rem;
    box-shadow: 0 20px 60px rgba(15,23,42,0.08);
}
.form-group {
    margin-bottom: 1.5rem;
}
.form-label {
    display: block;
    font-size: 0.875rem;
    font-weight: 600;
    color: #475569;
    margin-bottom: 0.5rem;
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
.form-input:disabled {
    background: rgba(148,163,184,0.1);
    cursor: not-allowed;
}
.btn-primary {
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    background: linear-gradient(120deg, var(--sf-blue-light), var(--sf-blue));
    color: #fff;
    font-weight: 600;
    font-size: 0.9375rem;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
}
.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 30px rgba(32,85,221,0.3);
}
.btn-secondary {
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    background: rgba(148,163,184,0.15);
    color: #475569;
    font-weight: 600;
    font-size: 0.9375rem;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
}
.btn-secondary:hover {
    background: rgba(148,163,184,0.25);
}
.alert {
    padding: 1rem 1.25rem;
    border-radius: 12px;
    margin-bottom: 1.5rem;
    font-size: 0.9375rem;
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
</style>
@endsection

@section('content')
<div class="flex flex-col gap-6">
    {{-- Header da Seção --}}
    <div class="space-y-1">
        <p class="text-[10px] font-bold text-[#448ccb] uppercase tracking-[0.3em]">Configuracoes de conta</p>
        <h1 class="text-4xl font-black text-slate-900 tracking-tight">Meu <span class="text-[#23366f]">Perfil</span></h1>
        <p class="text-slate-500 font-medium max-w-xl">Gerencie suas informacoes pessoais e configuracoes de seguranca.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success bg-green-50 text-green-700 border-green-100 rounded-2xl p-4 font-semibold flex items-center gap-3">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-error bg-red-50 text-red-700 border-red-100 rounded-2xl p-4 font-semibold flex items-center gap-3">
            <i class="fas fa-exclamation-circle"></i>
            <div>
                @foreach($errors->all() as $error)
                    <p class="text-sm">{{ $error }}</p>
                @endforeach
            </div>
        </div>
    @endif

    <div class="grid lg:grid-cols-3 gap-8">
        <!-- Coluna da Esquerda: Info Principal -->
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
                <div class="flex items-center gap-6 mb-10 pb-10 border-b border-slate-50">
                    <div class="relative">
                        <div class="w-20 h-20 rounded-[2rem] bg-gradient-to-br from-[#448ccb] to-[#23366f] flex items-center justify-center text-white text-3xl font-black shadow-xl shadow-blue-900/20">
                            {{ strtoupper(substr($usuario->name ?? 'U', 0, 1)) }}{{ strtoupper(substr(explode(' ', $usuario->name ?? 'U')[1] ?? '', 0, 1)) }}
                        </div>
                        <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-green-500 border-4 border-white rounded-full"></div>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-slate-900 tracking-tight">{{ $usuario->name ?? 'Usuario' }}</h2>
                        <p class="text-slate-400 font-bold text-xs uppercase tracking-widest">{{ $usuario->email ?? 'email@exemplo.com' }}</p>
                    </div>
                </div>

                <form action="{{ route('perfil.atualizar') }}" method="POST" class="space-y-6" id="perfilFormStandalone">
                    @csrf
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="form-group">
                            <label class="form-label text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Nome Completo</label>
                            <input type="text" name="name" value="{{ old('name', $usuario->name ?? '') }}" class="form-input bg-slate-50 border-transparent focus:bg-white focus:border-[#448ccb] h-14 rounded-xl font-bold" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Username</label>
                            <input type="text" name="username" id="standalone_username" value="{{ old('username', $usuario->username ?? '') }}" class="form-input bg-slate-50 border-transparent focus:bg-white focus:border-[#448ccb] h-14 rounded-xl font-bold" minlength="3" maxlength="30" required>
                            <small class="perfil-hint" id="standalone_username_hint">Apenas letras, números e _</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">E-mail (Não alterável)</label>
                        <input type="email" value="{{ $usuario->email ?? '' }}" class="form-input bg-slate-50 border-transparent opacity-60 h-14 rounded-xl font-bold" disabled>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="form-group">
                            <label class="form-label text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">CPF ou CNPJ</label>
                            <input type="text" name="cpf" id="standalone_cpf" value="{{ old('cpf', $usuario->cpf ? preg_replace(['/(\d{3})(\d{3})(\d{3})(\d{2})/', '/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/'], ['$1.$2.$3-$4', '$1.$2.$3/$4-$5'], $usuario->cpf) : '') }}" class="form-input bg-slate-50 border-transparent focus:bg-white focus:border-[#448ccb] h-14 rounded-xl font-bold" maxlength="18" required>
                            <small class="perfil-hint" id="standalone_cpf_hint">Necessário para pagamentos via PIX</small>
                        </div>
                        <div class="form-group">
                            <label class="form-label text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Telefone (WhatsApp)</label>
                            @php
                                $phoneStandalone = '';
                                if (!empty($usuario->phone)) {
                                    $p = $usuario->phone;
                                    if (strlen($p) === 11) {
                                        $phoneStandalone = '(' . substr($p,0,2) . ') ' . substr($p,2,5) . '-' . substr($p,7);
                                    } elseif (strlen($p) === 10) {
                                        $phoneStandalone = '(' . substr($p,0,2) . ') ' . substr($p,2,4) . '-' . substr($p,6);
                                    } else {
                                        $phoneStandalone = $p;
                                    }
                                }
                            @endphp
                            <input type="text" name="phone" id="standalone_phone" value="{{ old('phone', $phoneStandalone) }}" class="form-input bg-slate-50 border-transparent focus:bg-white focus:border-[#448ccb] h-14 rounded-xl font-bold" placeholder="(00) 00000-0000" maxlength="15" required>
                            <small class="perfil-hint">Para suporte e notificações</small>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="px-10 py-4 rounded-2xl bg-[#23366f] text-white font-black hover:scale-[1.02] transition-all shadow-xl shadow-blue-900/20">
                            Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>

            <!-- Alterar Senha -->
            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
                <h2 class="text-xl font-black text-slate-900 mb-2">Segurança da Conta</h2>
                <p class="text-sm text-slate-400 font-medium mb-10">Atualize sua senha para manter sua conta protegida.</p>

                <form action="{{ route('perfil.senha') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="form-group">
                        <label class="form-label text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Senha Atual</label>
                        <input type="password" name="senha_atual" class="form-input bg-slate-50 border-transparent focus:bg-white focus:border-[#448ccb] h-14 rounded-xl font-bold" required>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="form-group">
                            <label class="form-label text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Nova Senha</label>
                            <input type="password" name="nova_senha" class="form-input bg-slate-50 border-transparent focus:bg-white focus:border-[#448ccb] h-14 rounded-xl font-bold" minlength="6" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Confirmar Nova Senha</label>
                            <input type="password" name="nova_senha_confirmation" class="form-input bg-slate-50 border-transparent focus:bg-white focus:border-[#448ccb] h-14 rounded-xl font-bold" minlength="6" required>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="px-10 py-4 rounded-2xl border-2 border-[#23366f] text-[#23366f] font-black hover:bg-[#23366f] hover:text-white transition-all">
                            Alterar Senha
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Coluna da Direita: Status -->
        <div class="lg:col-span-1 space-y-8">
            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
                <h2 class="text-xl font-black text-slate-900 mb-8">Status da Conta</h2>
                
                <div class="space-y-6">
                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-[#23366f] shadow-sm">
                                <i class="fas fa-crown"></i>
                            </div>
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Plano Atual</span>
                        </div>
                        <span class="font-black text-[#23366f] text-sm">{{ $usuario->plano ?? 'Premium' }}</span>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-[#23366f] shadow-sm">
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Nível</span>
                        </div>
                        <span class="font-black text-slate-700 text-sm">{{ ucfirst($usuario->cargo ?? 'Usuário') }}</span>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-[#23366f] shadow-sm">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Estado</span>
                        </div>
                        <span class="px-3 py-1 rounded-lg {{ $usuario->status ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }} text-[10px] font-black uppercase">
                            {{ $usuario->status ? 'Verificada' : 'Pendente' }}
                        </span>
                    </div>
                </div>

                <div class="mt-10 p-6 rounded-2xl bg-[#23366f] text-white">
                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] opacity-60 mb-2">Membro desde</p>
                    <p class="text-lg font-black">{{ \Carbon\Carbon::parse($usuario->created_at)->format('F, Y') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
@parent
<style>
    .perfil-hint {
        display: block;
        font-size: 0.75rem;
        color: #94a3b8;
        margin-top: 0.35rem;
    }
    .perfil-hint.hint-valid { color: #10b981; font-weight: 600; }
    .perfil-hint.hint-invalid { color: #ef4444; font-weight: 600; }
    .form-input.input-valid { border-color: #10b981 !important; background: #f0fdf4 !important; }
    .form-input.input-invalid { border-color: #ef4444 !important; background: #fef2f2 !important; }
</style>
@endsection

@section('scripts')
document.addEventListener('DOMContentLoaded', function() {
    function _validarCPF(cpf) {
        cpf = cpf.replace(/\D/g, '');
        if (cpf.length !== 11) return false;
        if (/^(\d)\1{10}$/.test(cpf)) return false;
        for (let t = 9; t < 11; t++) {
            let sum = 0;
            for (let i = 0; i < t; i++) sum += parseInt(cpf[i]) * ((t + 1) - i);
            let digit = ((10 * sum) % 11) % 10;
            if (parseInt(cpf[t]) !== digit) return false;
        }
        return true;
    }

    function _validarCNPJ(cnpj) {
        cnpj = cnpj.replace(/\D/g, '');
        if (cnpj.length !== 14) return false;
        if (/^(\d)\1{13}$/.test(cnpj)) return false;
        const w1 = [5,4,3,2,9,8,7,6,5,4,3,2], w2 = [6,5,4,3,2,9,8,7,6,5,4,3,2];
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

    // CPF/CNPJ
    const cpfInput = document.getElementById('standalone_cpf');
    const cpfHint = document.getElementById('standalone_cpf_hint');
    let cpfValido = false;

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
                cpfValido = _checarCpfSilencioso(cpfInput.value);
                return;
            }

            cpfInput.classList.remove('input-valid', 'input-invalid');
            cpfHint.classList.remove('hint-valid', 'hint-invalid');
            if (nums.length === 0) { cpfHint.textContent = 'Necessário para pagamentos via PIX'; cpfValido = false; return; }
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
                cpfHint.textContent = `Digitando ${nums.length > 11 ? 'CNPJ' : 'CPF'}...`;
                cpfValido = false;
            }
        };
        cpfInput.addEventListener('input', function(e) {
            let v = e.target.value.replace(/\D/g, '');
            if (v.length <= 11) { v = v.replace(/(\d{3})(\d)/, '$1.$2').replace(/(\d{3})(\d)/, '$1.$2').replace(/(\d{3})(\d{1,2})$/, '$1-$2'); }
            else { v = v.substring(0,14); v = v.replace(/(\d{2})(\d)/, '$1.$2').replace(/(\d{3})(\d)/, '$1.$2').replace(/(\d{3})(\d)/, '$1/$2').replace(/(\d{4})(\d{1,2})$/, '$1-$2'); }
            e.target.value = v;
            atualizarCpf(true);
        });
        // Validar silenciosamente no carregamento (sem feedback visual)
        if (cpfInput.value) atualizarCpf(false);
    }

    // Telefone
    const phoneInput = document.getElementById('standalone_phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let v = e.target.value.replace(/\D/g, '');
            if (v.length <= 10) { v = v.replace(/(\d{2})(\d)/, '($1) $2').replace(/(\d{4})(\d{1,4})$/, '$1-$2'); }
            else { v = v.replace(/(\d{2})(\d)/, '($1) $2').replace(/(\d{5})(\d{1,4})$/, '$1-$2'); }
            e.target.value = v;
        });
    }

    // Username
    const usernameInput = document.getElementById('standalone_username');
    const usernameHint = document.getElementById('standalone_username_hint');
    let usernameValido = true, usernameTimer = null;
    const usernameOriginal = usernameInput ? usernameInput.value : '';

    if (usernameInput) {
        usernameInput.addEventListener('input', function() {
            const val = usernameInput.value.trim();
            usernameInput.classList.remove('input-valid', 'input-invalid');
            usernameHint.classList.remove('hint-valid', 'hint-invalid');
            if (val.length < 3) { usernameHint.textContent = 'Mínimo 3 caracteres'; usernameHint.classList.add('hint-invalid'); usernameValido = false; return; }
            if (!/^[a-zA-Z0-9_]+$/.test(val)) { usernameHint.textContent = 'Apenas letras, números e _'; usernameHint.classList.add('hint-invalid'); usernameInput.classList.add('input-invalid'); usernameValido = false; return; }
            if (val === usernameOriginal) { usernameHint.textContent = 'Seu username atual'; usernameValido = true; return; }
            usernameHint.textContent = 'Verificando...'; usernameValido = false;
            clearTimeout(usernameTimer);
            usernameTimer = setTimeout(async () => {
                try {
                    const res = await fetch('{{ route("perfil.checar-username") }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}' }, body: JSON.stringify({ username: val }) });
                    const data = await res.json();
                    if (usernameInput.value.trim() !== val) return;
                    usernameInput.classList.remove('input-valid', 'input-invalid');
                    usernameHint.classList.remove('hint-valid', 'hint-invalid');
                    if (data.available) { usernameInput.classList.add('input-valid'); usernameHint.classList.add('hint-valid'); usernameHint.textContent = 'Username disponível'; usernameValido = true; }
                    else { usernameInput.classList.add('input-invalid'); usernameHint.classList.add('hint-invalid'); usernameHint.textContent = data.message || 'Username já está em uso'; usernameValido = false; }
                } catch (err) { usernameHint.textContent = 'Erro ao verificar'; }
            }, 400);
        });
    }

    // Submit
    const form = document.getElementById('perfilFormStandalone');
    if (form) {
        form.addEventListener('submit', function(e) {
            const errors = [];
            if (!usernameValido) errors.push('Username inválido ou já em uso.');
            if (!cpfValido) errors.push('CPF/CNPJ inválido.');
            if (errors.length) { e.preventDefault(); alert(errors.join('\n')); }
        });
    }
});
@endsection
