<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\PostbackController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//Postbacks
Route::post('/postback/transacao', [PostbackController::class, 'handle']);

//API Endpoints

Route::get('/transacao/{transacao_id}', [ApiController::class, 'transacao_status']);
