# Alfa Proxy

Sistema de gerenciamento e venda de proxies SOCKS5 desenvolvido com Laravel 12.

## Requisitos

- PHP 8.2+
- Composer
- Node.js & NPM
- MySQL/MariaDB
- Python 3.8+ (para API de geração de proxies)

## Instalação

1. Clone o repositório e instale as dependências:

```bash
composer install
npm install
```

2. Configure o arquivo `.env`:

```bash
cp .env.example .env
php artisan key:generate
```

3. Configure o banco de dados no `.env` e execute as migrations:

```bash
php artisan migrate
```

4. Compile os assets:

```bash
npm run build
```

## Executando o Sistema

### Ambiente de Desenvolvimento

Use o comando `composer dev` para iniciar todos os serviços necessários:

```bash
composer dev
```

Este comando inicia automaticamente:
- **Laravel Server** (http://localhost:8000)
- **Queue Worker** (para processar jobs em background)
- **Vite Dev Server** (para hot reload dos assets)

### Executando Serviços Individualmente

Se preferir executar cada serviço separadamente:

```bash
# Terminal 1: Laravel Server
php artisan serve

# Terminal 2: Queue Worker
php artisan queue:work --timeout=0 --tries=3

# Terminal 3: Vite Dev Server
npm run dev
```

## Sistema de Geração de Proxies

### Requisitos

1. **API Python FastAPI**: O sistema precisa de uma API Python rodando para gerar as proxies via Dante SOCKS5
2. Configure a URL da API Python no `.env`:

```env
PYTHON_API_URL=http://127.0.0.1:8001
```

**IMPORTANTE**: A API Python deve rodar em uma porta diferente do Laravel (recomendado: 8001)

### Como Funciona

1. **Admin cadastra VPS**: No painel admin, ao cadastrar uma nova VPS, marque a opção "Rodar script de geração"
2. **Job é enfileirado**: O sistema cria um job em background para não travar a interface
3. **Processamento assíncrono**: O queue worker processa o job, conecta na VPS via SSH e gera as proxies
4. **Monitoramento em tempo real**: O admin pode acompanhar o status da geração na tela de proxies

### Rodando o Queue Worker

O queue worker é **ESSENCIAL** para processar os jobs de geração de proxies. Sem ele rodando, as proxies não serão geradas.

**Opção 1: Via composer dev (recomendado)**
```bash
composer dev
```

**Opção 2: Manualmente**
```bash
php artisan queue:work --timeout=0 --tries=3
```

**Opção 3: Via queue:listen (para desenvolvimento)**
```bash
php artisan queue:listen --tries=1
```

### Diferenças entre queue:work e queue:listen

- **`queue:work`**: Mais eficiente, mantém a aplicação em memória. Requer restart após mudanças no código.
- **`queue:listen`**: Reinicia a cada job, detecta mudanças automaticamente, mas é mais lento.

### Monitoramento de Jobs

**Ver logs em tempo real:**
```bash
php artisan pail
```

**Ver status da fila:**
```bash
php artisan queue:monitor
```

**Limpar jobs falhados:**
```bash
php artisan queue:flush
```

**Retentar jobs falhados:**
```bash
php artisan queue:retry all
```

### Status da Geração

O sistema rastreia o status de cada geração:

- **pending**: Na fila, aguardando processamento
- **processing**: Gerando proxies (conectando na VPS)
- **completed**: Proxies geradas com sucesso
- **failed**: Erro durante a geração

### Troubleshooting

**Job não está processando?**
1. Verifique se o queue worker está rodando
2. Verifique os logs: `storage/logs/laravel.log`
3. Verifique a tabela `jobs` no banco de dados

**Erro 404 ao chamar API Python?**
1. Verifique se a API Python está rodando
2. Verifique se a porta está correta no `.env`
3. Laravel usa porta 8000 por padrão, use 8001 para Python

**Job falha mesmo com Python API rodando?**
1. Verifique as credenciais SSH da VPS
2. Verifique se o servidor VPS está acessível
3. Verifique os logs detalhados com `php artisan pail`

## API Python para Geração de Proxies

A API Python deve implementar o endpoint:

```
POST /criar
Content-Type: application/json

{
  "ip": "72.60.50.149",
  "user": "root",
  "senha": "senha_ssh"
}

Resposta esperada:
{
  "proxies": [
    {
      "ip": "72.60.50.149",
      "porta": 1080,
      "usuario": "user1",
      "senha": "pass1"
    }
  ]
}

Ou simplesmente um array direto:
[
  {
    "ip": "72.60.50.149",
    "porta": 1080,
    "usuario": "user1",
    "senha": "pass1"
  }
]
```

### Iniciar API Python

```bash
# Exemplo com uvicorn (FastAPI)
uvicorn main:app --host 127.0.0.1 --port 8001
```

## Testing

```bash
composer test
```

## Code Quality

```bash
# Formatar código
./vendor/bin/pint

# Ver logs em tempo real
php artisan pail
```

## Estrutura do Projeto

- **app/Jobs/GerarProxiesJob.php**: Job para geração de proxies em background
- **app/Http/Controllers/AdminController.php**: Controle do painel admin
- **app/Models/Vps.php**: Model de VPS
- **app/Models/Stock.php**: Model de proxies em estoque
- **routes/api.php**: Rotas da API (inclui endpoint de status de geração)

## Licença

MIT
