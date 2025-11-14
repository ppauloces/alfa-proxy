<?php

namespace App\Http\Controllers;

use App\Models\Vps;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        // Se o checkbox estiver marcado, chamar a API Python
        if ($request->has('rodar_script') && $request->rodar_script) {
            try {
                $porta_inicial = 1080;
                $quantidade = $validated['quantidade_proxies'] ?? 50;
                $apiUrl = env('PYTHON_API_URL', 'http://localhost:8000');

                $response = \Illuminate\Support\Facades\Http::timeout(300)->post("{$apiUrl}/fake", [
                    'ip' => $validated['ip'],
                    'user' => $validated['usuario_ssh'],
                    'senha' => $validated['senha_ssh'],
                    'quantidade' => $quantidade,
                    'porta_inicial' => $porta_inicial,
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $proxies = is_array($data) && !isset($data['proxies']) ? $data : ($data['proxies'] ?? []);
                    $proxiesCriadas = 0;

                    // Cadastrar cada proxy na tabela stocks
                    foreach ($proxies as $proxy) {
                        Stock::create([
                            'user_id' => Auth::id(),
                            'vps_id' => $vps->id,
                            'tipo' => 'SOCKS5',
                            'ip' => $proxy['ip'],
                            'porta' => $proxy['porta'],
                            'usuario' => $proxy['usuario'],
                            'senha' => $proxy['senha'],
                            'pais' => $validated['pais'],
                            'expiracao' => now()->addDays(intval($validated['periodo_dias'])),
                            'disponibilidade' => true,
                        ]);
                        $proxiesCriadas++;
                    }

                    // Se for requisição AJAX, retornar JSON
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json([
                            'success' => true,
                            'message' => "VPS cadastrada e {$proxiesCriadas} proxies geradas com sucesso!",
                            'redirect' => route('proxies.show'),
                            'proxies_criadas' => $proxiesCriadas,
                        ]);
                    }

                    return redirect()
                        ->route('proxies.show')
                        ->with('success', "VPS cadastrada e {$proxiesCriadas} proxies geradas com sucesso!");
                } else {
                    $errorMessage = $response->json()['detail'] ?? 'Erro ao gerar proxies';

                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => "VPS cadastrada, mas houve erro ao gerar proxies: {$errorMessage}",
                            'redirect' => route('proxies.show'),
                        ], 400);
                    }

                    return redirect()
                        ->route('admin.proxies')
                        ->with('warning', "VPS cadastrada, mas houve erro ao gerar proxies: {$errorMessage}");
                }
            } catch (\Exception $e) {
                \Log::error('Erro ao chamar API Python: ' . $e->getMessage());

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => "VPS cadastrada, mas não foi possível gerar proxies. Erro: {$e->getMessage()}",
                        'redirect' => route('proxies.show'),
                    ], 500);
                }

                return redirect()
                    ->route('proxies.show')
                    ->with('warning', "VPS cadastrada, mas não foi possível gerar proxies. Erro: {$e->getMessage()}");
            }
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