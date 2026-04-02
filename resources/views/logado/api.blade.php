@extends('logado.partials.app')

@section('content')

<div class="bg-white rounded-lg shadow p-4 md:p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 md:mb-6">
        <h2 class="text-xl md:text-2xl font-bold text-gray-800">API de Integração CRM</h2>
        <div class="mt-2 md:mt-0">
            <button id="generateTokenBtn" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 md:px-4 md:py-2 rounded-lg transition duration-300 flex items-center space-x-2 text-sm md:text-base">
                <i class="fas fa-key"></i>
                <span>Gerar Novo Token</span>
            </button>
        </div>
    </div>

    <!-- Info Box -->
    <div class="bg-blue-50 border-l-4 border-blue-500 p-3 md:p-4 mb-4 md:mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-500 mt-1"></i>
            </div>
            <div class="ml-3">
                <p class="text-xs md:text-sm text-blue-700">
                    Utilize nossa API para integrar os serviços de proxy diretamente no seu CRM. Gere um token de acesso e configure no seu sistema externo.
                </p>
            </div>
        </div>
    </div>

    <!-- Token Section -->
    <div class="mb-6 md:mb-8">
        <h3 class="text-base md:text-lg font-semibold text-gray-800 mb-2 md:mb-3">Gerar Token</h3>
        <div id="newTokenContainer" class="hidden mb-4">
            <div class="bg-green-50 border border-green-200 rounded-lg p-3 md:p-4">
                <p class="text-xs md:text-sm text-green-700 font-semibold mb-2">
                    <i class="fas fa-exclamation-triangle mr-1"></i> Copie o token agora! Ele não será exibido novamente.
                </p>
                <div class="flex items-center gap-2">
                    <code id="newTokenValue" class="flex-1 bg-white border border-green-300 rounded px-3 py-2 text-xs md:text-sm font-mono text-gray-800 break-all"></code>
                    <button id="copyNewTokenBtn" class="bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded text-xs md:text-sm whitespace-nowrap">
                        <i class="fas fa-copy mr-1"></i> Copiar
                    </button>
                </div>
            </div>
        </div>

        <!-- Tokens Ativos -->
        <h3 class="text-base md:text-lg font-semibold text-gray-800 mb-2 md:mb-3">Tokens Ativos</h3>
        <div id="tokensList">
            @if($tokens->isEmpty())
                <p class="text-xs md:text-sm text-gray-500">Nenhum token gerado ainda.</p>
            @else
                <div class="space-y-2">
                    @foreach($tokens as $token)
                        <div class="flex items-center justify-between bg-gray-50 border border-gray-200 rounded-lg px-3 py-2" data-token-id="{{ $token->id }}">
                            <div class="flex-1">
                                <span class="text-sm font-medium text-gray-800">{{ $token->name }}</span>
                                <span class="text-xs text-gray-500 ml-2">Criado em {{ $token->created_at->format('d/m/Y H:i') }}</span>
                                @if($token->last_used_at)
                                    <span class="text-xs text-gray-400 ml-2">Ultimo uso: {{ $token->last_used_at->format('d/m/Y H:i') }}</span>
                                @endif
                            </div>
                            <button class="revoke-token-btn text-red-500 hover:text-red-700 text-xs md:text-sm ml-2" data-token-id="{{ $token->id }}">
                                <i class="fas fa-trash mr-1"></i> Revogar
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        <p class="text-xs text-gray-500 mt-2">Maximo de 5 tokens ativos. O token so aparece uma vez ao ser gerado.</p>
    </div>

    <!-- API Documentation -->
    <div class="mb-6 md:mb-8">
        <h3 class="text-lg md:text-xl font-bold text-gray-800 mb-3 md:mb-4">Documentacao da API</h3>

        <div class="space-y-4 md:space-y-6">
            <!-- Endpoint 1: Available -->
            <div class="border border-gray-200 rounded-lg p-3 md:p-4">
                <div class="flex items-start justify-between">
                    <div class="flex flex-wrap items-center">
                        <span class="api-method get-method">GET</span>
                        <span class="font-mono text-gray-800 text-sm md:text-base">/api/v1/proxies/available</span>
                    </div>
                    <button class="toggle-details text-blue-500 hover:text-blue-600 text-xs md:text-sm flex items-center">
                        <i class="fas fa-chevron-down mr-1"></i> Detalhes
                    </button>
                </div>
                <div class="mt-2 text-xs md:text-sm text-gray-600">
                    Retorna a quantidade de proxies disponíveis agrupados por país.
                </div>
                <div class="mt-2 details-content hidden">
                    <div class="bg-gray-50 p-2 md:p-3 rounded">
                        <h4 class="font-semibold text-xs md:text-sm mb-2">Exemplo de Resposta:</h4>
                        <pre class="text-xs bg-gray-100 p-2 rounded overflow-x-auto">{
    "success": true,
    "data": [
        { "country_code": "BR", "country_name": "Brasil", "available": 45 },
        { "country_code": "US", "country_name": "Estados Unidos", "available": 120 }
    ]
}</pre>
                    </div>
                </div>
            </div>

            <!-- Endpoint 2: Available Details -->
            <div class="border border-gray-200 rounded-lg p-3 md:p-4">
                <div class="flex items-start justify-between">
                    <div class="flex flex-wrap items-center">
                        <span class="api-method get-method">GET</span>
                        <span class="font-mono text-gray-800 text-sm md:text-base">/api/v1/proxies/available/details</span>
                    </div>
                    <button class="toggle-details text-blue-500 hover:text-blue-600 text-xs md:text-sm flex items-center">
                        <i class="fas fa-chevron-down mr-1"></i> Detalhes
                    </button>
                </div>
                <div class="mt-2 text-xs md:text-sm text-gray-600">
                    Lista todas as proxies disponíveis com dados completos (IP, porta, credenciais). Filtre por pais.
                </div>
                <div class="mt-2 details-content hidden">
                    <div class="bg-gray-50 p-2 md:p-3 rounded">
                        <h4 class="font-semibold text-xs md:text-sm mb-2">Parametros:</h4>
                        <ul class="text-xs space-y-1 mb-3">
                            <li><span class="font-mono">?country=BR</span> - Filtra por codigo do pais (opcional)</li>
                        </ul>
                        <h4 class="font-semibold text-xs md:text-sm mb-2">Exemplo de Resposta:</h4>
                        <pre class="text-xs bg-gray-100 p-2 rounded overflow-x-auto">{
    "success": true,
    "total": 2,
    "data": [
        {
            "id": 10,
            "type": "socks5",
            "country": "Brasil",
            "country_code": "BR"
        }
    ]
}</pre>
                    </div>
                </div>
            </div>

            <!-- Endpoint 3: Allocate -->
            <div class="border border-gray-200 rounded-lg p-3 md:p-4">
                <div class="flex items-start justify-between">
                    <div class="flex flex-wrap items-center">
                        <span class="api-method post-method">POST</span>
                        <span class="font-mono text-gray-800 text-sm md:text-base">/api/v1/proxies/allocate</span>
                    </div>
                    <button class="toggle-details text-blue-500 hover:text-blue-600 text-xs md:text-sm flex items-center">
                        <i class="fas fa-chevron-down mr-1"></i> Detalhes
                    </button>
                </div>
                <div class="mt-2 text-xs md:text-sm text-gray-600">
                    Aloca proxies para um usuario do CRM. O pagamento e feito no CRM, aqui so reserva a proxy.
                </div>
                <div class="mt-2 details-content hidden">
                    <div class="bg-gray-50 p-2 md:p-3 rounded">
                        <h4 class="font-semibold text-xs md:text-sm mb-2">Corpo da Requisicao:</h4>
                        <pre class="text-xs bg-gray-100 p-2 rounded overflow-x-auto mb-3">{
    "country": "BR",
    "quantity": 1,
    "period_days": 30,
    "reason": "Facebook",
    "crm_reference": "user_456"
}</pre>
                        <h4 class="font-semibold text-xs md:text-sm mb-2">Campos:</h4>
                        <ul class="text-xs space-y-1 mb-3">
                            <li><span class="font-mono">country</span> - Codigo do pais (BR, US, DE, etc.)</li>
                            <li><span class="font-mono">quantity</span> - Quantidade de proxies (1-100)</li>
                            <li><span class="font-mono">period_days</span> - Periodo em dias (30, 60, 90, 180, 360)</li>
                            <li><span class="font-mono">reason</span> - Motivo de uso (Facebook, Google, etc.)</li>
                            <li><span class="font-mono">crm_reference</span> - ID do usuario no CRM externo</li>
                        </ul>
                        <h4 class="font-semibold text-xs md:text-sm mb-2">Exemplo de Resposta:</h4>
                        <pre class="text-xs bg-gray-100 p-2 rounded overflow-x-auto">{
    "success": true,
    "proxies": [
        {
            "id": 456,
            "ip": "192.168.1.1",
            "port": "8080",
            "username": "user123",
            "password": "pass456",
            "type": "socks5",
            "country": "Brasil",
            "country_code": "BR",
            "expires_at": "2026-04-29T21:00:00+00:00"
        }
    ]
}</pre>
                    </div>
                </div>
            </div>

            <!-- Endpoint 4: Allocated -->
            <div class="border border-gray-200 rounded-lg p-3 md:p-4">
                <div class="flex items-start justify-between">
                    <div class="flex flex-wrap items-center">
                        <span class="api-method get-method">GET</span>
                        <span class="font-mono text-gray-800 text-sm md:text-base">/api/v1/proxies/allocated</span>
                    </div>
                    <button class="toggle-details text-blue-500 hover:text-blue-600 text-xs md:text-sm flex items-center">
                        <i class="fas fa-chevron-down mr-1"></i> Detalhes
                    </button>
                </div>
                <div class="mt-2 text-xs md:text-sm text-gray-600">
                    Lista todos os proxies alocados via CRM. Filtre por referencia do usuario.
                </div>
                <div class="mt-2 details-content hidden">
                    <div class="bg-gray-50 p-2 md:p-3 rounded">
                        <h4 class="font-semibold text-xs md:text-sm mb-2">Parametros:</h4>
                        <ul class="text-xs space-y-1 mb-3">
                            <li><span class="font-mono">?crm_reference=user_456</span> - Filtra por usuario do CRM</li>
                        </ul>
                        <h4 class="font-semibold text-xs md:text-sm mb-2">Exemplo de Resposta:</h4>
                        <pre class="text-xs bg-gray-100 p-2 rounded overflow-x-auto">{
    "success": true,
    "total": 2,
    "data": [
        {
            "id": 456,
            "ip": "192.168.1.1",
            "port": "8080",
            "username": "user123",
            "password": "pass456",
            "type": "socks5",
            "country": "Brasil",
            "country_code": "BR",
            "reason": "Facebook",
            "crm_reference": "user_456",
            "expires_at": "2026-04-29T21:00:00+00:00",
            "is_expired": false,
            "created_at": "2026-03-30T21:00:00+00:00"
        }
    ]
}</pre>
                    </div>
                </div>
            </div>

            <!-- Endpoint 5: Show -->
            <div class="border border-gray-200 rounded-lg p-3 md:p-4">
                <div class="flex items-start justify-between">
                    <div class="flex flex-wrap items-center">
                        <span class="api-method get-method">GET</span>
                        <span class="font-mono text-gray-800 text-sm md:text-base">/api/v1/proxies/:id</span>
                    </div>
                    <button class="toggle-details text-blue-500 hover:text-blue-600 text-xs md:text-sm flex items-center">
                        <i class="fas fa-chevron-down mr-1"></i> Detalhes
                    </button>
                </div>
                <div class="mt-2 text-xs md:text-sm text-gray-600">
                    Retorna os detalhes completos de um proxy específico.
                </div>
            </div>
        </div>
    </div>

    <!-- Authentication Section -->
    <div class="mb-6 md:mb-8">
        <h3 class="text-lg md:text-xl font-bold text-gray-800 mb-3 md:mb-4">Autenticacao</h3>
        <p class="text-xs md:text-sm text-gray-600 mb-2 md:mb-3">Inclua o token no cabecalho HTTP de todas as requisicoes:</p>

        <div class="bg-gray-50 p-2 md:p-3 rounded">
            <pre class="text-xs">Authorization: Bearer SEU_TOKEN_AQUI</pre>
        </div>
    </div>

    <!-- Rate Limits -->
    <div class="mb-6 md:mb-8">
        <h3 class="text-lg md:text-xl font-bold text-gray-800 mb-3 md:mb-4">Limites de Requisicao</h3>
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 md:p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-yellow-500 mt-1"></i>
                </div>
                <div class="ml-3">
                    <p class="text-xs md:text-sm text-yellow-700">
                        Limite de <strong>60 requisicoes por minuto</strong>. Requisicoes excedentes retornam status 429.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Code Examples -->
    <div>
        <h3 class="text-lg md:text-xl font-bold text-gray-800 mb-3 md:mb-4">Exemplos de Codigo</h3>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-6">
            <!-- PHP Example -->
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-800 text-white px-3 py-1 md:px-4 md:py-2 flex justify-between items-center">
                    <span class="text-xs md:text-sm font-medium">PHP (Laravel/CRM)</span>
                    <button class="text-gray-300 hover:text-white text-xs md:text-sm flex items-center copy-code-btn">
                        <i class="fas fa-copy mr-1"></i> Copiar
                    </button>
                </div>
                <div class="bg-gray-900 p-2 md:p-4">
                    <pre class="text-xs text-gray-300 overflow-x-auto">$response = Http::withToken(config('services.alfaproxy.token'))
    ->post('https://seudominio.com/api/v1/proxies/allocate', [
        'country' => 'BR',
        'quantity' => 1,
        'period_days' => 30,
        'reason' => 'Facebook',
        'crm_reference' => (string) $user->id,
    ]);

