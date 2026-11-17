<?php

namespace App\Services;

use App\Conectores\ConectorInterface;
use App\Models\Pedido;

class ConectorLogisticoService
{
    public function __construct(
        protected ConectorInterface $conector
    ) {}

    /**
     * Ejecuta la cotización / creación de envío delegando en el conector registrado.
     */
    public function procesarEnvio(Pedido $pedido): array
    {
        return $this->conector->ejecutar($pedido);
    }
}
