<?php

use App\Http\Controllers\Api\V1\BitacoraController;
use App\Http\Controllers\Api\V1\PedidoController;
use App\Http\Controllers\Api\V1\ProductoController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\WebhookController;

Route::prefix('v1')->group(function () {
    Route::get('/productos', [ProductoController::class, 'index']);
    Route::get('/pedidos/{id}', [PedidoController::class, 'show']);
    Route::get('/bitacora', [BitacoraController::class, 'index']);
    Route::post('/webhooks/woocommerce', [WebhookController::class, 'woocommerce']);
});
