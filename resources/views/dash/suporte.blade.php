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
<div class="flex flex-col gap-2 mb-8">
    <p class="text-sm uppercase tracking-[0.35em] text-slate-500">Central de ajuda</p>
    <h1 class="text-3xl font-bold text-slate-900">Suporte & Tickets</h1>
    <p class="text-slate-500">Estamos aqui para ajudar! Entre em contato ou consulte nossas perguntas frequentes.</p>
</div>

<!-- Métodos de Contato -->
<div class="grid md:grid-cols-3 gap-6 mb-8">
    <div class="contact-method">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#4F8BFF] to-[#2055dd] flex items-center justify-center">
                <i class="fas fa-envelope text-white text-xl"></i>
            </div>
            <div>
                <p class="font-semibold text-slate-900">E-mail</p>
                <p class="text-xs text-slate-500">Resposta em até 24h</p>
            </div>
        </div>
        <a href="mailto:suporte@alfaproxy.com" class="text-[#4F8BFF] font-semibold text-sm">suporte@alfaproxy.com</a>
    </div>

    <div class="contact-method">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#25D366] to-[#128C7E] flex items-center justify-center">
                <i class="fab fa-whatsapp text-white text-xl"></i>
            </div>
            <div>
                <p class="font-semibold text-slate-900">WhatsApp</p>
                <p class="text-xs text-slate-500">Atendimento rápido</p>
            </div>
        </div>
        <a href="https://wa.me/5511999999999" target="_blank" class="text-[#25D366] font-semibold text-sm">+55 11 99999-9999</a>
    </div>

    <div class="contact-method">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#7289DA] to-[#5865F2] flex items-center justify-center">
                <i class="fab fa-discord text-white text-xl"></i>
            </div>
            <div>
                <p class="font-semibold text-slate-900">Discord</p>
                <p class="text-xs text-slate-500">Comunidade ativa</p>
            </div>
        </div>
        <a href="https://discord.gg/alfaproxy" target="_blank" class="text-[#7289DA] font-semibold text-sm">discord.gg/alfaproxy</a>
    </div>
</div>

<div class="grid lg:grid-cols-2 gap-8">
    <!-- Formulário de Ticket -->
    <div class="support-card">
        <h2 class="text-xl font-semibold text-slate-900 mb-4">Abrir Novo Ticket</h2>
        <p class="text-sm text-slate-500 mb-6">Descreva seu problema ou dúvida e entraremos em contato em breve.</p>

        <form action="#" method="POST">
            @csrf
            <div class="mb-4">
                <label class="form-label">Assunto</label>
                <input type="text" name="assunto" class="form-input" placeholder="Ex: Problema com pagamento" required>
            </div>

            <div class="mb-4">
                <label class="form-label">Categoria</label>
                <select name="categoria" class="form-input" required>
                    <option value="">Selecione uma categoria</option>
                    <option value="pagamento">Pagamento</option>
                    <option value="proxy">Problemas com Proxy</option>
                    <option value="conta">Conta e Perfil</option>
                    <option value="tecnico">Suporte Técnico</option>
                    <option value="outros">Outros</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label">Prioridade</label>
                <select name="prioridade" class="form-input" required>
                    <option value="baixa">Baixa</option>
                    <option value="media" selected>Média</option>
                    <option value="alta">Alta</option>
                    <option value="urgente">Urgente</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label">Descrição</label>
                <textarea name="descricao" class="form-textarea" placeholder="Descreva seu problema em detalhes..." required></textarea>
            </div>

            <button type="submit" class="btn-primary">
                <i class="fas fa-paper-plane"></i> Enviar Ticket
            </button>
        </form>
    </div>

    <!-- FAQ -->
    <div class="support-card">
        <h2 class="text-xl font-semibold text-slate-900 mb-4">Perguntas Frequentes</h2>
        <p class="text-sm text-slate-500 mb-6">Respostas rápidas para dúvidas comuns.</p>

        <div class="space-y-3">
            <div class="faq-item">
                <div class="flex items-center justify-between">
                    <p class="font-semibold text-slate-900">Como funciona o proxy SOCKS5?</p>
                    <i class="fas fa-chevron-down faq-icon text-slate-400"></i>
                </div>
                <div class="faq-answer">
                    <p>O SOCKS5 é um protocolo de proxy que permite rotear qualquer tipo de tráfego através de um servidor intermediário, oferecendo maior privacidade e segurança. Ele suporta autenticação e pode lidar com diversos protocolos, incluindo HTTP, HTTPS e FTP.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="flex items-center justify-between">
                    <p class="font-semibold text-slate-900">Quanto tempo leva para ativar meu proxy?</p>
                    <i class="fas fa-chevron-down faq-icon text-slate-400"></i>
                </div>
                <div class="faq-answer">
                    <p>Após a confirmação do pagamento, seus proxies são ativados automaticamente em até 5 minutos. Você receberá um e-mail com as credenciais de acesso.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="flex items-center justify-between">
                    <p class="font-semibold text-slate-900">Posso trocar o país do meu proxy?</p>
                    <i class="fas fa-chevron-down faq-icon text-slate-400"></i>
                </div>
                <div class="faq-answer">
                    <p>Sim! Entre em contato com nosso suporte e faremos a troca sem custo adicional, sujeito à disponibilidade de IPs no país desejado.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="flex items-center justify-between">
                    <p class="font-semibold text-slate-900">O que é renovação automática?</p>
                    <i class="fas fa-chevron-down faq-icon text-slate-400"></i>
                </div>
                <div class="faq-answer">
                    <p>A renovação automática garante que seu proxy seja renovado automaticamente antes do vencimento, evitando interrupções no serviço. Você pode ativar/desativar essa opção a qualquer momento.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="flex items-center justify-between">
                    <p class="font-semibold text-slate-900">Quais formas de pagamento são aceitas?</p>
                    <i class="fas fa-chevron-down faq-icon text-slate-400"></i>
                </div>
                <div class="faq-answer">
                    <p>Aceitamos PIX, cartão de crédito/débito e criptomoedas (Bitcoin, USDT, Litecoin e BNB). Todas as transações são processadas de forma segura.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="flex items-center justify-between">
                    <p class="font-semibold text-slate-900">Posso solicitar reembolso?</p>
                    <i class="fas fa-chevron-down faq-icon text-slate-400"></i>
                </div>
                <div class="faq-answer">
                    <p>Sim, oferecemos garantia de reembolso de 7 dias para novos clientes. Se não estiver satisfeito com nosso serviço, entre em contato e processaremos o reembolso.</p>
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
