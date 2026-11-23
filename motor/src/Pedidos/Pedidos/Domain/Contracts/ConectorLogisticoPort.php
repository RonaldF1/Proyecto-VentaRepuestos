<?php

declare(strict_types=1);

namespace Src\Pedidos\Pedidos\Domain\Contracts;

interface ConectorLogisticoPort
{
    /**
     * @return array{estado: string, detalle: string|null, referencia_externa: string|null}
     */
    public function ejecutar(array $pedido): array;
}