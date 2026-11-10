<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
    protected $table = 'bitacoras';

    protected $fillable = [
        'fecha',
        'direccion',
        'sistema',
        'operacion',
        'estado',
        'codigo_http',
        'intentos',
        'duracion_ms',
        'correlacion',
        'error',
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'codigo_http' => 'integer',
        'intentos' => 'integer',
        'duracion_ms' => 'integer',
    ];

    public function toContractArray(): array
    {
        return [
            'id' => (int) $this->id,
            'fecha' => $this->fecha ? $this->fecha->toISOString() : now()->toISOString(),
            'direccion' => (string) $this->direccion,
            'sistema' => (string) $this->sistema,
            'operacion' => (string) $this->operacion,
            'estado' => (string) $this->estado,
            'codigo_http' => $this->codigo_http !== null ? (int) $this->codigo_http : null,
            'intentos' => (int) ($this->intentos ?: 1),
            'duracion_ms' => (int) ($this->duracion_ms ?? 0),
            'correlacion' => $this->correlacion !== null ? (string) $this->correlacion : null,
            'error' => $this->error !== null ? (string) $this->error : null,
        ];
    }
}
