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
        $cartoesUser = Cartao::where('user_id', Auth::id())->count();
   
        // Redirecionar para o dashboard com a seção de cartões ativa
        return redirect()->route('dash.show', ['section' => 'cartoes'])->with('cartoesUser', $cartoesUser);
    }

    /**
     * Salva um novo cartão
     */
    public function store(Request $request)
    {
        // Debug: Log dos dados recebidos
        Log::info('Tentativa de salvar cartão', [
            'user_id' => Auth::id(),
            'has_payment_method' => $request->has('payment_method_id'),
        ]);

        $request->validate([
            'payment_method_id' => 'required|string',
            'last4' => 'required|string|size:4',
            'brand' => 'required|string',
            'exp_month' => 'required|integer|min:1|max:12',
            'exp_year' => 'required|integer',
            'holder_name' => 'required|string|max:255',
            'cpf' => 'required|string|size:11',
            'is_default' => 'nullable|boolean',
        ]);

        try {
            DB::beginTransaction();

            // Validar limite de cartões (máximo 3)
            $totalCartoes = Cartao::where('user_id', Auth::id())->count();
            if ($totalCartoes >= 3) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você atingiu o limite de 3 cartões salvos.',
                ], 400);
            }

            // Verificar se já existe cartão com esses dados
            $cartaoExistente = Cartao::where('user_id', Auth::id())
                ->where('ultimos_digitos', $request->last4)
                ->where('mes_expiracao', $request->exp_month)
                ->where('ano_expiracao', $request->exp_year)
                ->first();

            if ($cartaoExistente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este cartão já está cadastrado.',
                ], 400);
            }

            // Se for marcado como padrão, desmarcar outros
            if ($request->is_default) {
                Cartao::where('user_id', Auth::id())->update(['is_default' => false]);
            }

            // Se for o primeiro cartão, torná-lo padrão automaticamente
            $isPrimeiro = $totalCartoes === 0;

            // Criar cartão com o PaymentMethod do Stripe
            $cartao = Cartao::create([
                'user_id' => Auth::id(),
                'bandeira' => strtolower($request->brand),
                'ultimos_digitos' => $request->last4,
                'mes_expiracao' => $request->exp_month,
                'ano_expiracao' => $request->exp_year,
                'nome_titular' => strtoupper($request->holder_name),
                'cpf' => $request->cpf,
                'gateway' => 'stripe',
                'token_gateway1' => $request->payment_method_id, // PaymentMethod ID do Stripe
                'token_gateway2' => null,
                'is_default' => $request->is_default || $isPrimeiro,
            ]);

            DB::commit();

            Log::info('Cartão tokenizado e salvo com sucesso', [
                'user_id' => Auth::id(),
                'cartao_id' => $cartao->id,
                'bandeira' => $request->brand,
                'gateway' => 'stripe',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cartão adicionado com sucesso!',
                'redirect' => route('dash.show', ['section' => 'cartoes']),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao salvar cartão', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao salvar cartão. Tente novamente.',
            ], 500);
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
