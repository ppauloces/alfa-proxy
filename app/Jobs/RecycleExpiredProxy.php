<?php

namespace App\Jobs;

use App\Models\Stock;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecycleExpiredProxy implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels, Batchable;

    public int $stockId;
    public int $tries = 3;
    public int $timeout = 240;
    public int $backoff = 10;
    public bool $deleteWhenMissingModels = true;

    public function __construct(int $stockId)
    {
        $this->stockId = $stockId;
        $this->onQueue('recycling');
    }

    public function handle(): void
    {
        if ($this->batch()?->cancelled()) {
            return;
        }

        $stock = Stock::with('vps')->find($this->stockId);

        if (!$stock) {
            Log::warning('Stock nao encontrado para reciclagem', ['stock_id' => $this->stockId]);
            return;
        }

        if (!$stock->bloqueada || !$stock->user_id) {
            Log::info('Stock nao elegivel para reciclagem (ja reciclado ou nao bloqueado)', [
                'stock_id' => $stock->id,
            ]);
            return;
        }

        if (!$stock->vps) {
            Log::error('VPS nao encontrada ao reciclar proxy', ['stock_id' => $stock->id]);
            return;
        }

        $vps = $stock->vps;

        // Fail-fast: confirma que o SSH realmente responde (banner handshake) em 3s.
        // Apenas TCP open nao basta - VPSs zumbi tem porta aberta mas sshd congelado.
        $sshOpen = false;
        foreach ([22, 22022] as $sshPort) {
            $sock = @fsockopen($vps->ip, $sshPort, $errno, $errstr, 3);
            if (!$sock) {
                continue;
            }
            stream_set_timeout($sock, 3);
            $banner = fgets($sock, 256);
            $meta = stream_get_meta_data($sock);
            fclose($sock);
            if ($banner && !$meta['timed_out'] && stripos($banner, 'SSH-') === 0) {
                $sshOpen = true;
                break;
            }
        }
        if (!$sshOpen) {
            Log::warning('VPS offline ou SSH congelado - pulando reciclagem', [
                'stock_id' => $stock->id,
                'vps_ip' => $vps->ip,
            ]);
            $this->release(3600); // tenta de novo em 1h
            return;
        }

        $pythonApiUrl = config('services.python_api.url', 'http://127.0.0.1:8001');

        $payload = [
            'ip_vps'        => $vps->ip,
            'user_ssh'      => $vps->usuario_ssh,
            'senha_ssh'     => $vps->senha_ssh,
            'usuario_proxy' => $stock->usuario,
            'porta'         => $stock->porta,
        ];

        $response = Http::timeout(180)->post("{$pythonApiUrl}/reciclar", $payload);

        if (!$response->successful()) {
            Log::error('Falha ao reciclar proxy via API Python', [
                'stock_id' => $stock->id,
                'status'   => $response->status(),
                'body'     => $response->body(),
            ]);
            throw new \Exception("API /reciclar retornou {$response->status()}");
        }

        $novaSenha = $response->json('nova_senha') ?? $stock->senha;

        DB::transaction(function () use ($stock, $novaSenha) {
            $stock->update([
                'senha'                => $novaSenha,
                'user_id'              => null,
                'disponibilidade'      => true,
                'bloqueada'            => false,
                'substituido'          => false,
                'substituido_por'      => null,
                'expiracao'            => null,
                'periodo_dias'         => null,
                'motivo_uso'           => null,
                'renovacao_automatica' => false,
                'recycled_at'          => now(),
                'recycling_notified_at'=> null,
            ]);
        });

        Log::info('Proxy reciclada automaticamente e devolvida ao estoque', [
            'stock_id' => $stock->id,
            'porta'    => $stock->porta,
            'vps_ip'   => $vps->ip,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::critical('Job de reciclagem de proxy falhou completamente', [
            'stock_id' => $this->stockId,
            'error'    => $exception->getMessage(),
        ]);
    }
}
