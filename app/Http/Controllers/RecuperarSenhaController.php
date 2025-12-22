<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Mail\RecuperarSenhaMail;

class RecuperarSenhaController extends Controller
{
    public function formSolicitar()
    {
        return view('auth.esqueci-senha');
    }

    public function enviarLink(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $token = Str::random(60);
        $email = $request->email;

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'email' => $email,
                'token' => Hash::make($token),
                'created_at' => now()
            ]
        );

        Mail::to($email)->send(new RecuperarSenhaMail($token, $email));

        return redirect()->route('senha.show')->with('success', 'Link de recuperação enviado para seu e-mail.');
    }

    public function formRedefinir($token){

        return view('auth.redefinir-senha', compact('token'));

    }

    public function redefinir(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $reset = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$reset) {
            return back()->withErrors(['email' => 'Token inválido ou expirado.']);
        }

        if (!Hash::check($request->token, $reset->token)) {
            return back()->withErrors(['email' => 'Token inválido ou expirado.']);
        }

        if (now()->diffInMinutes($reset->created_at) > 60) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['email' => 'Token expirado. Solicite um novo link de recuperação.']);
        }

        $user = User::where('email', $request->email)->first();
        $user->password = $request->password;
        $user->save();

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Senha alterada com sucesso!');
    }

    public function recuperarsenhav2()
    {
        return view('auth.esqueci-senhav2');
    }

    public function recuperar_senha_main(Request $request)
    {
        //dd($request->all());
        User::updateOrCreate(
            ['id' => Auth::id()],
            [
                'password' => $request->password,
            ]
        );

        return redirect()->back()->with('success', 'Senha alterada com sucesso');
    }
}

