<?php

use App\Http\Controllers\LogadoController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\PostbackController;
use App\Http\Controllers\RecuperarSenhaController;
use App\Http\Controllers\RegisterController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CartaoController;
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
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.perform');

//Rota de logout
Route::get('/logout', [LogoutController::class, 'perform'])->name('logout.perform');

//Rotas de registro
Route::get('/register', [RegisterController::class, 'show'])->name('register.show');
Route::post('/register', [RegisterController::class, 'register'])->name('register.perform');

//Rotas de recuperação de senha
Route::get('/esqueci-senha', [RecuperarSenhaController::class, 'formSolicitar'])->name('senha.show');
Route::post('/esqueci-senha', [RecuperarSenhaController::class, 'enviarLink'])->name('senha.enviar');
Route::get('/redefinir-senha/{token}', [RecuperarSenhaController::class, 'formRedefinir'])->name('senha.redefinir.form');
Route::post('/redefinir-senha', [RecuperarSenhaController::class, 'redefinir'])->name('senha.redefinir');


//Rotas de usuários logados
Route::middleware(['auth', CheckUserStatus::class])->group(function () {

    // Dashboard principal - Gerenciar Proxies
    Route::get('/dash', [LogadoController::class, 'dash'])->name('dash.show');

    // Meu Perfil
    Route::get('/perfil', [LogadoController::class, 'perfil'])->name('perfil.show');
    Route::post('/perfil/atualizar', [LogadoController::class, 'atualizarPerfil'])->name('perfil.atualizar');
    Route::post('/perfil/alterar-senha', [LogadoController::class, 'alterarSenha'])->name('perfil.senha');
    Route::post('/perfil/salvar-dados', [LogadoController::class, 'salvarDadosIniciais'])->name('perfil.salvar-dados');

    // Ordens / Proxies Ativos
    Route::get('/proxies', [LogadoController::class, 'proxies'])->name('proxies.show');
    Route::post('/proxies/renovar', [LogadoController::class, 'renovarProxy'])->name('proxies.renovar');
    Route::post('/proxies/renovar-pix', [LogadoController::class, 'processarRenovacao'])->name('proxies.renovar-pix');
    Route::post('/proxies/exportar', [LogadoController::class, 'exportarProxies'])->name('proxies.exportar');

    // Nova Compra
    Route::get('/nova-compra', [LogadoController::class, 'novaCompra'])->name('compra.nova');
    Route::post('/nova-compra/processar', [LogadoController::class, 'processarCompra'])->name('compra.processar');

    // Histórico de Transações
    Route::get('/transacoes', [LogadoController::class, 'transacoes'])->name('transacoes.show');

    // Carteira / Saldo
    Route::get('/saldo', [LogadoController::class, 'saldo'])->name('saldo.show');
    Route::post('/saldo/adicionar', [LogadoController::class, 'adicionarSaldo'])->name('saldo.adicionar');

    // Cartões de Crédito
    Route::get('/cartoes', [CartaoController::class, 'index'])->name('cartoes.index');
    Route::post('/cartoes', [CartaoController::class, 'store'])->name('cartoes.store');
    Route::post('/cartoes/{id}/default', [CartaoController::class, 'setDefault'])->name('cartoes.default');
    Route::delete('/cartoes/{id}', [CartaoController::class, 'destroy'])->name('cartoes.destroy');

    // Testar Proxy
    Route::post('/proxies/testar', [LogadoController::class, 'testarProxy'])->name('proxies.testar');

    // Outras rotas existentes
    Route::get('/cupons', [LogadoController::class, 'cupons'])->name('cupons.show');
    Route::get('/duvidas', [LogadoController::class, 'duvidas'])->name('duvidas.show');
    Route::get('/api', [LogadoController::class, 'api'])->name('api.show');
    Route::get('/suporte', [LogadoController::class, 'suporte'])->name('suporte.show');
    Route::get('/configuracoes', [LogadoController::class, 'configuracoes'])->name('configuracoes.show');

    Route::post('/pcm', [RecuperarSenhaController::class, 'recuperar_senha_main'])->name('trocar.senha.main');

});

//Rotas de administradores
Route::middleware(AdminMiddleware::class)->group(function () {
    Route::get('/admin/proxies', [AdminController::class, 'proxies'])->name('admin.proxies');
    Route::post('/admin/vps/cadastrar', [AdminController::class, 'cadastrarVps'])->name('vps.cadastrar');
    Route::get('/admin/historico-vps', [AdminController::class, 'historicoVps'])->name('admin.historico-vps');
    Route::get('/admin/usuarios', [AdminController::class, 'usuarios'])->name('usuarios.show');

    // Relatórios financeiros
    Route::get('/admin/relatorios', [AdminController::class, 'relatorios'])->name('admin.relatorios');

    // Transações / Vendas
    Route::get('/admin/transacoes', [AdminController::class, 'transacoes'])->name('admin.transacoes');

    // Gerenciamento de portas (bloqueio/desbloqueio)
    Route::post('/admin/proxy/bloquear', [AdminController::class, 'bloquearProxy'])->name('proxy.bloquear');
    Route::post('/admin/proxy/desbloquear', [AdminController::class, 'desbloquearProxy'])->name('proxy.desbloquear');

    // Gerenciamento de uso interno
    Route::post('/admin/proxy/uso-interno', [AdminController::class, 'marcarUsoInterno'])->name('proxy.uso-interno');
    Route::post('/admin/proxy/remover-uso-interno', [AdminController::class, 'removerUsoInterno'])->name('proxy.remover-uso-interno');

    // Atualizar apelido da VPS
    Route::post('/admin/vps/atualizar-apelido', [AdminController::class, 'atualizarApelidoVps'])->name('vps.atualizar-apelido');

    // Atualizar país da VPS
    Route::post('/admin/vps/atualizar-pais', [AdminController::class, 'atualizarPaisVps'])->name('vps.atualizar-pais');

    // Atualizar dados da VPS
    Route::post('/admin/vps/atualizar', [AdminController::class, 'atualizarVps'])->name('vps.atualizar');

    // Atualizar cargo do usuário
    Route::patch('/admin/usuarios/{id}/cargo', [AdminController::class, 'atualizarCargo'])->name('usuarios.atualizar-cargo');
});