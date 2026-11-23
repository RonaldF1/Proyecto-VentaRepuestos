<?php

declare(strict_types=1);

namespace Src\Catalogo\Productos\Infrastructure\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Catalogo\Productos\Infrastructure\Persistence\Models\ProductoModel;
use Src\Shared\Infrastructure\Http\ApiController;

class ProductoController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $sku = $request->query('sku');
        if ($sku !== null && mb_strlen($sku) > 100) {
            return $this->errorResponse('validacion', 'Los datos de la petición no son válidos.', [
                ['campo' => 'sku', 'mensaje' => 'Máximo 100 caracteres.'],
            ], 422);
        }

        $query = ProductoModel::query()->orderBy('id', 'asc');

        if ($sku !== null) {
            $query->where('sku', $sku);
        }

        return $this->paginar($request, $query, '/productos', fn(ProductoModel $p) => $p->toContractArray());
    }
}