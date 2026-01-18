<?php

namespace App\Services;

use App\Models\Stock;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProxyRenewalService
{
    /**
     * Calcula o preço de renovação baseado no período e tipo de usuário
     */
    public function calculateRenewalPrice(User $user, int $periodo): float
    {
        return $user->getPrecoBase($periodo);
    }

    /**
     * Calcula a nova data de expiração baseado no status atual do proxy
     */
    public function calculateNewExpiration(Stock $proxy, int $diasAdicionais): Carbon
    {
        $expiracaoAtual = Carbon::parse($proxy->expiracao);

        // Se proxy já expirou, adiciona dias a partir de agora
        if ($expiracaoAtual->isPast()) {
            return Carbon::now()->addDays($diasAdicionais);
        }

        // Se proxy ainda está ativo, adiciona dias à data atual de expiração
        return $expiracaoAtual->addDays($diasAdicionais);
    }

    /**
     * Verifica se o proxy pode ser renovado pelo usuário
     */
    public function canRenewProxy(Stock $proxy, User $user): bool
    {
        // Proxy deve pertencer ao usuário
        if ($proxy->user_id !== $user->id) {
            Log::warning('Tentativa de renovar proxy de outro usuário', [
                'proxy_id' => $proxy->id,
                'proxy_owner' => $proxy->user_id,
                'attempted_by' => $user->id,
            ]);
            return false;
        }

        // Proxy não pode ser de uso interno
        if ($proxy->uso_interno ?? false) {
            Log::warning('Tentativa de renovar proxy de uso interno', [
                'proxy_id' => $proxy->id,
                'user_id' => $user->id,
            ]);
            return false;
        }

        return true;
    }

    /**
     * Desbloqueia proxy via API Python se estiver bloqueado
     * Retorna true se conseguiu desbloquear ou se já estava desbloqueado
     */
    public function unblockProxyIfBlocked(Stock $proxy): bool
    {
        // Se não está bloqueado, não precisa fazer nada
        if (!$proxy->bloqueada) {
            Log::info('Proxy não estava bloqueado, pulando desbloqueio', [
                'proxy_id' => $proxy->id,
            ]);
            return true;
        }

        // Verificar se tem VPS associada
        if (!$proxy->vps) {
            Log::error('VPS não encontrada para desbloquear proxy', [
                'proxy_id' => $proxy->id,
                'porta' => $proxy->porta,
            ]);
            return false;
        }

        try {
            $vps = $proxy->vps;

            // Preparar payload para a API Python
            $payload = [
                'ip_vps' => $vps->ip,
                'user_ssh' => $vps->usuario_ssh,
                'senha_ssh' => $vps->senha_ssh,
                'porta' => $proxy->porta,
            ];

            // Chamar API Python para desbloquear a porta
            $pythonApiUrl = config('services.python_api.url', 'http://127.0.0.1:8001');
            $response = Http::timeout(30)->post("{$pythonApiUrl}/desbloquear", $payload);

            if ($response->successful()) {
                Log::info('Proxy desbloqueado com sucesso via API', [
                    'proxy_id' => $proxy->id,
                    'porta' => $proxy->porta,
                    'vps_ip' => $vps->ip,
                ]);
                return true;
            }

            Log::error('Erro ao desbloquear proxy via API', [
                'proxy_id' => $proxy->id,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);
            return false;

        } catch (\Exception $e) {
            Log::error('Exceção ao desbloquear proxy', [
                'proxy_id' => $proxy->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Renova o proxy: estende expiração + desbloqueia via API se necessário
     *
     * @throws \Exception se falhar ao desbloquear via API
     */
    public function renewProxy(Stock $proxy, int $diasAdicionais): Stock
    {
        $expiracaoAntiga = $proxy->expiracao;
        $estavaBlocqueado = $proxy->bloqueada;
        $novaExpiracao = $this->calculateNewExpiration($proxy, $diasAdicionais);

        // Se estava bloqueado, desbloquear via API ANTES de atualizar o banco
        if ($estavaBlocqueado) {
            Log::info('Proxy estava bloqueado, tentando desbloquear via API', [
                'proxy_id' => $proxy->id,
            ]);

            $desbloqueadoComSucesso = $this->unblockProxyIfBlocked($proxy);

            if (!$desbloqueadoComSucesso) {
                throw new \Exception('Falha ao desbloquear proxy via API Python. A renovação não pode ser concluída.');
            }
        }

        // Atualizar proxy no banco de dados
        $proxy->update([
            'expiracao' => $novaExpiracao,
            'bloqueada' => false,
            'periodo_dias' => $diasAdicionais,
        ]);

        Log::info('Proxy renovado com sucesso', [
            'proxy_id' => $proxy->id,
            'proxy_ip' => $proxy->ip,
            'proxy_porta' => $proxy->porta,
            'expiracao_anterior' => $expiracaoAntiga,
            'expiracao_nova' => $novaExpiracao->format('Y-m-d H:i:s'),
            'dias_adicionados' => $diasAdicionais,
            'estava_bloqueado' => $estavaBlocqueado,
            'foi_desbloqueado_api' => $estavaBlocqueado,
        ]);

        return $proxy->fresh();
    }

    /**
     * Formata informações do proxy para exibição no modal
     */
    public function getProxyRenewalInfo(Stock $proxy, int $diasAdicionais): array
    {
        $expiracaoAtual = Carbon::parse($proxy->expiracao);
        $novaExpiracao = $this->calculateNewExpiration($proxy, $diasAdicionais);
        $estaExpirado = $expiracaoAtual->isPast();

        return [
            'proxy_id' => $proxy->id,
            'proxy_endereco' => "{$proxy->ip}:{$proxy->porta}",
            'pais' => $proxy->pais,
            'expiracao_atual' => $expiracaoAtual->format('d/m/Y H:i'),
            'expiracao_nova' => $novaExpiracao->format('d/m/Y H:i'),
            'dias_adicionais' => $diasAdicionais,
            'esta_expirado' => $estaExpirado,
            'esta_bloqueado' => $proxy->bloqueada,
            'status_texto' => $estaExpirado ? 'Expirado' : 'Ativo',
        ];
    }
}