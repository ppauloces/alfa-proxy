<div class="flex flex-col gap-2 mb-8">
    <p class="text-sm uppercase tracking-[0.35em] text-slate-500">Preferências</p>
    <h1 class="text-3xl font-bold text-slate-900">Configurações</h1>
    <p class="text-slate-500">Personalize sua experiência e gerencie suas preferências.</p>
</div>

<!-- Notificações -->
<div class="settings-card">
    <h2 class="text-xl font-semibold text-slate-900 mb-4">Notificações</h2>
    <p class="text-sm text-slate-500 mb-6">Escolha como e quando você deseja receber notificações.</p>

    <div class="setting-item">
        <div>
            <p class="font-semibold text-slate-900">E-mail de Transações</p>
            <p class="text-sm text-slate-500">Receba confirmações de pagamento por e-mail</p>
        </div>
        <label class="switch">
            <input type="checkbox" checked>
            <span class="slider"></span>
        </label>
    </div>

    <div class="setting-item">
        <div>
            <p class="font-semibold text-slate-900">Alertas de Expiração</p>
            <p class="text-sm text-slate-500">Ser notificado quando proxies estiverem próximos do vencimento</p>
        </div>
        <label class="switch">
            <input type="checkbox" checked>
            <span class="slider"></span>
        </label>
    </div>

    <div class="setting-item">
        <div>
            <p class="font-semibold text-slate-900">Newsletter</p>
            <p class="text-sm text-slate-500">Receber novidades e promoções por e-mail</p>
        </div>
        <label class="switch">
            <input type="checkbox">
            <span class="slider"></span>
        </label>
    </div>

    <div class="setting-item">
        <div>
            <p class="font-semibold text-slate-900">Notificações Push</p>
            <p class="text-sm text-slate-500">Receber notificações no navegador</p>
        </div>
        <label class="switch">
            <input type="checkbox">
            <span class="slider"></span>
        </label>
    </div>
</div>

<!-- Segurança -->
<div class="settings-card">
    <h2 class="text-xl font-semibold text-slate-900 mb-4">Segurança</h2>
    <p class="text-sm text-slate-500 mb-6">Proteja sua conta com opções de segurança avançadas.</p>

    <div class="setting-item">
        <div>
            <p class="font-semibold text-slate-900">Autenticação de Dois Fatores (2FA)</p>
            <p class="text-sm text-slate-500">Adicione uma camada extra de segurança à sua conta</p>
        </div>
        <button class="btn-secondary">
            <i class="fas fa-shield-alt"></i> Configurar
        </button>
    </div>

    <div class="setting-item">
        <div>
            <p class="font-semibold text-slate-900">Sessões Ativas</p>
            <p class="text-sm text-slate-500">Gerencie dispositivos conectados à sua conta</p>
        </div>
        <button class="btn-secondary">
            <i class="fas fa-desktop"></i> Ver Sessões
        </button>
    </div>

    <div class="setting-item">
        <div>
            <p class="font-semibold text-slate-900">Histórico de Login</p>
            <p class="text-sm text-slate-500">Visualize acessos recentes à sua conta</p>
        </div>
        <button class="btn-secondary">
            <i class="fas fa-history"></i> Ver Histórico
        </button>
    </div>
</div>

<!-- Preferências de Proxy -->
<div class="settings-card">
    <h2 class="text-xl font-semibold text-slate-900 mb-4">Preferências de Proxy</h2>
    <p class="text-sm text-slate-500 mb-6">Configure o comportamento padrão dos seus proxies.</p>

    <div class="setting-item">
        <div>
            <p class="font-semibold text-slate-900">Renovação Automática</p>
            <p class="text-sm text-slate-500">Renovar proxies automaticamente antes do vencimento</p>
        </div>
        <label class="switch">
            <input type="checkbox" checked>
            <span class="slider"></span>
        </label>
    </div>

    <div class="setting-item">
        <div>
            <p class="font-semibold text-slate-900">Formato de Exportação Padrão</p>
            <p class="text-sm text-slate-500">ip:porta:usuario:senha</p>
        </div>
        <button class="btn-secondary">
            <i class="fas fa-edit"></i> Alterar
        </button>
    </div>

    <div class="setting-item">
        <div>
            <p class="font-semibold text-slate-900">Teste Automático de Proxies</p>
            <p class="text-sm text-slate-500">Verificar status dos proxies automaticamente</p>
        </div>
        <label class="switch">
            <input type="checkbox">
            <span class="slider"></span>
        </label>
    </div>
