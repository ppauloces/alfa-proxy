<?php

namespace App\Jobs;

use App\Models\Stock;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Bus\Batchable;

class BlockExpiredProxy implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels, Batchable;

    /**
     * O ID do proxy a ser bloqueado
     */
    public int $stockId;

    /**
     * Número de tentativas
     */
    public int $tries = 3;

    /**
     * Tempo de timeout (em segundos)
     */
    public int $timeout = 60;

    /**
     * Tempo entre tentativas (em segundos)
     */
    public int $backoff = 5;

    /**
     * Determina se o job deve ser deletado quando o modelo não existir mais
     */
    public bool $deleteWhenMissingModels = true;

    /**
     * Create a new job instance.
     */
    public function __construct(int $stockId)
    {
        $this->stockId = $stockId;

        // Define fila de alta prioridade para expiração
        $this->onQueue('expiration');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Se faz parte de um batch e o batch foi cancelado, pula execução
        if ($this->batch()?->cancelled()) {
            return;
        }

        // Buscar o proxy com VPS
        $stock = Stock::with('vps')->find($this->stockId);

        // Se não existe mais ou já está bloqueado, pula
        if (!$stock) {
            Log::warning('Proxy não encontrado para bloqueio', ['stock_id' => $this->stockId]);
            return;
        }

        if ($stock->bloqueada) {
            Log::info('Proxy já estava bloqueado, pulando', ['stock_id' => $this->stockId]);
            return;
        }

        // Verificar se tem VPS associada
        if (!$stock->vps) {
            Log::error('VPS não encontrada para proxy expirado', [
                'stock_id' => $stock->id,
                'porta' => $stock->porta,
            ]);

            // Marca como bloqueado mesmo sem VPS para não tentar novamente
            $stock->update(['bloqueada' => true]);
            return;
        }

        try {
            $vps = $stock->vps;

            // Preparar payload para a API Python
            $payload = [
                'ip_vps' => $vps->ip,
                'user_ssh' => $vps->usuario_ssh,
                'senha_ssh' => $vps->senha_ssh,
                'porta' => $stock->porta,
            ];

            // Chamar API Python para bloquear a porta
            $pythonApiUrl = config('services.python_api.url', 'http://127.0.0.1:8001');
            $response = Http::timeout(30)->post("{$pythonApiUrl}/bloquear", $payload);

            if ($response->successful()) {
                // Atualizar status no banco de dados
                $stock->update(['bloqueada' => true]);

                Log::info('Proxy expirado bloqueado automaticamente', [
                    'stock_id' => $stock->id,
                    'porta' => $stock->porta,
                    'vps_ip' => $vps->ip,
                    'expiracao' => $stock->expiracao,
                ]);
            } else {
                Log::error('Erro ao bloquear proxy expirado via API', [
                    'stock_id' => $stock->id,
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);

                // Lança exceção para retentar
                throw new \Exception('Falha na API Python: ' . $response->status());
            }

        } catch (\Exception $e) {
            Log::error('Exceção ao bloquear proxy expirado', [
                'stock_id' => $stock->id,
                'error' => $e->getMessage(),
                'tentativa' => $this->attempts(),
            ]);

            // Se não é a última tentativa, relança a exceção para retry
            if ($this->attempts() < $this->tries) {
                throw $e;
            }

            // Na última tentativa, marca como bloqueado no banco mesmo com erro na API
            // para evitar loop infinito de tentativas
            $stock->update(['bloqueada' => true]);

            Log::critical('Proxy marcado como bloqueado após múltiplas falhas', [
                'stock_id' => $stock->id,
                'tentativas' => $this->attempts(),
            ]);
        }
    }

    /**
     * Chamado quando o job falha
     */
    public function failed(\Throwable $exception): void
    {
        Log::critical('Job de bloqueio de proxy falhou completamente', [
            'stock_id' => $this->stockId,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
