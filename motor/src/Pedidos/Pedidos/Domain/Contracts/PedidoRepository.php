<?php

declare(strict_types=1);

namespace Src\Pedidos\Pedidos\Domain\Contracts;

interface PedidoRepository
{
    public function buscarPorId(int $id): ?array;
    public function guardar(array $datos): void;
}