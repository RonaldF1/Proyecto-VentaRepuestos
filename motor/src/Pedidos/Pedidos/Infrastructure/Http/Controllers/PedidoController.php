<?php

declare(strict_types=1);

namespace Src\Pedidos\Pedidos\Infrastructure\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Src\Pedidos\Pedidos\Infrastructure\Persistence\Models\PedidoModel;
use Src\Shared\Infrastructure\Http\ApiController;

class PedidoController extends ApiController
{
    public function show(string $id): JsonResponse
    {
        if (!ctype_digit((string) $id)) {
            return $this->errorResponse('no_encontrado', 'La ruta no existe.', [], 404);
        }

        $pedido = PedidoModel::find((int) $id);
        if (!$pedido) {
            return $this->errorResponse('no_encontrado', "No existe un pedido con id {$id}.", [], 404);
        }

        return response()->json($pedido->toContractArray(), 200);
    }
}