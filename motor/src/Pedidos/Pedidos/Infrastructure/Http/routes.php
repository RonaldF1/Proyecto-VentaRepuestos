<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Src\Pedidos\Pedidos\Infrastructure\Http\Controllers\PedidoController;
use Src\Pedidos\Pedidos\Infrastructure\Http\Controllers\WebhookController;

Route::get('/pedidos/{id}', [PedidoController::class, 'show']);
Route::post('/webhooks/woocommerce', [WebhookController::class, 'woocommerce']);