<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Producto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

        $query = Producto::query()->orderBy('id', 'asc');

        if ($sku !== null) {
            $query->where('sku', $sku);
        }

        return $this->paginar($request, $query, '/productos', fn(Producto $p) => $p->toContractArray());
    }
}