$data = $response->json();

if ($data['success']) {
    foreach ($data['proxies'] as $proxy) {
        // $proxy['ip'], $proxy['port'],
        // $proxy['username'], $proxy['password']
    }
}</pre>
                </div>
            </div>

            <!-- Python Example -->
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-800 text-white px-3 py-1 md:px-4 md:py-2 flex justify-between items-center">
                    <span class="text-xs md:text-sm font-medium">Python</span>
                    <button class="text-gray-300 hover:text-white text-xs md:text-sm flex items-center copy-code-btn">
                        <i class="fas fa-copy mr-1"></i> Copiar
                    </button>
                </div>
                <div class="bg-gray-900 p-2 md:p-4">
                    <pre class="text-xs text-gray-300 overflow-x-auto">import requests

url = "https://seudominio.com/api/v1/proxies/allocate"
headers = {
    "Authorization": "Bearer SEU_TOKEN_AQUI",
    "Content-Type": "application/json"
}
payload = {
    "country": "BR",
    "quantity": 1,
    "period_days": 30,
    "reason": "Facebook",
    "crm_reference": "user_456"
}

response = requests.post(url, json=payload, headers=headers)
data = response.json()

for proxy in data["proxies"]:
    print(f"{proxy['ip']}:{proxy['port']}")</pre>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Generate API Token (real backend call)
    const generateTokenBtn = document.getElementById('generateTokenBtn');
    const newTokenContainer = document.getElementById('newTokenContainer');
    const newTokenValue = document.getElementById('newTokenValue');
    const copyNewTokenBtn = document.getElementById('copyNewTokenBtn');

    generateTokenBtn.addEventListener('click', async () => {
        generateTokenBtn.disabled = true;
        generateTokenBtn.querySelector('span').textContent = 'Gerando...';

        try {
            const response = await fetch('{{ route("api.token.generate") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ name: 'CRM Token' }),
            });

            const data = await response.json();

            if (data.success) {
                newTokenValue.textContent = data.token;
                newTokenContainer.classList.remove('hidden');
                showAlert('Token gerado com sucesso! Copie-o agora.', 'success');
                setTimeout(() => location.reload(), 5000);
            } else {
                showAlert(data.error || 'Erro ao gerar token.', 'error');
            }
        } catch (error) {
            showAlert('Erro de conexao ao gerar token.', 'error');
        }

        generateTokenBtn.disabled = false;
        generateTokenBtn.querySelector('span').textContent = 'Gerar Novo Token';
    });

    // Copy new token
    copyNewTokenBtn.addEventListener('click', () => {
        navigator.clipboard.writeText(newTokenValue.textContent);
        copyNewTokenBtn.querySelector('i').className = 'fas fa-check mr-1';
        copyNewTokenBtn.lastChild.textContent = ' Copiado!';
        setTimeout(() => {
            copyNewTokenBtn.querySelector('i').className = 'fas fa-copy mr-1';
            copyNewTokenBtn.lastChild.textContent = ' Copiar';
        }, 2000);
    });

    // Revoke tokens
    document.querySelectorAll('.revoke-token-btn').forEach(btn => {
        btn.addEventListener('click', async () => {
            const tokenId = btn.dataset.tokenId;
            if (!confirm('Tem certeza que deseja revogar este token? Sistemas que o utilizam perderao acesso.')) return;

            try {
                const response = await fetch(`/api/token/${tokenId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                });

                const data = await response.json();

                if (data.success) {
                    btn.closest('[data-token-id]').remove();
                    showAlert('Token revogado com sucesso.', 'success');
                } else {
                    showAlert(data.error || 'Erro ao revogar token.', 'error');
                }
            } catch (error) {
                showAlert('Erro de conexao.', 'error');
            }
        });
    });

    // Toggle endpoint details
    document.querySelectorAll('.toggle-details').forEach(button => {
        button.addEventListener('click', () => {
            const card = button.closest('.border-gray-200');
            const details = card.querySelector('.details-content');
            if (!details) return;

            details.classList.toggle('hidden');
            const icon = button.querySelector('i');
            icon.classList.toggle('fa-chevron-down');
            icon.classList.toggle('fa-chevron-up');
        });
    });

    // Copy code examples
    document.querySelectorAll('.copy-code-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const codeBlock = btn.closest('.border-gray-200').querySelector('pre');
            navigator.clipboard.writeText(codeBlock.textContent);

            const icon = btn.querySelector('i');
            icon.className = 'fas fa-check mr-1';
            setTimeout(() => { icon.className = 'fas fa-copy mr-1'; }, 2000);
        });
    });

    function showAlert(message, type) {
        const alert = document.createElement('div');
        const bgClass = type === 'success' ? 'bg-green-500' : 'bg-red-500';
        const iconClass = type === 'success' ? 'fa-check-circle' : 'fa-times-circle';
        alert.className = `fixed bottom-4 right-4 ${bgClass} text-white px-4 py-2 rounded shadow-lg flex items-center z-50`;

        const icon = document.createElement('i');
        icon.className = `fas ${iconClass} mr-2`;
        alert.appendChild(icon);

        const span = document.createElement('span');
        span.textContent = message;
        alert.appendChild(span);

        document.body.appendChild(alert);
        setTimeout(() => {
            alert.classList.add('opacity-0', 'transition-opacity', 'duration-500');
            setTimeout(() => alert.remove(), 500);
        }, 4000);
    }
</script>

@endsection
