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
                @foreach($errors->perfil->all() as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif

        <form action="{{ route('perfil.atualizar') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Nome Completo</label>
                <input type="text" name="name" value="{{ old('name', $usuario->name ?? '') }}" class="form-input" required>
            </div>

            <div class="form-group">
                <label class="form-label">E-mail</label>
                <input type="email" value="{{ $usuario->email ?? '' }}" class="form-input" disabled>
                <p class="text-xs text-slate-500 mt-2">O e-mail não pode ser alterado. Entre em contato com o suporte se necessário.</p>
            </div>

            <div class="form-group">
                <label class="form-label">Username</label>
                <input type="text" value="{{ $usuario->username ?? '' }}" class="form-input" disabled>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="btn-primary">
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

            <div class="form-group">
                <label class="form-label">Nova Senha</label>
                <input type="password" name="nova_senha" class="form-input" minlength="6" required>
            </div>

            <div class="form-group">
                <label class="form-label">Confirmar Nova Senha</label>
                <input type="password" name="nova_senha_confirmation" class="form-input" minlength="6" required>
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
