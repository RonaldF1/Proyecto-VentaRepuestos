<?php

namespace App\Conectores;

use App\Models\Pedido;

interface ConectorInterface
{
    /**
     * Ejecuta la acción del conector sobre el pedido.
     *
     * @return array{estado: string, detalle: string, referencia_externa: string|null}
     */
    public function ejecutar(Pedido $pedido): array;
}
