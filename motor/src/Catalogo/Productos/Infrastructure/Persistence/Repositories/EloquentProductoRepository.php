<?php

declare(strict_types=1);

namespace Src\Catalogo\Productos\Infrastructure\Persistence\Repositories;

use Src\Catalogo\Productos\Domain\Contracts\ProductoRepository;
use Src\Catalogo\Productos\Infrastructure\Persistence\Models\ProductoModel;

class EloquentProductoRepository implements ProductoRepository
{
    public function buscarPorSku(string $sku): ?array
    {
        $item = ProductoModel::where('sku', $sku)->first();
        return $item ? $item->toContractArray() : null;
    }

    public function guardarDesdeTienda(array $datos): void
    {
        ProductoModel::updateOrCreate(
            ['id_tienda' => $datos['id_tienda']],
            $datos
        );
    }
}