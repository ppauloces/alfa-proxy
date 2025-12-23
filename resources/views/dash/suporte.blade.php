@extends('dash.layout')

@section('title', 'AlfaProxy - Suporte')

@section('styles')
<style>
.support-card {
    background: #fff;
    border-radius: 28px;
    border: 1px solid rgba(226,232,240,0.9);
    padding: 2rem;
    box-shadow: 0 20px 60px rgba(15,23,42,0.08);
}
.contact-method {
    padding: 1.5rem;
    border: 2px solid rgba(226,232,240,0.9);
    border-radius: 20px;
    transition: all 0.2s ease;
    cursor: pointer;
}
.contact-method:hover {
    border-color: var(--sf-blue);
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(32,85,221,0.15);
}
.faq-item {
    border: 1px solid rgba(226,232,240,0.9);
    border-radius: 16px;
    padding: 1.25rem;
    margin-bottom: 1rem;
    cursor: pointer;
    transition: all 0.2s ease;
}
.faq-item:hover {
    border-color: var(--sf-blue);
}
.faq-answer {
    display: none;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid rgba(226,232,240,0.5);
    color: #64748b;
}
.faq-item.open .faq-answer {
    display: block;
}
.faq-item.open .faq-icon {
    transform: rotate(180deg);
}
.faq-icon {
    transition: transform 0.2s ease;
}
.form-label {
    display: block;
    font-size: 0.875rem;
    font-weight: 600;
    color: #475569;
    margin-bottom: 0.5rem;
}
.form-input, .form-textarea {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid rgba(226,232,240,0.9);
    border-radius: 12px;
    font-size: 0.9375rem;
    transition: all 0.2s ease;
}
.form-input:focus, .form-textarea:focus {
    outline: none;
    border-color: var(--sf-blue);
    box-shadow: 0 0 0 3px rgba(32,85,221,0.1);
}
.form-textarea {
    min-height: 150px;
    resize: vertical;
}
.btn-primary {
    width: 100%;
    padding: 1rem 1.5rem;
    border-radius: 12px;
    background: linear-gradient(120deg, var(--sf-blue-light), var(--sf-blue));
    color: #fff;
    font-weight: 600;
    font-size: 1rem;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
}
.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 30px rgba(32,85,221,0.3);
}
</style>
@endsection

