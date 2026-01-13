# Sistema de Expiração Automática de Proxies

Sistema robusto e de alta performance para verificar e bloquear automaticamente proxies expirados via API Python.

## 📋 Visão Geral

O sistema implementa:

1. **Job Otimizado** - `BlockExpiredProxy` - Processa bloqueio de um proxy via API
2. **Comando Artisan** - `proxies:check-expired` - Verifica proxies expirados e enfileira jobs
3. **Scheduler Automático** - Executa verificação a cada 5 minutos
4. **Fila de Alta Performance** - Queue dedicada com batching e retry inteligente

## 🚀 Como Funciona

### Fluxo de Processamento

```
┌─────────────────────────────────────────────────────────────┐
│ Scheduler (a cada 5 minutos)                                │
│ php artisan schedule:work                                    │
└─────────────────┬───────────────────────────────────────────┘
                  │
                  ▼
┌─────────────────────────────────────────────────────────────┐
│ Comando: proxies:check-expired                              │
│ - Busca proxies com expiracao <= NOW                        │
│ - Filtra apenas não bloqueados                              │
│ - Cria jobs em batches de 100                               │
└─────────────────┬───────────────────────────────────────────┘
                  │
                  ▼
┌─────────────────────────────────────────────────────────────┐
│ Fila: expiration (4 workers processando)                    │
│ - Jobs executados em paralelo                               │
│ - Retry automático (3 tentativas)                           │
│ - Backoff de 5 segundos entre tentativas                    │
└─────────────────┬───────────────────────────────────────────┘
                  │
                  ▼
┌─────────────────────────────────────────────────────────────┐
│ Job: BlockExpiredProxy                                       │
│ 1. Busca proxy + VPS                                        │
│ 2. Valida se ainda precisa bloquear                         │
│ 3. Chama API Python: POST /bloquear                         │
│ 4. Atualiza campo 'bloqueada' = true                        │
│ 5. Registra logs detalhados                                 │
└─────────────────────────────────────────────────────────────┘
```

## 🛠️ Configuração

### 1. Iniciar Queue Workers

**Windows:**
```bash
# Método 1: Script automatizado (inicia 6 workers)
start-queue-workers.bat

# Método 2: Manual
php artisan queue:work --queue=expiration --sleep=1 --tries=3 --timeout=120
```

**Linux (Supervisor):**
```bash
# Copiar configuração
sudo cp queue-workers.conf /etc/supervisor/conf.d/alfa-proxy-queue.conf

# Recarregar supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start alfa-proxy-queue-expiration:*
```

### 2. Iniciar Scheduler

O scheduler deve rodar continuamente em background:

```bash
php artisan schedule:work
```

**Ou via Cron (Linux):**
```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

**Windows Task Scheduler:**
- Criar tarefa que executa a cada 1 minuto
- Comando: `php C:\laragon\www\alfa-proxy\artisan schedule:run`

## 📊 Comandos Disponíveis

### Verificar Proxies Expirados (Manual)

```bash
# Execução padrão (batch de 100)
php artisan proxies:check-expired

# Batch maior para processar muitos proxies rapidamente
php artisan proxies:check-expired --batch-size=500

# Forçar reprocessamento de proxies já bloqueados
php artisan proxies:check-expired --force
```

### Monitorar Filas

```bash
# Ver jobs pendentes
php artisan queue:monitor expiration

# Limpar jobs falhados
php artisan queue:flush

# Retentar jobs falhados
php artisan queue:retry all
```

### Verificar Scheduler

```bash
# Listar tarefas agendadas
php artisan schedule:list

# Testar execução do scheduler
php artisan schedule:test
```

## ⚡ Otimizações de Performance

### 1. Batching Inteligente
- Jobs agrupados em batches de 100 (configurável)
- Reduz overhead de enfileiramento
- Permite cancelamento em massa se necessário

### 2. Queue Dedicada
- Fila `expiration` separada da `default`
- 4 workers dedicados para processar bloqueios
- Priorização de proxies expirados

### 3. Chunked Processing
- Query processa 1000 registros por vez
- Evita memory overflow em grandes volumes
- Mantém performance mesmo com 10k+ proxies

### 4. Retry Inteligente
- 3 tentativas automáticas por job
- Backoff exponencial (5 segundos)
- Marca como bloqueado após falhas para evitar loop

### 5. Validações de Segurança
- Verifica se proxy já está bloqueado antes de processar
- Suporta cancelamento de batches
- Deleta job se modelo não existir mais

## 📈 Métricas e Logs

### Logs Gerados

O sistema registra eventos em `storage/logs/laravel.log`:

```php
// Sucesso
[INFO] Proxy expirado bloqueado automaticamente
[INFO] Verificação de proxies expirados concluída

