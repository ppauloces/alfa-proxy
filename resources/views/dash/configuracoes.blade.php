@extends('dash.layout')

@section('title', 'AlfaProxy - Configurações')

@section('styles')
<style>
.settings-card {
    background: #fff;
    border-radius: 28px;
    border: 1px solid rgba(226,232,240,0.9);
    padding: 2rem;
    box-shadow: 0 20px 60px rgba(15,23,42,0.08);
    margin-bottom: 1.5rem;
}
.setting-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.25rem 0;
    border-bottom: 1px solid rgba(226,232,240,0.5);
}
.setting-item:last-child {
    border-bottom: none;
}
.switch {
    position: relative;
    width: 50px;
    height: 28px;
}
.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}
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
.switch input:checked + .slider {
    background-color: var(--sf-blue);
}
.switch input:checked + .slider:before {
    transform: translateX(22px);
}
.danger-zone {
    border: 2px solid rgba(239,68,68,0.3);
    background: rgba(239,68,68,0.02);
}
.btn-danger {
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    background: #ef4444;
    color: white;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
}
.btn-danger:hover {
    background: #dc2626;
    transform: translateY(-2px);
}
.btn-secondary {
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    background: rgba(148,163,184,0.15);
    color: #475569;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
}
.btn-secondary:hover {
    background: rgba(148,163,184,0.25);
}
</style>
@endsection

@section('content')
<div class="flex flex-col gap-6">
    {{-- Header da Seção --}}
    <div class="space-y-1">
        <p class="text-[10px] font-bold text-[#448ccb] uppercase tracking-[0.3em]">Preferências</p>
        <h1 class="text-4xl font-black text-slate-900 tracking-tight">Configurações do <span class="text-[#23366f]">Sistema</span></h1>
        <p class="text-slate-500 font-medium max-w-xl">Personalize sua experiência e gerencie suas preferências de conta.</p>
    </div>

    <div class="grid lg:grid-cols-2 gap-8">
        <!-- Notificações -->
        <div class="bg-white p-10 rounded-[2.5rem] border border-slate-100 shadow-sm">
            <h2 class="text-2xl font-black text-slate-900 mb-2">Notificações</h2>
            <p class="text-sm text-slate-400 font-medium mb-10">Escolha como e quando você deseja receber alertas.</p>

            <div class="space-y-8">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-bold text-slate-700">E-mail de Transações</p>
                        <p class="text-[11px] text-slate-400 font-medium">Receba confirmações por e-mail</p>
                    </div>
                    <label class="switch scale-90">
                        <input type="checkbox" checked>
                        <span class="slider"></span>
                    </label>
                </div>

                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-bold text-slate-700">Alertas de Expiração</p>
                        <p class="text-[11px] text-slate-400 font-medium">Aviso prévio antes do vencimento</p>
                    </div>
                    <label class="switch scale-90">
                        <input type="checkbox" checked>
                        <span class="slider"></span>
                    </label>
                </div>

                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-bold text-slate-700">Informativos & Novidades</p>
                        <p class="text-[11px] text-slate-400 font-medium">Receba promoções exclusivas</p>
                    </div>
                    <label class="switch scale-90">
                        <input type="checkbox">
                        <span class="slider"></span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Segurança -->
        <div class="bg-white p-10 rounded-[2.5rem] border border-slate-100 shadow-sm">
            <h2 class="text-2xl font-black text-slate-900 mb-2">Segurança</h2>
            <p class="text-sm text-slate-400 font-medium mb-10">Proteção avançada para sua conta.</p>

            <div class="space-y-6">
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-[#23366f] shadow-sm">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-700">Verificação em 2 Etapas</p>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Desativado</p>
                        </div>
                    </div>
                    <button class="px-4 py-2 rounded-xl text-[10px] font-black uppercase bg-[#23366f] text-white transition-all">Configurar</button>
                </div>

                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-[#23366f] shadow-sm">
                            <i class="fas fa-desktop"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-700">Sessões Ativas</p>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">1 dispositivo online</p>
                        </div>
                    </div>
                    <button class="px-4 py-2 rounded-xl text-[10px] font-black uppercase border border-slate-200 text-slate-400 transition-all">Gerenciar</button>
                </div>
            </div>
        </div>

        <!-- Preferências -->
        <div class="bg-white p-10 rounded-[2.5rem] border border-slate-100 shadow-sm">
            <h2 class="text-2xl font-black text-slate-900 mb-2">Preferências</h2>
            <p class="text-sm text-slate-400 font-medium mb-10">Ajuste como o AlfaProxy funciona para você.</p>

            <div class="space-y-8">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-bold text-slate-700">Renovação Automática Padrão</p>
                        <p class="text-[11px] text-slate-400 font-medium">Sempre ativar ao comprar novos IPs</p>
                    </div>
                    <label class="switch scale-90">
                        <input type="checkbox" checked>
                        <span class="slider"></span>
                    </label>
                </div>

                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-bold text-slate-700">Teste Automático de Proxies</p>
                        <p class="text-[11px] text-slate-400 font-medium">Verificar saúde das rotas a cada 1h</p>
                    </div>
                    <label class="switch scale-90">
                        <input type="checkbox">
                        <span class="slider"></span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Perigo -->
        <div class="bg-red-50 p-10 rounded-[2.5rem] border border-red-100 shadow-sm shadow-red-900/5">
            <h2 class="text-2xl font-black text-red-600 mb-2">Zona de Perigo</h2>
            <p class="text-sm text-red-400 font-medium mb-10">Ações críticas e irreversíveis.</p>

            <div class="space-y-4">
                <button class="w-full p-5 rounded-2xl bg-white border border-red-100 flex items-center justify-between group hover:bg-red-600 transition-all">
                    <div class="flex items-center gap-4 text-left">
                        <div class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center group-hover:bg-red-500 group-hover:text-white transition-all">
                            <i class="fas fa-trash-alt"></i>
                        </div>
                        <div>
                            <p class="text-sm font-black text-slate-900 group-hover:text-white">Excluir Minha Conta</p>
                            <p class="text-[10px] text-slate-400 font-bold uppercase group-hover:text-red-200">Apagar todos os dados e proxies</p>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-red-200 group-hover:text-white group-hover:translate-x-1 transition-all"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- Botões de Ação Final --}}
    <div class="flex items-center justify-end gap-4 mt-6">
        <button class="px-8 py-4 rounded-2xl text-sm font-black text-slate-400 hover:text-slate-600 transition-all uppercase tracking-widest">Descartar</button>
        <button class="px-12 py-4 rounded-2xl bg-[#23366f] text-white font-black hover:scale-[1.02] transition-all shadow-xl shadow-blue-900/20" onclick="alert('Configurações salvas!')">
            Salvar Preferências
        </button>
    </div>
</div>
@endsection
@endsection

@section('scripts')
// Save confirmation
document.querySelector('.btn-primary').addEventListener('click', () => {
    alert('Configurações salvas com sucesso!');
});
@endsection
