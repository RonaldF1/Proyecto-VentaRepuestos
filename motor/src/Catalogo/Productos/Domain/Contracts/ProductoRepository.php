<?php

declare(strict_types=1);

namespace Src\Catalogo\Productos\Domain\Contracts;

interface ProductoRepository
{
    public function buscarPorSku(string $sku): ?array;
    public function guardarDesdeTienda(array $datos): void;
}