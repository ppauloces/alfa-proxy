<?php

namespace App\Http\Controllers;

use App\Models\Vps;
use App\Models\Stock;
use App\Models\User;
use App\Models\Cartao;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class AdminController extends Controller
{
    public function proxies(Request $request)
    {
        // Buscar usuário autenticado
        $usuario = User::where('id', Auth::id())->first();

        // Buscar todas as VPS cadastradas
        $vpsList = Vps::with('proxies')->orderBy('created_at', 'desc')->get();

        // Formatar dados para a view (Painel de Farm)
        $vpsFarm = $vpsList->map(function ($vps) {
            $vpsData = [
                'id' => $vps->id,
                'apelido' => $vps->apelido,
                'ip' => $vps->ip,
                'pais' => $vps->pais,
                'hospedagem' => $vps->hospedagem,
                'valor' => 'R$ ' . number_format($vps->valor, 2, ',', '.'),
                'periodo' => $vps->periodo_dias . ' dias',
                'contratada' => $vps->data_contratacao->format('d/m/Y'),
                'status' => $vps->status,
                'proxies' => $vps->proxies,
            ];
            return (object) $vpsData;
        });

        // Buscar proxies geradas recentemente
        $generatedProxies = Stock::with('vps')
            ->whereNotNull('vps_id')
            ->orderBy('created_at', 'desc')
            ->limit(25)
            ->get()
            ->map(function ($proxy) {
                return [
                    'numero' => '#' . str_pad($proxy->id, 3, '0', STR_PAD_LEFT),
                    'endereco' => $proxy->ip . ':' . $proxy->porta,
                    'user' => $proxy->usuario,
                    'senha' => $proxy->senha,
                    'vps' => $proxy->vps ? $proxy->vps->apelido : 'N/A',
                    'status' => $proxy->disponibilidade ? 'Disponivel' : 'Vendida',
                ];
            })->toArray();

        // Dados para o Histórico de VPS (usado na aba de Histórico)
        $vpsHistorico = $vpsList->map(function ($vps) {
            $dataExpiracao = $vps->data_contratacao->addDays($vps->periodo_dias);
            $diasRestantes = now()->diffInDays($dataExpiracao, false);
            
            $statusExpiracao = 'Ativa';
            $badgeExpiracao = 'bg-green-100 text-green-700';
            
            if ($diasRestantes < 0) {
                $statusExpiracao = 'Expirada';
                $badgeExpiracao = 'bg-red-100 text-red-700';
            } elseif ($diasRestantes <= 5) {
                $statusExpiracao = 'Expira em breve';
                $badgeExpiracao = 'bg-amber-100 text-amber-700';
            }

            return (object) [
                'id' => $vps->id,
                'apelido' => $vps->apelido,
                'ip' => $vps->ip,
                'pais' => $vps->pais,
                'hospedagem' => $vps->hospedagem,
                'valor_formatado' => 'R$ ' . number_format($vps->valor, 2, ',', '.'),
                'periodo_dias' => $vps->periodo_dias,
                'data_contratacao' => $vps->data_contratacao->format('d/m/Y'),
                'data_expiracao' => $dataExpiracao->format('d/m/Y'),
                'status_expiracao' => $statusExpiracao,
                'badge_expiracao' => $badgeExpiracao,
                'status' => $vps->status,
                'total_proxies' => $vps->proxies->count(),
                'proxies_geradas' => $vps->proxies_geradas,
                'status_geracao' => $vps->status_geracao,
                'erro_geracao' => $vps->erro_geracao,
            ];
        });

        $estatisticas = [
            'total_vps' => $vpsList->count(),
            'vps_ativas' => $vpsList->filter(fn($v) => $v->data_contratacao->addDays($v->periodo_dias)->isFuture())->count(),
            'vps_expiradas' => $vpsList->filter(fn($v) => $v->data_contratacao->addDays($v->periodo_dias)->isPast())->count(),
            'total_gasto' => $vpsList->sum('valor'),
            'total_proxies_geradas' => $vpsList->sum('proxies_geradas'),
            'media_proxies_por_vps' => $vpsList->count() > 0 ? round($vpsList->sum('proxies_geradas') / $vpsList->count(), 1) : 0,
        ];

        $activeSection = $request->get('section', 'admin-proxies');
        $currentSection = $activeSection;

        return view('dash.index', compact(
            'usuario', 
            'vpsFarm', 
            'generatedProxies', 
            'vpsHistorico', 
            'estatisticas', 
            'activeSection', 
            'currentSection'
        ));
    }

    public function historicoVps(Request $request)
    {
        // Redireciona para a página de proxies com a seção de histórico ativa
        return redirect()->route('admin.proxies', ['section' => 'admin-historico-vps']);
    }
    public function cadastrarVps(Request $request)
    {
        $validated = $request->validate([
            'ip' => 'required',
            'usuario_ssh' => 'required|string|max:255',
            'senha_ssh' => 'required|string',
            'valor' => 'required|numeric|min:0',
            'pais' => 'required|string|in:Brasil,Estados Unidos,Portugal,Alemanha',
            'hospedagem' => 'required|string|max:255',
            'periodo_dias' => 'required|integer|in:30,60,90,180',
            'data_contratacao' => 'required|date',
            'apelido' => 'nullable|string|max:255',
            'rodar_script' => 'nullable|boolean',
            'quantidade_proxies' => 'nullable|integer|min:1|max:1000',
        ], [
            'ip.required' => 'O IP da VPS é obrigatório.',
            'ip.ip' => 'O IP informado não é válido.',
            'usuario_ssh.required' => 'O usuário SSH é obrigatório.',
            'senha_ssh.required' => 'A senha SSH é obrigatória.',
            'valor.required' => 'O valor da VPS é obrigatório.',
            'valor.numeric' => 'O valor deve ser um número.',
            'valor.min' => 'O valor deve ser maior ou igual a zero.',
            'pais.required' => 'O país é obrigatório.',
            'pais.in' => 'País selecionado é inválido.',
            'hospedagem.required' => 'A hospedagem é obrigatória.',
            'periodo_dias.required' => 'O período contratado é obrigatório.',
            'periodo_dias.in' => 'Período selecionado é inválido.',
            'data_contratacao.required' => 'A data de contratação é obrigatória.',
            'data_contratacao.date' => 'A data informada não é válida.',
        ]);

        // Gerar apelido se não fornecido
        $apelido = $validated['apelido'] ?? $this->gerarApelido($validated['pais'], $validated['hospedagem']);

        // Criar a VPS
        $vps = Vps::create([
            'apelido' => $apelido,
            'ip' => $validated['ip'],
            'usuario_ssh' => $validated['usuario_ssh'],
            'senha_ssh' => $validated['senha_ssh'],
            'valor' => $validated['valor'],
            'pais' => $validated['pais'],
            'hospedagem' => $validated['hospedagem'],
            'periodo_dias' => $validated['periodo_dias'],
            'data_contratacao' => $validated['data_contratacao'],
            'status' => 'Operacional',
            'status_geracao' => 'pending',
        ]);

        // Se o checkbox estiver marcado, despachar Job para gerar proxies em background
        if ($request->has('rodar_script') && $request->rodar_script) {
            // Atualizar status da VPS para 'pending' (aguardando processamento)
            $vps->update(['status_geracao' => 'pending']);

            // Despachar Job para a fila (processamento em background)
           // \App\Jobs\GerarProxiesJob::dispatch($vps, intval($validated['periodo_dias']), Auth::id());

            // Resposta imediata ao admin
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'VPS cadastrada! A geração de proxies está sendo processada em background. Você será notificado quando concluir.',
                    'redirect' => route('admin.proxies'),
                ]);
            }

            return redirect()
                ->route('admin.proxies')
                ->with('success', 'VPS cadastrada! A geração de proxies está sendo processada em background.');
        }

        // Se for requisição AJAX sem script, retornar JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'VPS cadastrada com sucesso!',
                'redirect' => route('admin.proxies'),
            ]);
        }

        return redirect()
            ->route('admin.proxies')
            ->with('success', 'VPS cadastrada com sucesso!');
    }

    /**
     * Retorna status em tempo real da geração de proxies
     * Endpoint: GET /api/vps/status-geracao
     */
    public function statusGeracao(Request $request)
    {
        // Buscar apenas VPS que estão ATIVAMENTE em processo de geração (pending ou processing)
        $vpsEmGeracao = Vps::whereIn('status_geracao', ['pending', 'processing'])
            ->select('id', 'apelido', 'ip', 'status_geracao', 'proxies_geradas', 'erro_geracao', 'updated_at')
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($vps) {
                return [
                    'id' => $vps->id,
                    'apelido' => $vps->apelido,
                    'ip' => $vps->ip,
                    'status' => $vps->status_geracao,
                    'proxies_geradas' => $vps->proxies_geradas,
                    'erro' => $vps->erro_geracao,
                    'ultima_atualizacao' => $vps->updated_at->diffForHumans(),
                    'badge_class' => $this->getBadgeClass($vps->status_geracao),
                    'badge_text' => $this->getBadgeText($vps->status_geracao),
                ];
            });

        return response()->json([
            'success' => true,
            'vps' => $vpsEmGeracao,
            'tem_processamento_ativo' => $vpsEmGeracao->count() > 0,
        ]);
    }

    /**
     * Retorna classe CSS para badge baseado no status
     */
    private function getBadgeClass($status)
    {
        return match($status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'processing' => 'bg-blue-100 text-blue-800 animate-pulse',
            'completed' => 'bg-green-100 text-green-800',
            'failed' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Retorna texto para badge baseado no status
     */
    private function getBadgeText($status)
    {
        return match($status) {
            'pending' => 'Na fila',
            'processing' => 'Gerando...',
            'completed' => 'Concluído',
            'failed' => 'Erro',
            default => 'Desconhecido',
        };
    }

    private function gerarApelido($pais, $hospedagem)
    {
        $prefixos = [
            'Brasil' => 'BR',
            'Estados Unidos' => 'US',
            'Portugal' => 'PT',
            'Alemanha' => 'DE',
        ];

        $prefixo = $prefixos[$pais] ?? 'XX';
        $hospedagemShort = strtoupper(substr($hospedagem, 0, 4));
        $numero = Vps::where('pais', $pais)->count() + 1;

        return "{$prefixo}-{$hospedagemShort} " . str_pad($numero, 2, '0', STR_PAD_LEFT);
    }

    /**
     * Bloquear porta do proxy via UFW (Python API)
     */
    public function bloquearProxy(Request $request)
    {
        try {
            $validated = $request->validate([
                'stock_id' => 'required|exists:stocks,id',
            ]);

            // Buscar o proxy e sua VPS
            $stock = Stock::with('vps')->findOrFail($validated['stock_id']);

            if (!$stock->vps) {
                return response()->json([
                    'success' => false,
                    'error' => 'VPS não encontrada para este proxy.',
                ], 404);
            }

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
                $data = $response->json();

                // Atualizar status no banco de dados
                $stock->update(['bloqueada' => true]);

                Log::info('Porta bloqueada com sucesso', [
                    'stock_id' => $stock->id,
                    'porta' => $stock->porta,
                    'vps_ip' => $vps->ip,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => $data['mensagem'] ?? 'Porta bloqueada com sucesso!',
                    'data' => $data,
                ]);
            }

            Log::error('Erro ao bloquear porta', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Erro ao bloquear porta no servidor.',
            ], 500);

        } catch (\Exception $e) {
            Log::error('Exceção ao bloquear porta', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Erro ao conectar com o servidor: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Desbloquear porta do proxy via UFW (Python API)
     */
    public function desbloquearProxy(Request $request)
    {
        try {
            $validated = $request->validate([
                'stock_id' => 'required|exists:stocks,id',
            ]);

            // Buscar o proxy e sua VPS
            $stock = Stock::with('vps')->findOrFail($validated['stock_id']);

            if (!$stock->vps) {
                return response()->json([
                    'success' => false,
                    'error' => 'VPS não encontrada para este proxy.',
                ], 404);
            }

            $vps = $stock->vps;

            // Preparar payload para a API Python
            $payload = [
                'ip_vps' => $vps->ip,
                'user_ssh' => $vps->usuario_ssh,
                'senha_ssh' => $vps->senha_ssh,
                'porta' => $stock->porta,
            ];

            // Chamar API Python para desbloquear a porta
            $pythonApiUrl = config('services.python_api.url', 'http://127.0.0.1:8001');
            $response = Http::timeout(30)->post("{$pythonApiUrl}/desbloquear", $payload);

            if ($response->successful()) {
                $data = $response->json();

                // Atualizar status no banco de dados
                $stock->update(['bloqueada' => false]);

                Log::info('Porta desbloqueada com sucesso', [
                    'stock_id' => $stock->id,
                    'porta' => $stock->porta,
                    'vps_ip' => $vps->ip,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => $data['mensagem'] ?? 'Porta desbloqueada com sucesso!',
                    'data' => $data,
                ]);
            }

            Log::error('Erro ao desbloquear porta', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Erro ao desbloquear porta no servidor.',
            ], 500);

        } catch (\Exception $e) {
            Log::error('Exceção ao desbloquear porta', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Erro ao conectar com o servidor: ' . $e->getMessage(),
            ], 500);
        }
    }
}