<?php

namespace App\Http\Controllers;

use App\Models\Cartao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CartaoController extends Controller
{
    /**
     * Exibe a página de gerenciamento de cartões
     */
    public function index()
    {
        // Redirecionar para o dashboard com a seção de cartões ativa
        return redirect()->route('dash.show', ['section' => 'cartoes']);
    }

    /**
     * Salva um novo cartão
     */
    public function store(Request $request)
    {
        // Debug: Log dos dados recebidos
        Log::info('Tentativa de salvar cartão', [
            'user_id' => Auth::id(),
            'request_data' => $request->except(['number', 'cvc']), // Não logar dados sensíveis completos
            'has_number' => $request->has('number'),
            'has_cvc' => $request->has('cvc'),
        ]);

        $request->validate([
            'number' => 'required|string|min:13|max:19',
            'name' => 'required|string|max:255',
            'expiry' => 'required|string',
            'cvc' => 'required|string|min:3|max:4',
        ]);

        try {
            DB::beginTransaction();

            // Processar dados do cartão
            $numeroLimpo = preg_replace('/\s+/', '', $request->number);
            $ultimosDigitos = substr($numeroLimpo, -4);

            // Identificar bandeira
            $bandeira = $this->identificarBandeira($numeroLimpo);

            // Processar validade - remover espaços e normalizar
            $expiryLimpo = preg_replace('/\s+/', '', $request->expiry);

            // Validar formato da validade
            if (!preg_match('/^(\d{2})\/(\d{2}|\d{4})$/', $expiryLimpo, $matches)) {
                return redirect()
                    ->back()
                    ->with('cartoes_error', 'Formato de validade inválido. Use MM/AA ou MM/AAAA.');
            }

            $mes = $matches[1];
            $ano = $matches[2];

            // Se ano tem 4 dígitos, usar diretamente. Se tem 2, converter para 20XX
            if (strlen($ano) == 2) {
                $ano = '20' . $ano;
            }

            // Validar se o cartão não está expirado
            $expiracaoData = \Carbon\Carbon::createFromDate($ano, $mes, 1)->endOfMonth();
            if (now()->greaterThan($expiracaoData)) {
                return redirect()
                    ->back()
                    ->with('cartoes_error', 'Cartão expirado. Verifique a data de validade.');
            }

            // Verificar se já existe cartão com esses dados
            $cartaoExistente = Cartao::where('user_id', Auth::id())
                ->where('ultimos_digitos', $ultimosDigitos)
                ->where('mes_expiracao', (int) $mes)
                ->where('ano_expiracao', (int) $ano)
                ->first();

            if ($cartaoExistente) {
                return redirect()
                    ->back()
                    ->with('cartoes_error', 'Este cartão já está cadastrado.');
            }

            // Se for marcado como padrão, desmarcar outros
            if ($request->is_default) {
                Cartao::where('user_id', Auth::id())->update(['is_default' => false]);
            }

            // Se for o primeiro cartão, torná-lo padrão automaticamente
            $totalCartoes = Cartao::where('user_id', Auth::id())->count();
            $isPrimeiro = $totalCartoes === 0;

            // Criar cartão
            $cartao = Cartao::create([
                'user_id' => Auth::id(),
                'bandeira' => $bandeira,
                'ultimos_digitos' => $ultimosDigitos,
                'mes_expiracao' => (int) $mes,
                'ano_expiracao' => (int) $ano,
                'nome_titular' => strtoupper($request->name),
                'gateway' => null, // Será definido quando o gateway tokenizar
                'token_gateway1' => null, // Será preenchido pelo gateway
                'token_gateway2' => null, // Será preenchido pelo gateway 2
                'is_default' => $request->is_default || $isPrimeiro,
            ]);

            DB::commit();

            Log::info('Cartão adicionado com sucesso', [
                'user_id' => Auth::id(),
                'cartao_id' => $cartao->id,
                'bandeira' => $bandeira,
            ]);

            return redirect()
                ->route('dash.show', ['section' => 'cartoes'])
                ->with('cartoes_success', 'Cartão adicionado com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao salvar cartão', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()
                ->back()
                ->with('cartoes_error', 'Erro ao salvar cartão. Tente novamente.');
        }
    }

    /**
     * Define um cartão como padrão
     */
    public function setDefault($id)
    {
        try {
            $cartao = Cartao::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            DB::beginTransaction();

            // Desmarcar todos os outros
            Cartao::where('user_id', Auth::id())->update(['is_default' => false]);

            // Marcar este como padrão
            $cartao->is_default = true;
            $cartao->save();

            DB::commit();

            Log::info('Cartão definido como padrão', [
                'user_id' => Auth::id(),
                'cartao_id' => $cartao->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cartão definido como padrão',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao definir cartão padrão', [
                'user_id' => Auth::id(),
                'cartao_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao definir cartão padrão',
            ], 500);
        }
    }

    /**
     * Remove um cartão
     */
    public function destroy($id)
    {
        try {
            $cartao = Cartao::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $wasDefault = $cartao->is_default;

            DB::beginTransaction();

            $cartao->delete();

            // Se era padrão, definir outro como padrão
            if ($wasDefault) {
                $proximoCartao = Cartao::where('user_id', Auth::id())
                    ->orderBy('created_at', 'desc')
                    ->first();

                if ($proximoCartao) {
                    $proximoCartao->is_default = true;
                    $proximoCartao->save();
                }
            }

            DB::commit();

            Log::info('Cartão removido', [
                'user_id' => Auth::id(),
                'cartao_id' => $id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cartão removido com sucesso',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao remover cartão', [
                'user_id' => Auth::id(),
                'cartao_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao remover cartão',
            ], 500);
        }
    }

    /**
     * Identifica a bandeira do cartão pelo número
     */
    private function identificarBandeira(string $numero): string
    {
        $primeiroDigito = substr($numero, 0, 1);
        $primeirosDois = substr($numero, 0, 2);
        $primeirosQuatro = substr($numero, 0, 4);
        $primeirosSeis = substr($numero, 0, 6);

        // Visa
        if ($primeiroDigito === '4') {
            return 'visa';
        }

        // Mastercard
        if (in_array($primeirosDois, ['51', '52', '53', '54', '55']) ||
            ($primeirosQuatro >= '2221' && $primeirosQuatro <= '2720')) {
            return 'mastercard';
        }

        // American Express
        if (in_array($primeirosDois, ['34', '37'])) {
            return 'amex';
        }

        // Elo
        $eloBins = [
            '4011', '4312', '4389', '4514', '4573',
            '5041', '5066', '5067',
            '6277', '6362', '6363',
            '506707', '506708', '506715', '506716', '506717', '506718',
            '506719', '506720', '506721', '506722', '506723', '506724',
            '506725', '506726', '506727', '506728', '506729', '506730',
        ];

        if (in_array($primeirosQuatro, $eloBins) || in_array($primeirosSeis, $eloBins)) {
            return 'elo';
        }

        // Hipercard
        if ($primeirosSeis === '606282' || $primeirosQuatro === '3841') {
            return 'hipercard';
        }

        // Diners Club
        if (in_array($primeirosDois, ['36', '38']) || $primeirosQuatro === '3095') {
            return 'diners';
        }

        // Discover
        if ($primeirosQuatro === '6011' || $primeirosDois === '65' || ($primeirosQuatro >= '6444' && $primeirosQuatro <= '6449')) {
            return 'discover';
        }

        return 'desconhecido';
    }
}