@section('content')
<div class="flex flex-col gap-6">
    {{-- Header da Seção --}}
    <div class="space-y-1">
        <p class="text-[10px] font-bold text-[#448ccb] uppercase tracking-[0.3em]">Central de ajuda</p>
        <h1 class="text-4xl font-black text-slate-900 tracking-tight">Suporte & <span class="text-[#23366f]">Tickets</span></h1>
        <p class="text-slate-500 font-medium max-w-xl">Estamos aqui para ajudar! Entre em contato ou consulte nossas perguntas frequentes.</p>
    </div>

    <!-- Métodos de Contato -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="mailto:suporte@alfaproxy.com" class="group bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm hover:border-[#23366f] transition-all">
            <div class="w-14 h-14 rounded-2xl bg-blue-50 text-[#23366f] flex items-center justify-center text-2xl mb-6 group-hover:scale-110 transition-transform">
                <i class="fas fa-envelope"></i>
            </div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">E-mail</p>
            <p class="text-lg font-black text-slate-900 mb-2">suporte@alfaproxy.com</p>
            <p class="text-xs text-slate-400 font-medium">Resposta em até 24 horas úteis.</p>
        </a>

        <a href="https://wa.me/5511999999999" target="_blank" class="group bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm hover:border-green-500 transition-all">
            <div class="w-14 h-14 rounded-2xl bg-green-50 text-green-600 flex items-center justify-center text-2xl mb-6 group-hover:scale-110 transition-transform">
                <i class="fab fa-whatsapp"></i>
            </div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">WhatsApp</p>
            <p class="text-lg font-black text-slate-900 mb-2">+55 11 99999-9999</p>
            <p class="text-xs text-slate-400 font-medium">Atendimento em tempo real.</p>
        </a>

        <a href="https://discord.gg/alfaproxy" target="_blank" class="group bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm hover:border-[#5865F2] transition-all">
            <div class="w-14 h-14 rounded-2xl bg-[#5865F2]/10 text-[#5865F2] flex items-center justify-center text-2xl mb-6 group-hover:scale-110 transition-transform">
                <i class="fab fa-discord"></i>
            </div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Discord</p>
            <p class="text-lg font-black text-slate-900 mb-2">Comunidade Alfa</p>
            <p class="text-xs text-slate-400 font-medium">Interaja com outros usuários.</p>
        </a>
    </div>

    <div class="grid lg:grid-cols-2 gap-8">
        <!-- Formulário de Ticket -->
        <div class="bg-white p-10 rounded-[2.5rem] border border-slate-100 shadow-sm">
            <h2 class="text-2xl font-black text-slate-900 mb-2">Abrir Novo Ticket</h2>
            <p class="text-sm text-slate-400 font-medium mb-10">Descreva sua solicitação e nossa equipe analisará o mais rápido possível.</p>

            <form action="#" method="POST" class="space-y-6">
                @csrf
                <div class="form-group">
                    <label class="form-label text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Assunto do Ticket</label>
                    <input type="text" name="assunto" class="form-input bg-slate-50 border-transparent focus:bg-white focus:border-[#448ccb] h-14 rounded-xl font-bold" placeholder="Ex: Dúvida sobre renovação" required>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div class="form-group">
                        <label class="form-label text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Categoria</label>
                        <select name="categoria" class="form-select bg-slate-50 border-transparent focus:bg-white focus:border-[#448ccb] h-14 rounded-xl font-bold" required>
                            <option value="">Selecione</option>
                            <option value="pagamento">Pagamento</option>
                            <option value="proxy">Proxies</option>
                            <option value="tecnico">Técnico</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Prioridade</label>
                        <select name="prioridade" class="form-select bg-slate-50 border-transparent focus:bg-white focus:border-[#448ccb] h-14 rounded-xl font-bold" required>
                            <option value="baixa">Baixa</option>
                            <option value="media" selected>Média</option>
                            <option value="alta">Alta</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Descrição Detalhada</label>
                    <textarea name="descricao" class="form-textarea bg-slate-50 border-transparent focus:bg-white focus:border-[#448ccb] rounded-2xl p-5 font-bold min-h-[150px]" placeholder="Conte-nos o que está acontecendo..." required></textarea>
                </div>

                <button type="submit" class="w-full py-4 rounded-2xl bg-[#23366f] text-white font-black hover:scale-[1.02] transition-all shadow-xl shadow-blue-900/20">
                    Enviar Solicitação
                </button>
            </form>
        </div>

        <!-- FAQ -->
        <div class="bg-white p-10 rounded-[2.5rem] border border-slate-100 shadow-sm">
            <h2 class="text-2xl font-black text-slate-900 mb-2">Perguntas Frequentes</h2>
            <p class="text-sm text-slate-400 font-medium mb-10">Respostas rápidas para as dúvidas mais comuns dos nossos clientes.</p>

            <div class="space-y-4">
                @php
                    $faqs = [
                        ['q' => 'Como funciona o proxy SOCKS5?', 'a' => 'O SOCKS5 é um protocolo que permite rotear tráfego com alta segurança e suporte a diversos protocolos.'],
                        ['q' => 'Quanto tempo leva para ativar?', 'a' => 'A ativação é automática e ocorre em até 5 minutos após a confirmação do pagamento.'],
                        ['q' => 'Posso trocar o país do IP?', 'a' => 'Sim, basta abrir um ticket e nossa equipe fará a alteração conforme disponibilidade.'],
                        ['q' => 'O que é renovação automática?', 'a' => 'É um sistema que garante que seus proxies não expirem, cobrando o valor do seu saldo no dia do vencimento.'],
                    ];
                @endphp

                @foreach($faqs as $faq)
                    <div class="faq-item group p-6 rounded-2xl border border-slate-50 hover:border-[#23366f] transition-all cursor-pointer">
                        <div class="flex items-center justify-between">
                            <p class="font-bold text-slate-700 group-hover:text-[#23366f] transition-colors">{{ $faq['q'] }}</p>
                            <i class="fas fa-chevron-down faq-icon text-slate-300 group-hover:text-[#23366f] transition-all"></i>
                        </div>
                        <div class="faq-answer mt-4 pt-4 border-t border-slate-50 text-sm text-slate-500 font-medium leading-relaxed">
                            {{ $faq['a'] }}
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-10 p-6 rounded-2xl bg-slate-50 border border-slate-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-white flex items-center justify-center text-[#23366f] shadow-sm">
                    <i class="fas fa-lightbulb"></i>
                </div>
                <div>
                    <p class="text-xs font-black text-slate-900 uppercase">Dica Alfa</p>
                    <p class="text-[11px] text-slate-400 font-medium">Nossa documentação completa está disponível na seção API.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
// FAQ toggle
document.querySelectorAll('.faq-item').forEach(item => {
    item.addEventListener('click', () => {
        item.classList.toggle('open');
    });
});
@endsection
