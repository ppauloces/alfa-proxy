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
            'valor_renovacao' => 'nullable|numeric|min:0',
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
            'valor_renovacao.numeric' => 'O valor de renovação deve ser um número.',
            'valor_renovacao.min' => 'O valor de renovação deve ser maior ou igual a zero.',
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
            'valor_renovacao' => $validated['valor_renovacao'],
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
                'nova_expiracao' => 'nullable|date|after:now',
            ], [
                'nova_expiracao.date' => 'A data de expiração deve ser uma data válida.',
                'nova_expiracao.after' => 'A data de expiração deve ser futura.',
            ]);

            // Buscar o proxy e sua VPS
            $stock = Stock::with('vps')->findOrFail($validated['stock_id']);

            if (!$stock->vps) {
                return response()->json([
                    'success' => false,
                    'error' => 'VPS não encontrada para este proxy.',
                ], 404);
            }

            // Verificar se a proxy está expirada e se foi fornecida uma nova data
            $isExpirada = $stock->expiracao && Carbon::parse($stock->expiracao)->isPast();

            if ($isExpirada && !isset($validated['nova_expiracao'])) {
                return response()->json([
                    'success' => false,
                    'error' => 'Proxy expirada! Você deve fornecer uma nova data de expiração.',
                    'requires_date' => true,
                ], 422);
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

                // Preparar dados de atualização
                $updateData = ['bloqueada' => false];

                // Se foi fornecida uma nova data de expiração, atualizar
                if (isset($validated['nova_expiracao'])) {
                    $updateData['expiracao'] = $validated['nova_expiracao'];
                }

                // Atualizar status no banco de dados
                $stock->update($updateData);

                Log::info('Porta desbloqueada com sucesso', [
                    'stock_id' => $stock->id,
                    'porta' => $stock->porta,
                    'vps_ip' => $vps->ip,
                    'nova_expiracao' => $validated['nova_expiracao'] ?? null,
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
     * Substituir proxy caído por outro disponível do mesmo país
     */
    public function substituirProxy(Request $request)
    {
        $validated = $request->validate([
            'stock_id' => 'required|integer|exists:stocks,id',
            'vps_id' => 'required|integer|exists:vps,id',
        ]);

        $stockAntigo = \App\Models\Stock::find($validated['stock_id']);

        if (!$stockAntigo->user_id) {
            return response()->json(['success' => false, 'error' => 'Este proxy não está atribuído a nenhum usuário.'], 422);
        }

        if ($stockAntigo->substituido) {
            return response()->json(['success' => false, 'error' => 'Este proxy já foi substituído anteriormente.'], 422);
        }

        $novoStock = \App\Models\Stock::whereNull('user_id')
            ->where('disponibilidade', true)
            ->where('uso_interno', false)
            ->where('vps_id', $validated['vps_id'])
            ->first();

        if (!$novoStock) {
            return response()->json(['success' => false, 'error' => 'Sem estoque disponível na VPS selecionada.'], 422);
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($stockAntigo, $novoStock) {
            $novoStock->update([
                'user_id' => $stockAntigo->user_id,
                'codigo_pais' => $stockAntigo->codigo_pais,
                'motivo_uso' => $stockAntigo->motivo_uso,
                'periodo_dias' => $stockAntigo->periodo_dias,
                'expiracao' => $stockAntigo->expiracao,
                'disponibilidade' => false,
                'renovacao_automatica' => false,
            ]);

            $stockAntigo->update([
                'substituido' => true,
                'substituido_por' => $novoStock->id,
            ]);
        });

        Log::info('Proxy substituído pelo admin', [
            'stock_antigo_id' => $stockAntigo->id,
            'stock_novo_id' => $novoStock->id,
            'user_id' => $stockAntigo->user_id,
            'pais' => $stockAntigo->pais,
            'admin_id' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Proxy substituído com sucesso!',
            'novo_proxy' => [
                'id' => $novoStock->id,
                'ip' => $novoStock->ip,
                'porta' => $novoStock->porta,
            ],
        ]);
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

    /**
     * Atualizar cargo do usuário
     */
    public function atualizarCargo(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'cargo' => 'required|string|in:usuario,admin,super,revendedor',
            ], [
                'cargo.required' => 'O cargo é obrigatório.',
                'cargo.in' => 'Cargo inválido. Valores aceitos: usuario, admin, super, revendedor.',
            ]);

            $user = User::findOrFail($id);
            $cargoAntigo = $user->cargo;

            // Atualizar cargo do usuário
            $user->update(['cargo' => $validated['cargo']]);

            Log::info('Cargo do usuário atualizado', [
                'user_id' => $user->id,
                'cargo_antigo' => $cargoAntigo,
                'cargo_novo' => $validated['cargo'],
                'atualizado_por' => Auth::id(),
            ]);

            return redirect()->back()->with('success', 'Cargo atualizado com sucesso!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors(['error' => $e->validator->errors()->first()]);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar cargo do usuário', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->withErrors(['error' => 'Erro ao atualizar cargo: ' . $e->getMessage()]);
        }
    }

    public function usuarios(Request $request)
    {
        return redirect()->route('dash.show', ['section' => $request->query('section', 'admin-usuarios')]);
    }

    /**
     * Atualizar dados da VPS
     */
    public function atualizarVps(Request $request)
    {
        try {
            $validated = $request->validate([
                'vps_id' => 'required|exists:vps,id',
                'valor' => 'required|numeric|min:0',
                'valor_renovacao' => 'nullable|numeric|min:0',
                'data_contratacao' => 'required|date',
                'periodo_dias' => 'required|integer|in:30,60,90,180',
                'hospedagem' => 'required|string|max:255',
                'status' => 'required|string|in:Operacional,Inativa,Excluída',
            ], [
                'vps_id.required' => 'ID da VPS é obrigatório.',
                'vps_id.exists' => 'VPS não encontrada.',
                'valor.required' => 'O valor da VPS é obrigatório.',
                'valor.numeric' => 'O valor deve ser um número.',
                'valor.min' => 'O valor deve ser maior ou igual a zero.',
                'valor_renovacao.numeric' => 'O valor de renovação deve ser um número.',
                'valor_renovacao.min' => 'O valor de renovação deve ser maior ou igual a zero.',
                'data_contratacao.required' => 'A data de contratação é obrigatória.',
                'data_contratacao.date' => 'A data informada não é válida.',
                'periodo_dias.required' => 'O período contratado é obrigatório.',
                'periodo_dias.in' => 'Período selecionado é inválido.',
                'hospedagem.required' => 'A hospedagem é obrigatória.',
                'status.required' => 'O status é obrigatório.',
                'status.in' => 'Status inválido.',
            ]);

            $vps = Vps::findOrFail($validated['vps_id']);

            // Armazenar valores antigos para log
            $dadosAntigos = [
                'valor' => $vps->valor,
                'valor_renovacao' => $vps->valor_renovacao,
                'data_contratacao' => $vps->data_contratacao,
                'periodo_dias' => $vps->periodo_dias,
                'hospedagem' => $vps->hospedagem,
                'status' => $vps->status,
            ];

            // Atualizar VPS
            $vps->update([
                'valor' => $validated['valor'],
                'valor_renovacao' => $validated['valor_renovacao'],
                'data_contratacao' => $validated['data_contratacao'],
                'periodo_dias' => $validated['periodo_dias'],
                'hospedagem' => $validated['hospedagem'],
                'status' => $validated['status'],
            ]);

            Log::info('VPS atualizada', [
                'vps_id' => $vps->id,
                'apelido' => $vps->apelido,
                'dados_antigos' => $dadosAntigos,
                'dados_novos' => [
                    'valor' => $validated['valor'],
                    'valor_renovacao' => $validated['valor_renovacao'],
                    'data_contratacao' => $validated['data_contratacao'],
                    'periodo_dias' => $validated['periodo_dias'],
                    'hospedagem' => $validated['hospedagem'],
                    'status' => $validated['status'],
                ],
                'atualizado_por' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'VPS atualizada com sucesso!',
                'vps' => $vps->fresh(),
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->validator->errors()->first(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar VPS', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Erro ao atualizar VPS: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ===== SEÇÕES MASTER (apenas super) =====

    public function colaboradores(Request $request)
    {
        if (!Auth::user()->isSuperAdmin()) {
            return redirect()->route('dash.show')->with('error', 'Acesso restrito.');
        }

        return redirect()->route('dash.show', ['section' => 'admin-colaboradores']);
    }

    public function financeiro(Request $request)
    {
        if (!Auth::user()->isSuperAdmin()) {
            return redirect()->route('dash.show')->with('error', 'Acesso restrito.');
        }

        return redirect()->route('dash.show', ['section' => 'admin-financeiro']);
    }

    public function atualizarCargoColaborador(Request $request, $id)
    {
        if (!Auth::user()->isSuperAdmin()) {
            return redirect()->back()->with('error', 'Acesso restrito.');
        }

        try {
            $validated = $request->validate([
                'cargo' => 'required|in:usuario,admin,super',
            ]);

            $user = User::findOrFail($id);

            if ($user->id === Auth::id()) {
                return redirect()->back()->withErrors(['error' => 'Você não pode alterar seu próprio cargo.']);
            }

            $user->cargo = $validated['cargo'];
            $user->save();

            Log::info('Cargo de colaborador atualizado', [
                'admin_id' => Auth::id(),
                'user_id' => $user->id,
                'novo_cargo' => $validated['cargo'],
            ]);

            return redirect()->route('dash.show', ['section' => 'admin-colaboradores'])
                ->with('success', 'Cargo atualizado com sucesso.');
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar cargo de colaborador', ['error' => $e->getMessage()]);
            return redirect()->back()->withErrors(['error' => 'Erro ao atualizar cargo.']);
        }
    }

    public function toggleStatusColaborador(Request $request, $id)
    {
        if (!Auth::user()->isSuperAdmin()) {
            return redirect()->back()->with('error', 'Acesso restrito.');
        }

        try {
            $user = User::findOrFail($id);

            if ($user->id === Auth::id()) {
                return redirect()->back()->withErrors(['error' => 'Você não pode alterar seu próprio status.']);
            }

            $user->status = !$user->status;
            $user->save();

            Log::info('Status de colaborador alterado', [
                'admin_id' => Auth::id(),
                'user_id' => $user->id,
                'novo_status' => $user->status,
            ]);

            $msg = $user->status ? 'Colaborador ativado.' : 'Colaborador desativado.';
            return redirect()->route('dash.show', ['section' => 'admin-colaboradores'])
                ->with('success', $msg);
        } catch (\Exception $e) {
            Log::error('Erro ao alterar status de colaborador', ['error' => $e->getMessage()]);
            return redirect()->back()->withErrors(['error' => 'Erro ao alterar status.']);
        }
    }

    // ===== LANÇAMENTO DE DESPESAS =====

    public function lancarRenovacaoVps(Request $request)
    {
        if (!Auth::user()->isSuperAdmin()) {
            return response()->json(['success' => false, 'error' => 'Acesso restrito.'], 403);
        }

        try {
            $validated = $request->validate([
                'vps_id' => 'required|exists:vps,id',
                'valor' => 'required|numeric|min:0.01',
                'data_vencimento' => 'required|date',
                'status' => 'required|in:pago,pendente',
            ], [
                'vps_id.required' => 'Selecione uma VPS.',
                'vps_id.exists' => 'VPS não encontrada.',
                'valor.required' => 'O valor é obrigatório.',
                'valor.min' => 'O valor deve ser maior que zero.',
                'data_vencimento.required' => 'A data é obrigatória.',
                'status.required' => 'O status é obrigatório.',
            ]);

            $vps = Vps::findOrFail($validated['vps_id']);

            $despesa = Despesa::create([
                'vps_id' => $vps->id,
                'tipo' => 'renovacao',
                'valor' => $validated['valor'],
                'descricao' => "Renovação VPS {$vps->apelido} - Período {$vps->periodo_dias} dias",
                'data_vencimento' => $validated['data_vencimento'],
                'data_pagamento' => $validated['status'] === 'pago' ? $validated['data_vencimento'] : null,
                'status' => $validated['status'],
            ]);


            return response()->json([
                'success' => true,
                'message' => "Renovação de {$vps->apelido} lançada com sucesso (R$ " . number_format($validated['valor'], 2, ',', '.') . ").",
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->validator->errors()->first(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erro ao lançar renovação de VPS', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'error' => 'Erro ao lançar renovação: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Retorna dados financeiros filtrados por período (AJAX)
     */
    public function financeiroData(Request $request)
    {
        if (!Auth::user()->isSuperAdmin()) {
            return response()->json(['error' => 'Acesso negado.'], 403);
        }

        $startDate = $request->query('start_date')
            ? Carbon::parse($request->query('start_date'))->startOfDay()
            : Carbon::now()->subDays(29)->startOfDay();

        $endDate = $request->query('end_date')
            ? Carbon::parse($request->query('end_date'))->endOfDay()
            : Carbon::now()->endOfDay();

        // ===== CARDS FINANCEIROS =====
        $totalEntradas = Transaction::where('status', 1)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('valor');

        $totalSaidas = Despesa::whereIn('status', ['pago', 'pendente'])
            ->whereBetween('data_vencimento', [$startDate, $endDate])
            ->sum('valor');

        $lucroLiquido = $totalEntradas - $totalSaidas;

        $revendedoresIds = User::where('cargo', 'revendedor')->pluck('id');
        $totalEntradasRevendedores = Transaction::where('status', 1)
            ->whereIn('user_id', $revendedoresIds)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('valor');
        $proxiesVendidasRevendedores = Stock::whereIn('user_id', $revendedoresIds)
            ->where('disponibilidade', false)
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->count();

        $transacoesNoPeriodo = Transaction::where('status', 1)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
        $despesasNoPeriodo = Despesa::whereIn('status', ['pago', 'pendente'])
            ->whereBetween('data_vencimento', [$startDate, $endDate])
            ->count();

        $financeCards = [
            [
                'label' => 'Total Entradas',
                'value' => 'R$ ' . number_format($totalEntradas, 2, ',', '.'),
                'trend' => '+' . $transacoesNoPeriodo . ' no período',
                'bar' => 100,
            ],
            [
                'label' => 'Total Saídas',
                'value' => 'R$ ' . number_format($totalSaidas, 2, ',', '.'),
                'trend' => $despesasNoPeriodo . ' despesas no período',
                'bar' => $totalEntradas > 0 ? ($totalSaidas / $totalEntradas) * 100 : 0,
            ],
            [
                'label' => 'Lucro Líquido',
                'value' => 'R$ ' . number_format($lucroLiquido, 2, ',', '.'),
                'trend' => $lucroLiquido >= 0 ? 'Positivo' : 'Negativo',
                'bar' => $totalEntradas > 0 ? ($lucroLiquido / $totalEntradas) * 100 : 0,
            ],
            [
                'label' => 'Vendas Revendedores',
                'value' => 'R$ ' . number_format($totalEntradasRevendedores, 2, ',', '.'),
                'trend' => $proxiesVendidasRevendedores . ' ' . ($proxiesVendidasRevendedores === 1 ? 'proxy vendida' : 'proxies vendidas'),
                'bar' => $totalEntradas > 0 ? ($totalEntradasRevendedores / $totalEntradas) * 100 : 0,
            ],
        ];

        // ===== EXTRATO DE SAÍDAS =====
        $saidasDespesas = Despesa::with('vps')
            ->whereIn('status', ['pago', 'pendente'])
            ->whereBetween('data_vencimento', [$startDate, $endDate])
            ->orderBy('data_vencimento', 'desc')
            ->limit(50)
            ->get()
            ->map(function ($despesa) {
                $tipoLabel = match ($despesa->tipo) {
                    'compra' => 'Compra VPS',
                    'cobranca' => 'Cobrança',
                    'renovacao' => 'Renovação',
                    default => ucfirst($despesa->tipo),
                };

                return [
                    'descricao' => $despesa->descricao ?? 'VPS ' . ($despesa->vps->apelido ?? 'N/A'),
                    'categoria' => $tipoLabel,
                    'tipo' => $despesa->tipo,
                    'data' => $despesa->data_vencimento ? $despesa->data_vencimento->format('d/m/Y') : $despesa->created_at->format('d/m/Y'),
                    'valor' => '- R$ ' . number_format((float) $despesa->valor, 2, ',', '.'),
                    'status' => $despesa->status,
                    'sort_date' => ($despesa->data_vencimento ?? $despesa->created_at)->toISOString(),
                ];
            });

        // Proxies em uso interno como saída
        $saidasUsoInterno = Stock::where('uso_interno', true)
            ->with('vps')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($stock) {
                $endereco = ($stock->ip ?? 'N/A') . ':' . $stock->porta;
                return [
                    'descricao' => 'Uso Interno — ' . ($stock->finalidade_interna ?? 'Não especificada'),
                    'categoria' => 'Uso Interno',
                    'tipo' => 'uso_interno',
                    'data' => $stock->updated_at->format('d/m/Y'),
                    'valor' => $endereco,
                    'status' => 'ativo',
                    'sort_date' => $stock->updated_at->toISOString(),
                ];
            });

        $saidasSubstituidas = Stock::with('vps')
            ->where('substituido', true)
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($stock) {
                $endereco = ($stock->ip ?? 'N/A') . ':' . $stock->porta;
                return [
                    'descricao' => 'Substituição — ' . $endereco,
                    'categoria' => 'Substituição',
                    'tipo' => 'substituicao',
                    'data' => $stock->updated_at->format('d/m/Y'),
                    'valor' => '1 Proxy (Custo)',
                    'status' => 'ativo',
                    'sort_date' => $stock->updated_at->toISOString(),
                ];
            });

        $extratoSaidas = $saidasDespesas->concat($saidasUsoInterno)->concat($saidasSubstituidas)
            ->sortByDesc('sort_date')
            ->values();

        // ===== EXTRATO DE ENTRADAS (agrupadas por user+dia) =====
        $entradas = Transaction::with('user')
            ->where('status', 1)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->map(function ($transacao) {
                $metodo = $transacao->metodo_pagamento ?? 'saldo';
                $metodoLabel = match ($metodo) {
                    'pix' => 'PIX',
                    'credit_card' => 'Cartão de Crédito',
                    'saldo' => 'Saldo',
                    'boleto' => 'Boleto',
                    'usdt' => 'USDT',
                    'btc' => 'Bitcoin',
                    'ltc' => 'Litecoin',
                    'bnb' => 'Binance',
                    default => ucfirst($metodo),
                };

                return [
                    'data' => $transacao->created_at->format('d/m/Y'),
                    'valor_raw' => (float) $transacao->valor,
                    'is_revendedor' => $transacao->user && $transacao->user->cargo === 'revendedor',
                    'user_id' => $transacao->user_id,
                    'username' => $transacao->user->username ?? 'Usuário',
                    'categoria' => $metodoLabel,
                ];
            });

        $extratoEntradas = $entradas->groupBy(function ($item) {
            return $item['user_id'] . '_' . $item['data'];
        })->map(function ($group) {
            $first = $group->first();
            $totalRaw = $group->sum('valor_raw');

            return [
                'username' => $first['username'],
                'user_id' => $first['user_id'],
                'is_revendedor' => $first['is_revendedor'],
                'data' => $first['data'],
                'quantidade' => $group->count(),
                'valor_total' => '+ R$ ' . number_format($totalRaw, 2, ',', '.'),
                'categoria' => $first['categoria'],
            ];
        })->values();

        return response()->json([
            'financeCards' => $financeCards,
            'extratoSaidas' => $extratoSaidas,
            'extratoEntradas' => $extratoEntradas,
        ]);
    }

    /**
     * Dados filtrados para a aba admin de transações (vendas) via AJAX.
     */
    public function transacoesData(Request $request)
    {
        $startDate = $request->query('start_date')
            ? Carbon::parse($request->query('start_date'))->startOfDay()
            : Carbon::now()->subDays(29)->startOfDay();

        $endDate = $request->query('end_date')
            ? Carbon::parse($request->query('end_date'))->endOfDay()
            : Carbon::now()->endOfDay();

        // Cards filtrados por período
        $proxiesVendidos = Stock::where('disponibilidade', false)
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->count();
        $proxiesAtivos = Stock::where('disponibilidade', false)
            ->where('bloqueada', false)
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->count();
        $proxiesBloqueados = Stock::where('bloqueada', true)
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->count();
        $receitaTotal = Transaction::where('status', 1)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('valor');

        $soldProxyCards = [
            [
                'label' => 'Total Vendidos',
                'value' => number_format($proxiesVendidos, 0, ',', '.'),
                'chip' => 'Proxies',
            ],
            [
                'label' => 'Ativos',
                'value' => number_format($proxiesAtivos, 0, ',', '.'),
                'chip' => 'Em uso',
            ],
            [
                'label' => 'Bloqueados',
                'value' => number_format($proxiesBloqueados, 0, ',', '.'),
                'chip' => 'Suspensos',
            ],
            [
                'label' => 'Receita Total',
                'value' => 'R$ ' . number_format($receitaTotal, 2, ',', '.'),
                'chip' => 'Arrecadado',
            ],
        ];

        // Lista de vendas filtrada
        $soldProxies = Stock::with(['user', 'vps'])
            ->where('disponibilidade', false)
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($proxy) {
                $expiracao = $proxy->expiracao ? Carbon::parse($proxy->expiracao) : null;
                $diasRestantes = $expiracao ? now()->diffInDays($expiracao, false) : 0;

                $gastoCliente = Transaction::where('user_id', $proxy->user_id)
                    ->where('status', 1)
                    ->sum('valor');

                $pedidos = Stock::where('user_id', $proxy->user_id)
                    ->where('disponibilidade', false)
                    ->count();

                $transactions = Transaction::where('user_id', $proxy->user_id)
                    ->where('tipo', 'compra_proxy')
                    ->where('status', 1)
                    ->orderBy('created_at', 'desc')
                    ->get();

                $matchedTransaction = $transactions->first(function ($txn) use ($proxy) {
                    return abs(strtotime($txn->created_at) - strtotime($proxy->updated_at)) < 120;
                });

                $metadata = [];
                if ($matchedTransaction) {
                    $metaRaw = $matchedTransaction->metadata;
                    if (is_string($metaRaw)) {
                        $metadata = json_decode($metaRaw, true);
                    } elseif (is_array($metaRaw)) {
                        $metadata = $metaRaw;
                    }
                }

                return [
                    'id' => $proxy->id,
                    'stock_id' => $proxy->id,
                    'data' => $proxy->updated_at->format('d/m/Y'),
                    'endereco' => $proxy->ip . ':' . $proxy->porta,
                    'comprador' => $proxy->user->username ?? 'Anônimo',
                    'email' => $proxy->user->email ?? 'N/A',
                    'ip' => $proxy->ip,
                    'porta' => $proxy->porta,
                    'usuario' => $proxy->usuario,
                    'senha' => $proxy->senha,
                    'status' => $proxy->substituido ? 'substituida' : ($proxy->bloqueada ? 'bloqueada' : 'ativa'),
                    'periodo' => $diasRestantes > 0 ? $diasRestantes : 0,
                    'gasto_cliente' => 'R$ ' . number_format($gastoCliente, 2, ',', '.'),
                    'pedidos' => $pedidos,
                    'valor_unitario' => $metadata['valor_unitario'] ?? null,
                    'periodo_comprado' => $metadata['periodo'] ?? null,
                    'motivo' => $metadata['motivo'] ?? null,
                ];
            })->values();

        return response()->json([
            'soldProxyCards' => $soldProxyCards,
            'soldProxies' => $soldProxies,
        ]);
    }

    public function ipGeolocation(Request $request)
    {
        $ip = $request->input('ip');

        if (!$ip || !filter_var($ip, FILTER_VALIDATE_IP)) {
            return response()->json(['error' => 'IP inválido'], 422);
        }

        $response = Http::timeout(5)->get("http://ip-api.com/json/{$ip}?fields=status,city,regionName,country,countryCode");

        if ($response->successful() && $response->json('status') === 'success') {
            $data = $response->json();
            return response()->json([
                'city' => $data['city'] ?? null,
                'region' => $data['regionName'] ?? null,
                'country_name' => $data['country'] ?? null,
                'country_code' => $data['countryCode'] ?? null,
            ]);
        }

        return response()->json(['error' => 'Falha ao buscar geolocalização'], 502);
    }
}