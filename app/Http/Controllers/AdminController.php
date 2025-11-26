<?php

namespace App\Http\Controllers;

use App\Models\Vps;
use App\Models\Stock;
use App\Models\User;
use App\Models\Cartao;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class AdminController extends Controller
{
    public function proxies(Request $request)
    {
        // Buscar usuário autenticado
        $usuario = User::where('id', Auth::id())->first();
      

        // Buscar todas as VPS cadastradas
        $vpsList = Vps::with('proxies')->orderBy('created_at', 'desc')->get();

        // Formatar dados para a view
        $vpsFarm = $vpsList->map(function ($vps) {
            return [
                'id' => $vps->id,
                'apelido' => $vps->apelido,
                'ip' => $vps->ip,
                'pais' => $vps->pais,
                'hospedagem' => $vps->hospedagem,
                'valor' => 'R$ ' . number_format($vps->valor, 2, ',', '.'),
                'periodo' => $vps->periodo_dias . ' dias',
                'contratada' => $vps->data_contratacao->format('d/m/Y'),
                'status' => $vps->status,
                'proxies' => $vps->proxies->map(function ($proxy) use ($vps) {
                    return [
                        'codigo' => '#' . str_pad($proxy->id, 3, '0', STR_PAD_LEFT),
                        'endpoint' => $vps->ip . ':' . $proxy->porta,
                        'status' => $proxy->disponibilidade ? 'disponivel' : 'vendida',
                    ];
                })->toArray(),
            ];
        })->toArray();

        // Buscar proxies geradas recentemente
        $generatedProxies = Stock::with('vps')
            ->whereNotNull('vps_id')
            ->orderBy('created_at', 'desc')
            ->limit(50)
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

        $activeSection = 'admin-proxies';

        return view('dash.index', compact('usuario', 'vpsFarm', 'generatedProxies', 'activeSection'));
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
        ]);

        // Se o checkbox estiver marcado, despachar Job para gerar proxies em background
        if ($request->has('rodar_script') && $request->rodar_script) {
            // Atualizar status da VPS para 'pending' (aguardando processamento)
            $vps->update(['status_geracao' => 'pending']);

            // Despachar Job para a fila (processamento em background)
            \App\Jobs\GerarProxiesJob::dispatch($vps, intval($validated['periodo_dias']), Auth::id());

            // Resposta imediata ao admin
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'VPS cadastrada! A geração de proxies está sendo processada em background. Você será notificado quando concluir.',
                    'redirect' => route('proxies.show'),
                ]);
            }

            return redirect()
                ->route('proxies.show')
                ->with('success', 'VPS cadastrada! A geração de proxies está sendo processada em background.');
        }

        // Se for requisição AJAX sem script, retornar JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'VPS cadastrada com sucesso!',
                'redirect' => route('proxies.show'),
            ]);
        }

        return redirect()
            ->route('proxies.show')
            ->with('success', 'VPS cadastrada com sucesso!');
    }

    /**
     * Retorna status em tempo real da geração de proxies
     * Endpoint: GET /api/vps/status-geracao
     */
    public function statusGeracao(Request $request)
    {
        // Buscar apenas VPS que estão em processo de geração (não null)
        $vpsEmGeracao = Vps::whereNotNull('status_geracao')
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
            'tem_processamento_ativo' => $vpsEmGeracao->whereIn('status', ['pending', 'processing'])->count() > 0,
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
}