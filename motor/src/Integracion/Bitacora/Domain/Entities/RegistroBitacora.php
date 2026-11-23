<?php

declare(strict_types=1);

namespace Src\Integracion\Bitacora\Domain\Entities;

final class RegistroBitacora
{
    public function __construct(
        public readonly int $id,
        public readonly string $fecha,
        public readonly string $direccion,
        public readonly string $sistema,
        public readonly string $operacion,
        public readonly string $estado,
        public readonly ?int $codigoHttp,
        public readonly int $intentos,
        public readonly int $duracionMs,
        public readonly ?string $correlacion,
        public readonly ?string $error
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'fecha' => $this->fecha,
            'direccion' => $this->direccion,
            'sistema' => $this->sistema,
            'operacion' => $this->operacion,
            'estado' => $this->estado,
            'codigo_http' => $this->codigoHttp,
            'intentos' => $this->intentos,
            'duracion_ms' => $this->duracionMs,
            'correlacion' => $this->correlacion,
            'error' => $this->error,
        ];
    }
}