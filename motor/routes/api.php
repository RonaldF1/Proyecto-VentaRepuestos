<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    require base_path('src/Catalogo/Productos/Infrastructure/Http/routes.php');
    require base_path('src/Pedidos/Pedidos/Infrastructure/Http/routes.php');
    require base_path('src/Integracion/Bitacora/Infrastructure/Http/routes.php');
});