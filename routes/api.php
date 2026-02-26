<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\PostbackController;
use App\Http\Controllers\WebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//Postbacks
Route::post('/postback/transacao', [PostbackController::class, 'handle'])->name('postback.transacao');

//Webhooks
Route::post('/webhook/abacatepay', [WebhookController::class, 'abacatepay']);
Route::post('/webhook/asaas', [WebhookController::class, 'asaas']);
Route::post('/webhook/xgate', [WebhookController::class, 'xgate']);

// Alias sem prefixo /api (compatibilidade com painéis que não suportam /api)
Route::post('/xgate-webhook', [WebhookController::class, 'xgate']);

//API Endpoints
Route::get('/transacao/{transacao_id}', [ApiController::class, 'transacao_status']);

// Status de geração de proxies (usa autenticação web padrão via session)
Route::middleware('web')->group(function () {
    Route::get('/vps/status-geracao', [App\Http\Controllers\AdminController::class, 'statusGeracao']);
});
