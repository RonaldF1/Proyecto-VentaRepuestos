<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Src\Catalogo\Productos\Infrastructure\Http\Controllers\ProductoController;

Route::get('/productos', [ProductoController::class, 'index']);