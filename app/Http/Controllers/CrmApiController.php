<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Services\ProxyAllocationService;
use Illuminate\Http\Request;

class CrmApiController extends Controller
{
    protected ProxyAllocationService $allocationService;

    public function __construct(ProxyAllocationService $allocationService)
    {
        $this->allocationService = $allocationService;
    }

    /**
     * Lista proxies disponíveis agrupados por país
     * GET /api/v1/proxies/available
     */
    public function available()
    {
        $paisesMap = [
            'BR' => 'Brasil',
            'US' => 'Estados Unidos',
            'GB' => 'Reino Unido',
            'DE' => 'Alemanha',
            'FR' => 'França',
            'IT' => 'Itália',
            'ES' => 'Espanha',
            'PT' => 'Portugal',
            'CA' => 'Canadá',
            'AU' => 'Austrália',
        ];

        $disponibilidade = [];

        foreach ($paisesMap as $codigo => $nome) {
            $count = $this->allocationService->getAvailableProxiesCount($codigo);
            if ($count > 0) {
                $disponibilidade[] = [
                    'country_code' => $codigo,
                    'country_name' => $nome,
                    'available' => $count,
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => $disponibilidade,
        ]);
    }

    /**
     * Lista proxies disponíveis com detalhes completos
     * GET /api/v1/proxies/available/details?country=BR
     */
    public function availableDetails(Request $request)
    {
        $query = Stock::whereNull('user_id')
            ->where('disponibilidade', true)
            ->where('uso_interno', false)
            ->whereHas('vps', function ($q) {
                $q->where('status', 'Operacional');
            });

        if ($request->has('country')) {
            $paisNome = $this->allocationService->getPaisNome($request->country);
            $query->where('pais', $paisNome);
        }

        $proxies = $query->orderBy('pais')->get();

        $data = $proxies->map(fn ($proxy) => [
            'id' => $proxy->id,
            'type' => $proxy->tipo,
            'ip' => $proxy->ip,
            'port' => $proxy->porta,
            'username' => $proxy->usuario,
            'password' => $proxy->senha,
            'country' => $proxy->pais,
            'country_code' => $proxy->codigo_pais,
        ]);

        return response()->json([
            'success' => true,
            'total' => $data->count(),
            'data' => $data,
        ]);
    }

    /**
     * Aloca proxies para o CRM (sem cobrança, pagamento é feito lá)
     * POST /api/v1/proxies/allocate
     *
     * Body: {
     *   "country": "BR",
     *   "quantity": 1,
     *   "period_days": 30,
     *   "reason": "Facebook",
     *   "crm_reference": "user_123"
     * }
     */
    public function allocate(Request $request)
    {
        $validated = $request->validate([
            'country' => 'required|string|max:2',
            'quantity' => 'required|integer|min:1|max:100',
            'period_days' => 'required|integer|in:30,60,90,180,360',
            'reason' => 'required|string|max:255',
            'crm_reference' => 'required|string|max:255',
        ]);

        // Verificar estoque
        $availableCount = $this->allocationService->getAvailableProxiesCount($validated['country']);
        if ($availableCount < $validated['quantity']) {
            return response()->json([
                'success' => false,
                'error' => 'Estoque insuficiente.',
                'requested' => $validated['quantity'],
                'available' => $availableCount,
            ], 422);
        }

        // Alocar proxies
        try {
            $proxies = $this->allocationService->allocateProxies($request->user()->id, [
                'pais' => $validated['country'],
                'quantidade' => $validated['quantity'],
                'periodo_dias' => $validated['period_days'],
                'motivo' => $validated['reason'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Falha ao alocar proxies: ' . $e->getMessage(),
            ], 500);
        }

        // Marcar proxies como origem CRM
        $proxyIds = collect($proxies)->pluck('id')->toArray();
        Stock::whereIn('id', $proxyIds)->update([
            'origem' => 'crm',
            'crm_referencia' => $validated['crm_reference'],
        ]);

        // Formatar resposta
        $proxiesData = collect($proxies)->map(fn ($proxy) => $this->formatProxy($proxy));

        return response()->json([
            'success' => true,
            'proxies' => $proxiesData,
        ], 201);
    }

    /**
     * Lista proxies alocados via CRM
     * GET /api/v1/proxies/allocated?crm_reference=user_123
     */
    public function allocated(Request $request)
    {
        $query = Stock::where('user_id', $request->user()->id)
            ->where('origem', 'crm');

        if ($request->has('crm_reference')) {
            $query->where('crm_referencia', $request->crm_reference);
        }

        $proxies = $query->orderBy('created_at', 'desc')->get();

        $data = $proxies->map(fn ($proxy) => $this->formatProxy($proxy));

        return response()->json([
            'success' => true,
            'total' => $data->count(),
            'data' => $data,
        ]);
    }

    /**
     * Detalhes de um proxy específico
     * GET /api/v1/proxies/{id}
     */
    public function show(Request $request, int $id)
    {
        $proxy = Stock::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->where('origem', 'crm')
            ->first();

        if (!$proxy) {
            return response()->json([
                'success' => false,
                'error' => 'Proxy não encontrado.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $this->formatProxy($proxy),
        ]);
    }

    /**
     * Testa conectividade de um proxy
     * POST /api/v1/proxies/test
     *
     * Body: { "ip": "1.2.3.4", "porta": 1080, "usuario": "user", "senha": "pass" }
     */
    public function test(Request $request)
    {
        $request->validate([
            'ip' => 'required|string',
            'porta' => 'required|integer',
            'usuario' => 'required|string',
            'senha' => 'required|string',
        ]);

        try {
            $apiUrl = config('services.python_api.url', env('PYTHON_API_URL', 'http://127.0.0.1:8001'));

            $response = \Illuminate\Support\Facades\Http::timeout(15)->post("{$apiUrl}/testar", [
                'ip' => $request->ip,
                'porta' => (int) $request->porta,
                'usuario' => $request->usuario,
                'senha' => $request->senha,
                'ip_visto_pelo_servidor' => $request->ip(),
                'timeout' => 5,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                // Buscar localização do IP detectado
                $detectedIp = $data['ip_visto_pelo_servidor'] ?? null;
                if ($detectedIp) {
                    try {
                        $geoResponse = \Illuminate\Support\Facades\Http::timeout(5)
                            ->get("http://ip-api.com/json/{$detectedIp}?fields=status,city,regionName,country,countryCode");

                        if ($geoResponse->successful() && $geoResponse->json('status') === 'success') {
                            $geo = $geoResponse->json();
                            $data['location'] = trim(($geo['city'] ?? '') . ', ' . ($geo['regionName'] ?? '') . ' - ' . ($geo['country'] ?? ''), ', - ');
                            $data['country_code'] = $geo['countryCode'] ?? null;
                        }
                    } catch (\Exception $e) {
                        // Ignora erro de geolocalização
                    }
                }

                return response()->json($data);
            }

            return response()->json([
                'status' => 'error',
                'error' => $response->json()['detail'] ?? 'Erro ao testar proxy',
            ], $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'error' => 'Erro ao conectar com servidor de testes: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Formata um proxy para resposta da API
     */
    private function formatProxy(Stock $proxy): array
    {
        return [
            'id' => $proxy->id,
            'ip' => $proxy->ip,
            'port' => $proxy->porta,
            'username' => $proxy->usuario,
            'password' => $proxy->senha,
            'type' => $proxy->tipo,
            'country' => $proxy->pais,
            'country_code' => $proxy->codigo_pais,
            'reason' => $proxy->motivo_uso,
            'crm_reference' => $proxy->crm_referencia,
            'period_days' => $proxy->periodo_dias,
            'expires_at' => $proxy->expiracao?->toIso8601String(),
            'is_expired' => $proxy->expiracao ? $proxy->expiracao->isPast() : false,
            'created_at' => $proxy->created_at->toIso8601String(),
        ];
    }
}
