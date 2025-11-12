<?php

use App\Http\Controllers\LogadoController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\PostbackController;
use App\Http\Controllers\RecuperarSenhaController;
use App\Http\Controllers\RegisterController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckUserStatus;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

//Rota página inicial
Route::get('/', function () {
    return view('index');
})->name('inicial');

Route::get('/faq', function() {
    return view('faq');
})->name('faq');

//Rotas de login
Route::get('/login', [LoginController::class, 'show'])->name('login.show');
Route::post('/login', [LoginController::class, 'login'])->name('login.perform');

//Rota de logout
Route::get('/logout', [LogoutController::class, 'perform'])->name('logout.perform');

//Rotas de registro
Route::get('/register', [RegisterController::class, 'show'])->name('register.show');
Route::post('/register', [RegisterController::class, 'register'])->name('register.perform');

//Rotas de recuperação de senha
Route::get('/esqueci-senha', [RecuperarSenhaController::class, 'formSolicitar'])->name('senha.show');
Route::post('/esqueci-senha', [RecuperarSenhaController::class, 'enviarLink'])->name('senha.enviar');
Route::get('/redefinir-senha/{token}', [RecuperarSenhaController::class, 'formRedefinir'])->name('senha.redefinir');
Route::post('/redefinir-senha', [RecuperarSenhaController::class, 'redefinir'])->name('senha.redefinir');


//Rotas de usuários logados
Route::middleware(['auth', CheckUserStatus::class])->group(function () {

    Route::get('/dashboard', [LogadoController::class, 'dashboard'])->name('dashboard.show');
    Route::get('/saldo', [LogadoController::class, 'saldo'])->name('saldo.show');
    Route::get('/socks5', [LogadoController::class, 'socks5'])->name('socks5.show');
    Route::get('/transacoes', [LogadoController::class, 'transacoes'])->name('transacoes.show');
    Route::get('/cupons', [LogadoController::class, 'cupons'])->name('cupons.show');
    Route::get('/duvidas', [LogadoController::class, 'duvidas'])->name('duvidas.show');
    Route::get('/api', [LogadoController::class, 'api'])->name('api.show');
    Route::get('/comprar', [LogadoController::class, 'comprar_proxies'])->name('comprar.show');
    Route::get('/pagamento', [LogadoController::class, 'pagamento'])->name('pagamento.show');

    Route::post('/pcm', [RecuperarSenhaController::class, 'recuperar_senha_main'])->name('trocar.senha.main');

    Route::get('/api2', function () {
        $usuario = User::where('id', Auth::id())->first();
        return view('logado.api2', compact('usuario'));
    });

});

//Rotas de administradores
Route::middleware(AdminMiddleware::class)->group(function () {



});
