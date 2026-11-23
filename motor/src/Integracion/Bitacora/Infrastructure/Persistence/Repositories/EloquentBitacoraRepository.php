<?php

declare(strict_types=1);

namespace Src\Integracion\Bitacora\Infrastructure\Persistence\Repositories;

use Src\Integracion\Bitacora\Domain\Contracts\BitacoraRepository;
use Src\Integracion\Bitacora\Infrastructure\Persistence\Models\BitacoraModel;

class EloquentBitacoraRepository implements BitacoraRepository
{
    public function registrar(array $datos): void
    {
        BitacoraModel::create($datos);
    }
}