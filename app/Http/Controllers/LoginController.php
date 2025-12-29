<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function show()
    {
        if(Auth::check()) {
            return redirect()->route('dash.show');
        }

        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->getCredentials();

        if(!Auth::validate($credentials)):
            return redirect()->to('login')
                ->withInput($request->only('username'))
                ->withErrors([
                    'username' => 'E-mail/username ou senha incorretos. Verifique suas credenciais e tente novamente.'
                ]);
        endif;

        /** @var User $user */
        $user = Auth::getProvider()->retrieveByCredentials($credentials);

        // Verificar se o usuário está ativo (status = 1)
        if ($user && $user->status == 0) {
            return redirect()->to('login')
                ->withInput($request->only('username'))
                ->with('error', 'Sua conta está desativada. Entre em contato com o suporte.');
        }

        // Remember me por 1 ano (true = ativar remember me)
        Auth::login($user, true);

        return $this->authenticated($request, $user);
    }

    protected function authenticated(Request $request, $user)
    {
        return redirect()->route('dash.show');
    }
}
