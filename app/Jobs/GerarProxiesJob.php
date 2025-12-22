<?php

namespace App\Jobs;

use App\Models\Vps;
use App\Models\Stock;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GerarProxiesJob implements ShouldQueue
{
    use Queueable;

    /**
     * Número de tentativas de reprocessamento em caso de falha
     */
    public $tries = 1;

    /**
     * Tempo máximo de execução em segundos (30 minutos)
     */
    public $timeout = 1800;

    /**
     * Dados da VPS
     */
    protected $vps;
    protected $perioDias;
    protected $userId;

    /**
     * Create a new job instance.
     */
    public function __construct(Vps $vps, int $perioDias, int $userId)
    {
        $this->vps = $vps;
        $this->perioDias = $perioDias;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Iniciando geração de proxies para VPS', [
            'vps_id' => $this->vps->id,
            'vps_ip' => $this->vps->ip,
        ]);

        try {
            // Atualizar status para "processing"
            $this->vps->update(['status_geracao' => 'processing']);

            // Chamar API Python
            $apiUrl = config('services.python_api.url');

            // Log dos dados que serão enviados (sem senha por segurança)
            Log::info('Enviando requisição para API Python', [
                'vps_id' => $this->vps->id,
                'api_url' => "{$apiUrl}/criar",
                'payload' => [
                    'ip' => $this->vps->ip,
                    'user' => $this->vps->usuario_ssh,
                ],
            ]);

            $response = Http::timeout(900)->post("{$apiUrl}/criar", [
                'ip' => $this->vps->ip,
                'user' => $this->vps->usuario_ssh,
                'senha' => $this->vps->senha_ssh,
            ]);

            // Log da resposta recebida
            Log::info('Resposta recebida da API Python', [
                'vps_id' => $this->vps->id,
                'status_code' => $response->status(),
                'response_body' => $response->body(),
            ]);

            // Tentar decodificar JSON
            try {
                $jsonData = $response->json();

            } catch (\Exception $jsonException) {
                Log::error('Erro ao decodificar JSON', [
                    'vps_id' => $this->vps->id,
                    'error' => $jsonException->getMessage(),
                    'raw_body' => $response->body(),
                ]);
            }

            if ($response->successful()) {
                $data = $response->json();

                
                // A API pode retornar array direto ou {'proxies': [...]}
                $proxies = is_array($data) && !isset($data['proxies'])
                    ? $data
                    : ($data['proxies'] ?? []);

                

                $proxiesCriadas = 0;

                // Cadastrar cada proxy na tabela stocks
                foreach ($proxies as $proxy) {
                    Stock::create([
                        'user_id' => null, // Proxies gerados ficam disponíveis no estoque
                        'vps_id' => $this->vps->id,
                        'tipo' => 'SOCKS5',
                        'ip' => $proxy['ip'],
                        'porta' => $proxy['porta'],
                        'usuario' => $proxy['usuario'],
                        'senha' => $proxy['senha'],
                        'pais' => $this->vps->pais,
                        'expiracao' => null, // Expiração definida quando for vendido
                        'disponibilidade' => true,
                    ]);
                    $proxiesCriadas++;
                }

                // Atualizar VPS com status de sucesso
                $this->vps->update([
                    'status_geracao' => 'completed',
                    'proxies_geradas' => $proxiesCriadas,
                    'erro_geracao' => null,
                ]);

              
            } else {
                // API retornou erro ou status HTTP não-sucesso
                $statusCode = $response->status();
                $responseBody = $response->body();

                // Tentar extrair mensagem de erro do JSON
                $errorMessage = 'Erro desconhecido ao gerar proxies';
                try {
                    $jsonResponse = $response->json();
                    if (is_array($jsonResponse) && isset($jsonResponse['detail'])) {
                        $errorMessage = $jsonResponse['detail'];
                    } elseif (is_array($jsonResponse) && isset($jsonResponse['error'])) {
                        $errorMessage = $jsonResponse['error'];
                    } elseif (is_array($jsonResponse) && isset($jsonResponse['message'])) {
                        $errorMessage = $jsonResponse['message'];
                    }
                } catch (\Exception $e) {
                    // Se não conseguir decodificar JSON, usar corpo bruto
                    if (!empty($responseBody) && strlen($responseBody) < 500) {
                        $errorMessage = "HTTP {$statusCode}: {$responseBody}";
                    } else {
                        $errorMessage = "HTTP {$statusCode}: Resposta não-JSON ou muito longa";
                    }
                }

                $this->vps->update([
                    'status_geracao' => 'failed',
                    'erro_geracao' => $errorMessage,
                ]);

                Log::error('Erro na API Python ao gerar proxies', [
                    'vps_id' => $this->vps->id,
                    'status_code' => $statusCode,
                    'error' => $errorMessage,
                    'response_body_preview' => substr($responseBody, 0, 500),
                ]);

                throw new \Exception($errorMessage);
            }

        } catch (\Exception $e) {
            // Registrar erro
            $this->vps->update([
                'status_geracao' => 'failed',
                'erro_geracao' => $e->getMessage(),
            ]);

            Log::error('Exceção ao gerar proxies', [
                'vps_id' => $this->vps->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Re-lançar exceção para Laravel tentar novamente (se ainda houver tentativas)
            throw $e;
        }
    }

    /**
     * O que fazer quando o job falhar definitivamente (após todas as tentativas)
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Job de geração de proxies falhou definitivamente', [
            'vps_id' => $this->vps->id,
            'error' => $exception->getMessage(),
        ]);

        $this->vps->update([
            'status_geracao' => 'failed',
            'erro_geracao' => 'Falha após múltiplas tentativas: ' . $exception->getMessage(),
        ]);
    }
}
