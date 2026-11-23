<?php

declare(strict_types=1);

namespace Src\Pedidos\Pedidos\Domain\Contracts;

interface NormalizadorDireccionPort
{
    /**
     * @return array{estado: string, normalizada: array|null}
     */
    public function normalizar(string $direccionTexto, int $pedidoId): array;
}