</div>

<!-- Aparência -->
<div class="settings-card">
    <h2 class="text-xl font-semibold text-slate-900 mb-4">Aparência</h2>
    <p class="text-sm text-slate-500 mb-6">Personalize a interface do dashboard.</p>

    <div class="setting-item">
        <div>
            <p class="font-semibold text-slate-900">Tema Escuro</p>
            <p class="text-sm text-slate-500">Ativar modo escuro na interface</p>
        </div>
        <label class="switch">
            <input type="checkbox">
            <span class="slider"></span>
        </label>
    </div>

    <div class="setting-item">
        <div>
            <p class="font-semibold text-slate-900">Idioma</p>
            <p class="text-sm text-slate-500">Português (Brasil)</p>
        </div>
        <button class="btn-secondary">
            <i class="fas fa-globe"></i> Alterar
        </button>
    </div>

    <div class="setting-item">
        <div>
            <p class="font-semibold text-slate-900">Compactar Sidebar</p>
            <p class="text-sm text-slate-500">Iniciar com sidebar recolhida por padrão</p>
        </div>
        <label class="switch">
            <input type="checkbox">
            <span class="slider"></span>
        </label>
    </div>
</div>

<!-- API -->
<div class="settings-card">
    <h2 class="text-xl font-semibold text-slate-900 mb-4">API & Integrações</h2>
    <p class="text-sm text-slate-500 mb-6">Gerencie chaves de API e integrações com terceiros.</p>

    <div class="setting-item">
        <div>
            <p class="font-semibold text-slate-900">Chave de API</p>
            <p class="text-sm text-slate-500 font-mono">••••••••••••••••••••</p>
        </div>
        <div class="flex gap-2">
            <button class="btn-secondary">
                <i class="fas fa-eye"></i> Mostrar
            </button>
            <button class="btn-secondary">
                <i class="fas fa-sync-alt"></i> Regenerar
            </button>
        </div>
    </div>

    <div class="setting-item">
        <div>
            <p class="font-semibold text-slate-900">Webhooks</p>
            <p class="text-sm text-slate-500">Receber notificações em tempo real via webhook</p>
        </div>
        <button class="btn-secondary">
            <i class="fas fa-cog"></i> Configurar
        </button>
    </div>
</div>

<!-- Zona de Perigo -->
<div class="settings-card danger-zone">
    <h2 class="text-xl font-semibold text-red-600 mb-4">Zona de Perigo</h2>
    <p class="text-sm text-slate-500 mb-6">Ações irreversíveis que afetam permanentemente sua conta.</p>

    <div class="setting-item">
        <div>
            <p class="font-semibold text-slate-900">Exportar Dados</p>
            <p class="text-sm text-slate-500">Baixar uma cópia de todos os seus dados</p>
        </div>
        <button class="btn-secondary">
            <i class="fas fa-download"></i> Exportar
        </button>
    </div>

    <div class="setting-item">
        <div>
            <p class="font-semibold text-slate-900">Desativar Conta</p>
            <p class="text-sm text-slate-500">Desativar temporariamente sua conta</p>
        </div>
        <button class="btn-danger" onclick="confirm('Tem certeza que deseja desativar sua conta?')">
            <i class="fas fa-ban"></i> Desativar
        </button>
    </div>

    <div class="setting-item">
        <div>
            <p class="font-semibold text-slate-900">Excluir Conta</p>
            <p class="text-sm text-slate-500">Excluir permanentemente sua conta e todos os dados</p>
        </div>
        <button class="btn-danger" onclick="confirm('ATENÇÃO: Esta ação é IRREVERSÍVEL! Tem certeza?')">
            <i class="fas fa-trash"></i> Excluir
        </button>
    </div>
</div>

<!-- Botões de Ação -->
<div class="flex gap-4 justify-end">
    <button class="btn-secondary">
        <i class="fas fa-times"></i> Cancelar
    </button>
    <button class="btn-primary" style="width: auto; padding: 0.75rem 2rem;" data-settings-save>
        <i class="fas fa-save"></i> Salvar Configurações
    </button>
</div>
