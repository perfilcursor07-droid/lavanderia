<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EstabelecimentoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API Routes para Estabelecimentos
Route::prefix('estabelecimentos')->group(function () {
    Route::get('/', [EstabelecimentoController::class, 'index']);
    Route::post('/', [EstabelecimentoController::class, 'store']);
    Route::get('/ativos', [EstabelecimentoController::class, 'ativos']);
    Route::get('/buscar-cnpj', [EstabelecimentoController::class, 'buscarCnpj']);
    Route::get('/{estabelecimento}', [EstabelecimentoController::class, 'show']);
    Route::put('/{estabelecimento}', [EstabelecimentoController::class, 'update']);
    Route::delete('/{estabelecimento}', [EstabelecimentoController::class, 'destroy']);
    Route::post('/{estabelecimento}/toggle-status', [EstabelecimentoController::class, 'toggleStatus']);
});
