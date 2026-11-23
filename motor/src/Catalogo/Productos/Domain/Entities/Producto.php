<?php

declare(strict_types=1);

namespace Src\Catalogo\Productos\Domain\Entities;

final class Producto
{
    public function __construct(
        public readonly int $id,
        public readonly int $idTienda,
        public readonly string $sku,
        public readonly string $nombre,
        public readonly string $tipo,
        public readonly ?int $precio,
        public readonly ?int $existencias,
        public readonly array $categorias,
        public readonly string $campoPropioNombre,
        public readonly ?string $campoPropioValor,
        public readonly string $sincronizadoEn
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'id_tienda' => $this->idTienda,
            'sku' => $this->sku,
            'nombre' => $this->nombre,
            'tipo' => $this->tipo,
            'precio' => $this->precio,
            'existencias' => $this->existencias,
            'categorias' => $this->categorias,
            'campo_propio' => [
                'nombre' => $this->campoPropioNombre,
                'valor' => $this->campoPropioValor,
            ],
            'sincronizado_en' => $this->sincronizadoEn,
        ];
    }
}