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
        <a href="mailto:contato@alfaproxy.com" class="text-[#4F8BFF] font-semibold text-sm">contato@alfaproxy.com</a>
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
                
                <x-ui.select name="categoria" :value="old('categoria')" placeholder="Selecione" :options="[
                    'pagamento' => 'Pagamento',
                    'proxy' => 'Problemas com Proxy',
                    'conta' => 'Conta e Perfil',
                    'tecnico' => 'Suporte Técnico',
                    'outros' => 'Outros'
                ]" required>
                </x-ui.select>
            </div>

            <div class="mb-4">
                <label class="form-label">Prioridade</label>
                <x-ui.select name="prioridade" :value="old('prioridade')" placeholder="Selecione" :options="[
                    'baixa' => 'Baixa',
                    'media' => 'Média',
                    'alta' => 'Alta',
                    'urgente' => 'Urgente'
                ]" required>
                </x-ui.select>
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