// Avisos
[WARNING] Proxy não encontrado para bloqueio

// Erros
[ERROR] Erro ao bloquear proxy expirado via API
[ERROR] VPS não encontrada para proxy expirado

// Críticos
[CRITICAL] Proxy marcado como bloqueado após múltiplas falhas
[CRITICAL] Job de bloqueio de proxy falhou completamente
```

### Monitoramento

Use o Laravel Horizon (opcional) para dashboard visual:

```bash
composer require laravel/horizon
php artisan horizon:install
php artisan horizon
```

Acesse: `http://localhost/horizon`

## 🔍 Troubleshooting

### Jobs não estão sendo processados

```bash
# Verificar se workers estão rodando
php artisan queue:work --queue=expiration --sleep=1 --tries=3

# Verificar tabela de jobs
# Deve ter registros com queue = 'expiration'
```

### Scheduler não está executando

```bash
# Verificar se schedule:work está rodando
# No Windows, procurar na task manager por "php.exe"

# Testar manualmente
php artisan proxies:check-expired
```

### API Python não responde

```bash
# Verificar configuração da URL
# Em config/services.php ou .env
PYTHON_API_URL=http://127.0.0.1:8001

# Testar API manualmente
curl -X POST http://127.0.0.1:8001/bloquear \
  -H "Content-Type: application/json" \
  -d '{"ip_vps":"1.2.3.4","user_ssh":"root","senha_ssh":"pass","porta":1080}'
```

### Muitos jobs falhando

```bash
# Ver jobs falhados
php artisan queue:failed

# Limpar fila de jobs falhados
php artisan queue:flush

# Aumentar timeout e tentativas
# Editar BlockExpiredProxy.php:
public int $tries = 5;
public int $timeout = 120;
```

## 🎯 Capacidade do Sistema

Com a configuração atual:

- **4 workers** processando simultaneamente
- **Timeout de 120s** por job
- **~30 proxies/minuto** por worker

**Capacidade Total:** ~120 proxies/minuto = 7.200 proxies/hora

Para aumentar:
- Aumentar número de workers (8, 12, 16...)
- Usar Redis ao invés de database queue
- Implementar circuit breaker para API Python

## 📝 Estrutura de Arquivos

```
app/
├── Jobs/
│   └── BlockExpiredProxy.php          # Job que bloqueia proxy via API
├── Console/
│   └── Commands/
│       └── CheckExpiredProxies.php    # Comando de verificação
routes/
└── console.php                         # Scheduler configurado aqui

# Arquivos de configuração
queue-workers.conf                      # Supervisor config (Linux)
start-queue-workers.bat                 # Script Windows
EXPIRATION_SYSTEM.md                    # Esta documentação
```

## 🚦 Status do Sistema

Para verificar se tudo está funcionando:

```bash
# 1. Workers ativos?
ps aux | grep "queue:work"  # Linux
tasklist | findstr php.exe  # Windows

# 2. Scheduler rodando?
ps aux | grep "schedule:work"  # Linux

# 3. Jobs sendo processados?
php artisan queue:monitor expiration

# 4. Últimas execuções
tail -f storage/logs/laravel.log | grep "expirado"
```

## 🔐 Segurança

- Jobs só processam proxies com `bloqueada = false`
- Validação de existência antes de bloquear
- Timeouts para prevenir travamentos
- Retry limit para evitar loops infinitos
- Logs detalhados para auditoria

## 📞 Suporte

Em caso de problemas:
1. Verificar logs em `storage/logs/laravel.log`
2. Verificar jobs falhados: `php artisan queue:failed`
3. Verificar se API Python está online
4. Verificar se workers estão rodando
