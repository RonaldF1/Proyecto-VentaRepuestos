<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $table = 'pedidos';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'id',
        'estado',
        'moneda',
        'total',
        'creado_en',
        'lineas',
        'direccion_estado',
        'direccion_texto',
        'direccion_normalizada',
        'conector_estado',
        'conector_detalle',
        'conector_referencia_externa',
        'conector_actualizado_en',
    ];

    protected $casts = [
        'id' => 'integer',
        'total' => 'integer',
        'creado_en' => 'datetime',
        'lineas' => 'array',
        'direccion_normalizada' => 'array',
        'conector_actualizado_en' => 'datetime',
    ];

    public function toContractArray(): array
    {
        return [
            'id' => (int) $this->id,
            'estado' => (string) $this->estado,
            'moneda' => (string) ($this->moneda ?: 'CRC'),
            'total' => (int) $this->total,
            'creado_en' => $this->creado_en ? $this->creado_en->toISOString() : now()->toISOString(),
            'lineas' => array_map(function ($l) {
                return [
                    'sku' => isset($l['sku']) ? (string) $l['sku'] : null,
                    'nombre' => (string) ($l['nombre'] ?? ''),
                    'cantidad' => (int) ($l['cantidad'] ?? 1),
                    'precio_unitario' => (int) ($l['precio_unitario'] ?? 0),
                    'subtotal' => (int) ($l['subtotal'] ?? (($l['cantidad'] ?? 1) * ($l['precio_unitario'] ?? 0))),
                ];
            }, $this->lineas ?? []),
            'direccion' => [
                'estado' => (string) $this->direccion_estado,
                'texto' => (string) $this->direccion_texto,
                'normalizada' => $this->direccion_normalizada ? [
                    'provincia' => (string) ($this->direccion_normalizada['provincia'] ?? ''),
                    'canton' => (string) ($this->direccion_normalizada['canton'] ?? ''),
                    'distrito' => (string) ($this->direccion_normalizada['distrito'] ?? ''),
                    'otras_senas' => (string) ($this->direccion_normalizada['otras_senas'] ?? ''),
                    'confianza' => (float) ($this->direccion_normalizada['confianza'] ?? 0),
                ] : null,
            ],
            'conector' => [
                'estado' => (string) $this->conector_estado,
                'detalle' => $this->conector_detalle ? (string) $this->conector_detalle : null,
                'referencia_externa' => $this->conector_referencia_externa ? (string) $this->conector_referencia_externa : null,
                'actualizado_en' => $this->conector_actualizado_en ? $this->conector_actualizado_en->toISOString() : null,
            ],
        ];
    }
}
