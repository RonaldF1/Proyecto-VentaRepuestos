<?php

namespace App\Jobs;

use App\Models\Pedido;
use App\Services\ConectorLogisticoService;
use App\Services\NormalizadorDireccionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcesarPedidoWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [5, 15, 30];

    public function __construct(
        public int $pedidoId
    ) {}

    public function handle(
        NormalizadorDireccionService $normalizador,
        ConectorLogisticoService $conector
    ): void {
        $pedido = Pedido::find($this->pedidoId);
        if (!$pedido) {
            return;
        }

        // Paso 1 (3.5): Normalizar dirección con IA y validar contra DireccionNormalizada
        $resultadoDireccion = $normalizador->normalizar($pedido->direccion_texto, $pedido->id);

        $pedido->update([
            'direccion_estado' => $resultadoDireccion['estado'],
            'direccion_normalizada' => $resultadoDireccion['normalizada'],
        ]);

        // Si confianza < 0.85, queda pendiente_revision y no se envía al conector automáticamente
        if ($resultadoDireccion['estado'] !== 'normalizada') {
            $pedido->update([
                'conector_estado' => 'pendiente',
                'conector_detalle' => 'esperando_revision_direccion',
                'conector_actualizado_en' => now(),
            ]);
            return;
        }

        // Paso 2 (3.4): Ejecutar acción en el conector logístico
        $conector->procesarEnvio($pedido);
    }
}