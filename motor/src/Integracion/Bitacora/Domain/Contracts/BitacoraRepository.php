<?php

declare(strict_types=1);

namespace Src\Integracion\Bitacora\Domain\Contracts;

interface BitacoraRepository
{
    public function registrar(array $datos): void;
}