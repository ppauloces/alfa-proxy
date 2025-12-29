<?php

namespace App\Http\Controllers;

use App\Models\Vps;
use App\Models\Stock;
use App\Models\User;
use App\Models\Cartao;
use App\Models\Despesa;
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
        return redirect()->route('dash.show', [
            'section' => $request->query('section', 'admin-proxies')
        ]);
    }
    public function historicoVps(Request $request)
    { 
        return redirect()->route('dash.show', ['section' => $request->query('section', 'admin-historico-vps')]);
    }

    public function relatorios(Request $request)
    {
        return redirect()->route('dash.show', ['section' => $request->query('section', 'admin-relatorios')]);
    }

    public function transacoes(Request $request)
    {
        return redirect()->route('dash.show', ['section' => $request->query('section', 'admin-transacoes')]);
    }
    public function cadastrarVps(Request $request)
    {
        $validated = $request->validate([
            'ip' => 'required',
            'usuario_ssh' => 'required|string|max:255',
            'senha_ssh' => 'required|string',
            'valor' => 'required|numeric|min:0',
            'pais' => 'required|string|in:Brasil,Estados Unidos,Reino Unido,Alemanha,França,Itália,Espanha,Portugal,Canadá,Austrália',
            'hospedagem' => 'required|string|max:255',
            'periodo_dias' => 'required|integer|in:30,60,90,180',
            'data_contratacao' => 'required|date',
            'apelido' => 'nullable|string|max:255',
            'rodar_script' => 'nullable|boolean',
            'vps_paga' => 'nullable|boolean',
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

        // IMPORTANTE: Registrar despesa ANTES de retornar (se checkbox estiver marcado)
        if ($request->has('vps_paga') && $request->vps_paga) {
            Despesa::create([
                'vps_id' => $vps->id,
                'tipo' => 'compra',
                'valor' => $validated['valor'],
                'descricao' => 'VPS ' . $vps->apelido . ' paga',
                'data_vencimento' => $validated['data_contratacao'],
                'data_pagamento' => $validated['data_contratacao'],
                'status' => 'pago',
            ]);

            Log::info('Despesa de VPS registrada', [
                'vps_id' => $vps->id,
                'valor' => $validated['valor'],
                'descricao' => 'VPS ' . $vps->apelido . ' paga',
            ]);
        }

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
        return match ($status) {
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
        return match ($status) {
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
            'Reino Unido' => 'GB',
            'Alemanha' => 'DE',
            'França' => 'FR',
            'Itália' => 'IT',
            'Espanha' => 'ES',
            'Portugal' => 'PT',
            'Canadá' => 'CA',
            'Austrália' => 'AU',
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
            Log::info('Resposta da API Python', [
                'status_code' => $response->status(),
                'response_body' => $response->body(),
                'response_json' => $response->json(),
                'payload_enviado' => $payload,
            ]);
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

    /**
     * Atualizar apelido da VPS
     */
    public function atualizarApelidoVps(Request $request)
    {
        try {
            $validated = $request->validate([
                'vps_id' => 'required|exists:vps,id',
                'apelido' => 'required|string|max:255|min:1',
            ], [
                'vps_id.required' => 'ID da VPS é obrigatório.',
                'vps_id.exists' => 'VPS não encontrada.',
                'apelido.required' => 'O apelido é obrigatório.',
                'apelido.max' => 'O apelido deve ter no máximo 255 caracteres.',
                'apelido.min' => 'O apelido deve ter pelo menos 1 caractere.',
            ]);

            $vps = Vps::findOrFail($validated['vps_id']);
            $vps->update(['apelido' => $validated['apelido']]);

            Log::info('Apelido da VPS atualizado', [
                'vps_id' => $vps->id,
                'apelido_antigo' => $vps->getOriginal('apelido'),
                'apelido_novo' => $validated['apelido'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Apelido atualizado com sucesso!',
                'apelido' => $validated['apelido'],
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->validator->errors()->first(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar apelido da VPS', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Erro ao atualizar apelido: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Atualizar país da VPS e de todas as proxies vinculadas
     */
    public function atualizarPaisVps(Request $request)
    {
        try {
            $validated = $request->validate([
                'vps_id' => 'required|exists:vps,id',
                'pais' => 'required|string|in:Brasil,Estados Unidos,Reino Unido,Alemanha,França,Itália,Espanha,Portugal,Canadá,Austrália',
            ], [
                'vps_id.required' => 'ID da VPS é obrigatório.',
                'vps_id.exists' => 'VPS não encontrada.',
                'pais.required' => 'O país é obrigatório.',
                'pais.in' => 'País selecionado é inválido.',
            ]);

            $vps = Vps::findOrFail($validated['vps_id']);
            $paisAntigo = $vps->pais;

            // Atualizar país da VPS
            $vps->update(['pais' => $validated['pais']]);

            // Obter código do país usando o mesmo mapeamento
            $paisesMap = [
                'Brasil' => 'BR',
                'Estados Unidos' => 'US',
                'Reino Unido' => 'GB',
                'Alemanha' => 'DE',
                'França' => 'FR',
                'Itália' => 'IT',
                'Espanha' => 'ES',
                'Portugal' => 'PT',
                'Canadá' => 'CA',
                'Austrália' => 'AU',
            ];
            $codigoPais = $paisesMap[$validated['pais']] ?? 'XX';

            // Atualizar país e código de todas as proxies dessa VPS
            $proxiesAtualizadas = Stock::where('vps_id', $validated['vps_id'])
                ->update([
                    'pais' => $validated['pais'],
                    'codigo_pais' => $codigoPais,
                ]);

            Log::info('País da VPS e proxies atualizados', [
                'vps_id' => $vps->id,
                'pais_antigo' => $paisAntigo,
                'pais_novo' => $validated['pais'],
                'codigo_pais' => $codigoPais,
                'proxies_atualizadas' => $proxiesAtualizadas,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'País atualizado com sucesso!',
                'pais' => $validated['pais'],
                'proxies_atualizadas' => $proxiesAtualizadas,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->validator->errors()->first(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar país da VPS', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Erro ao atualizar país: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Marcar proxy como uso interno
     */
    public function marcarUsoInterno(Request $request)
    {
        Log::info('marcarUsoInterno chamado', [
            'request_data' => $request->all(),
            'user_id' => Auth::id(),
        ]);

        try {
            $validated = $request->validate([
                'stock_id' => 'required|exists:stocks,id',
                'finalidade_interna' => 'required|string|max:255',
            ], [
                'stock_id.required' => 'ID da proxy é obrigatório.',
                'stock_id.exists' => 'Proxy não encontrada.',
                'finalidade_interna.required' => 'A finalidade é obrigatória.',
                'finalidade_interna.max' => 'A finalidade deve ter no máximo 255 caracteres.',
            ]);

            Log::info('Validação passou', ['validated' => $validated]);

            $proxy = Stock::findOrFail($validated['stock_id']);

            Log::info('Proxy encontrada', [
                'proxy_id' => $proxy->id,
                'bloqueada' => $proxy->bloqueada,
                'disponibilidade' => $proxy->disponibilidade,
                'uso_interno' => $proxy->uso_interno,
            ]);

            // Verificar se a proxy está bloqueada
            if ($proxy->bloqueada) {
                return response()->json([
                    'success' => false,
                    'error' => 'Não é possível marcar uma proxy bloqueada como uso interno.',
                ], 422);
            }

            // Verificar se a proxy está disponível (não vendida)
            if (!$proxy->disponibilidade) {
                return response()->json([
                    'success' => false,
                    'error' => 'Não é possível marcar uma proxy vendida como uso interno.',
                ], 422);
            }

            // Verificar se já está em uso interno
            if ($proxy->uso_interno) {
                return response()->json([
                    'success' => false,
                    'error' => 'Esta proxy já está marcada como uso interno.',
                ], 422);
            }

            // Marcar como uso interno
            $proxy->update([
                'uso_interno' => true,
                'finalidade_interna' => $validated['finalidade_interna'],
                'disponibilidade' => true, // Continua em estoque
                'user_id' => null, // Não vincula a nenhum usuário
            ]);

            Log::info('Proxy marcada como uso interno', [
                'stock_id' => $proxy->id,
                'finalidade' => $validated['finalidade_interna'],
                'ip' => $proxy->ip,
                'porta' => $proxy->porta,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Proxy marcada como uso interno com sucesso!',
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->validator->errors()->first(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erro ao marcar proxy como uso interno', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Erro ao processar requisição: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remover proxy do uso interno
     */
    public function removerUsoInterno(Request $request)
    {
        try {
            $validated = $request->validate([
                'stock_id' => 'required|exists:stocks,id',
            ], [
                'stock_id.required' => 'ID da proxy é obrigatório.',
                'stock_id.exists' => 'Proxy não encontrada.',
            ]);

            $proxy = Stock::findOrFail($validated['stock_id']);

            // Verificar se está em uso interno
            if (!$proxy->uso_interno) {
                return response()->json([
                    'success' => false,
                    'error' => 'Esta proxy não está marcada como uso interno.',
                ], 422);
            }

            // Remover uso interno
            $proxy->update([
                'uso_interno' => false,
                'finalidade_interna' => null,
                'disponibilidade' => true, // Volta para estoque disponível
            ]);

            Log::info('Proxy removida do uso interno', [
                'stock_id' => $proxy->id,
                'ip' => $proxy->ip,
                'porta' => $proxy->porta,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Proxy removida do uso interno com sucesso!',
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->validator->errors()->first(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erro ao remover proxy do uso interno', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Erro ao processar requisição: ' . $e->getMessage(),
            ], 500);
        }
    }

}