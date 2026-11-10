<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Pedido;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PedidoController extends ApiController
{
    public function show(string $id): JsonResponse
    {
        if (!ctype_digit((string) $id)) {
            return $this->errorResponse('no_encontrado', "La ruta no existe.", [], 404);
        }

        $pedido = Pedido::find((int) $id);
        if (!$pedido) {
            return $this->errorResponse('no_encontrado', "No existe un pedido con id {$id}.", [], 404);
        }

        return response()->json($pedido->toContractArray(), 200);
    }
}
