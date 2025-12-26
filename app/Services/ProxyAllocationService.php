<?php

namespace App\Services;

use App\Models\Stock;
use App\Models\Vps;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ProxyAllocationService
{
    /**
     * Aloca proxies EXISTENTES do estoque de forma randomizada entre DIFERENTES VPS
     * Pega proxies disponíveis no estoque e vincula ao usuário
     *
     * @param int $userId
     * @param array $purchaseData [pais, quantidade, periodo_dias, motivo]
     * @return array Array de proxies alocados
     */
    public function allocateProxies(int $userId, array $purchaseData): array
    {
        $paisCodigo = $purchaseData['pais'];
        $quantidade = $purchaseData['quantidade'];
        $periodoDias = $purchaseData['periodo_dias'];
        $motivo = $purchaseData['motivo'];

        // Converter código do país para nome completo
        $paisNome = $this->getPaisNome($paisCodigo);

        // Buscar proxies DISPONÍVEIS no estoque do país solicitado
        // Proxies disponíveis = user_id é NULL (não vendidas ainda) e disponibilidade = true
        // EXCLUIR proxies de uso interno
        $proxiesDisponiveis = Stock::whereNull('user_id')
            ->where('disponibilidade', true)
            ->where('uso_interno', false)
            ->where('pais', $paisNome)
            ->inRandomOrder() // Randomiza a ordem
            ->limit($quantidade)
            ->get();

        // Verificar se há proxies suficientes em estoque
        if ($proxiesDisponiveis->count() < $quantidade) {
            throw new \Exception(sprintf(
                "Estoque insuficiente! Solicitado: %d proxies. Disponível: %d proxies do país %s.",
                $quantidade,
                $proxiesDisponiveis->count(),
                $paisNome
            ));
        }

        $proxiesAlocados = [];
        $expiracao = Carbon::now()->addDays($periodoDias);

        // Atualizar cada proxy para vincular ao usuário
        foreach ($proxiesDisponiveis as $proxy) {
            $proxy->update([
                'user_id' => $userId,
                'codigo_pais' => $paisCodigo,
                'motivo_uso' => $motivo,
                'periodo_dias' => $periodoDias,
                'expiracao' => $expiracao,
                'disponibilidade' => false, // Marcar como vendida
                'renovacao_automatica' => false,
            ]);

            $proxiesAlocados[] = $proxy->fresh(); // Recarregar para pegar dados atualizados
        }

        return $proxiesAlocados;
    }

    /**
     * Gera credenciais únicas para um proxy
     *
     * @param Vps $vps
     * @param string $pais
     * @return array
     */
    private function generateProxyCredentials(Vps $vps, string $pais): array
    {
        // Gera porta entre 10000 e 65000
        $porta = rand(10000, 65000);

        // Gera usuário único (prefixo do país + random)
        $usuario = strtolower($pais) . '_' . Str::random(8);

        // Gera senha forte
        $senha = Str::random(16);

        return [
            'porta' => $porta,
            'usuario' => $usuario,
            'senha' => $senha,
        ];
    }

    /**
     * Retorna o nome completo do país baseado no código
     *
     * @param string $codigo
     * @return string
     */
    private function getPaisNome(string $codigo): string
    {
        $paises = [
            'BR' => 'Brasil',
            'US' => 'Estados Unidos',
            'UK' => 'Reino Unido',
            'DE' => 'Alemanha',
            'FR' => 'França',
            'ES' => 'Espanha',
            'IT' => 'Itália',
            'PT' => 'Portugal',
            'CA' => 'Canadá',
            'AU' => 'Austrália',
        ];

        return $paises[$codigo] ?? $codigo;
    }

    /**
     * Verifica se há proxies disponíveis em estoque para um país
     *
     * @param string $paisCodigo Código do país (ex: BR, US)
     * @return bool
     */
    public function hasAvailableVps(string $paisCodigo): bool
    {
        // Converter código do país para nome completo
        $paisNome = $this->getPaisNome($paisCodigo);

        // Verificar se há proxies disponíveis no estoque (não vendidas e não de uso interno)
        return Stock::whereNull('user_id')
            ->where('disponibilidade', true)
            ->where('uso_interno', false)
            ->where('pais', $paisNome)
            ->exists();
    }

    /**
     * Retorna a quantidade de proxies disponíveis em estoque para um país
     *
     * @param string $paisCodigo Código do país (ex: BR, US)
     * @return int
     */
    public function getAvailableProxiesCount(string $paisCodigo): int
    {
        $paisNome = $this->getPaisNome($paisCodigo);

        return Stock::whereNull('user_id')
            ->where('disponibilidade', true)
            ->where('uso_interno', false)
            ->where('pais', $paisNome)
            ->count();
    }

    /**
     * Retorna estatísticas de disponibilidade de VPS
     *
     * @return array
     */
    public function getVpsStats(): array
    {
        $vpsAtivas = Vps::where('status', 'Operacional')->get();

        $stats = [
            'total' => $vpsAtivas->count(),
            'por_pais' => [],
        ];

        foreach ($vpsAtivas as $vps) {
            if (!isset($stats['por_pais'][$vps->pais])) {
                $stats['por_pais'][$vps->pais] = 0;
            }
            $stats['por_pais'][$vps->pais]++;
        }

        return $stats;
    }
}
