<?php

declare(strict_types=1);

namespace Src\Pedidos\Pedidos\Infrastructure\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Src\Pedidos\Pedidos\Domain\Contracts\ConectorLogisticoPort;
use Src\Pedidos\Pedidos\Domain\Contracts\NormalizadorDireccionPort;
use Src\Pedidos\Pedidos\Infrastructure\Persistence\Models\PedidoModel;

class ProcesarPedidoWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [5, 15, 30];

    public function __construct(
        public int $pedidoId
    ) {}

    public function handle(
        NormalizadorDireccionPort $normalizador,
        ConectorLogisticoPort $conector
    ): void {
        $pedido = PedidoModel::find($this->pedidoId);
        if (!$pedido) {
            return;
        }

        $resultadoDireccion = $normalizador->normalizar($pedido->direccion_texto, $pedido->id);

        $pedido->update([
            'direccion_estado' => $resultadoDireccion['estado'],
            'direccion_normalizada' => $resultadoDireccion['normalizada'],
        ]);

        if ($resultadoDireccion['estado'] !== 'normalizada') {
            $pedido->update([
                'conector_estado' => 'pendiente',
                'conector_detalle' => 'esperando_revision_direccion',
                'conector_actualizado_en' => now(),
            ]);
            return;
        }

        $resultado = $conector->ejecutar($pedido);

        $pedido->update([
            'conector_estado' => $resultado['estado'] ?? 'fallo',
            'conector_detalle' => $resultado['detalle'] ?? null,
            'conector_referencia_externa' => $resultado['referencia_externa'] ?? null,
            'conector_actualizado_en' => now(),
        ]);
    }
}