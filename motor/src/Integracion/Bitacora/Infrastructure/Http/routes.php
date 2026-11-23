<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Src\Integracion\Bitacora\Infrastructure\Http\Controllers\BitacoraController;

Route::get('/bitacora', [BitacoraController::class, 'index']);