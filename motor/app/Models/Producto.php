<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'productos';

    protected $fillable = [
        'id_tienda',
        'sku',
        'nombre',
        'tipo',
        'precio',
        'existencias',
        'categorias',
        'campo_propio_nombre',
        'campo_propio_valor',
        'sincronizado_en',
    ];

    protected $casts = [
        'id_tienda' => 'integer',
        'precio' => 'integer',
        'existencias' => 'integer',
        'categorias' => 'array',
        'sincronizado_en' => 'datetime',
    ];

    public function toContractArray(): array
    {
        return [
            'id' => (int) $this->id,
            'id_tienda' => (int) $this->id_tienda,
            'sku' => (string) $this->sku,
            'nombre' => (string) $this->nombre,
            'tipo' => (string) $this->tipo,
            'precio' => $this->precio !== null ? (int) $this->precio : null,
            'existencias' => $this->existencias !== null ? (int) $this->existencias : null,
            'categorias' => array_values($this->categorias ?? []),
            'campo_propio' => [
                'nombre' => (string) ($this->campo_propio_nombre ?: 'numero_parte_oem'),
                'valor' => $this->campo_propio_valor,
            ],
            'sincronizado_en' => $this->sincronizado_en ? $this->sincronizado_en->toISOString() : now()->toISOString(),
        ];
    }
}
