<?php

declare(strict_types=1);

namespace Src\Pedidos\Pedidos\Infrastructure\Adapters;

use App\Conectores\ConectorInterface;
use App\Models\Pedido as LegacyPedido;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Src\Integracion\Bitacora\Infrastructure\Persistence\Models\BitacoraModel;
use Src\Pedidos\Pedidos\Domain\Contracts\ConectorLogisticoPort;
use Src\Pedidos\Pedidos\Infrastructure\Persistence\Models\PedidoModel;
use Throwable;

class ShippoConectorAdapter implements ConectorLogisticoPort, ConectorInterface
{
    public function ejecutar($pedido): array
    {
        $inicio = microtime(true);
        $pedidoId = is_array($pedido) ? ($pedido['id'] ?? 0) : $pedido->id;
        $correlacion = (string) $pedidoId;
        $apiKey = config('services.shippo.key');
        $apiUrl = rtrim((string) config('services.shippo.url', 'https://api.goshippo.com'), '/');

        try {
            $dir = is_array($pedido) ? ($pedido['direccion_normalizada'] ?? []) : ($pedido->direccion_normalizada ?? []);
            $texto = is_array($pedido) ? ($pedido['direccion_texto'] ?? '') : ($pedido->direccion_texto ?? '');
            $provincia = $dir['provincia'] ?? 'Alajuela';
            $canton = $dir['canton'] ?? 'San Carlos';
            $senas = $dir['otras_senas'] ?? $texto;

            if (!empty($apiKey)) {
                $payload = [
                    'address_from' => [
                        'name' => 'Bodega Repuestos RDC',
                        'street1' => 'Ciudad Quesada, Centro',
                        'city' => 'San Carlos',
                        'state' => 'Alajuela',
                        'zip' => '21001',
                        'country' => 'CR',
                    ],
                    'address_to' => [
                        'name' => 'Destinatario Pedido #' . $pedidoId,
                        'street1' => mb_substr($senas, 0, 100),
                        'city' => $canton,
                        'state' => $provincia,
                        'zip' => '10101',
                        'country' => 'CR',
                    ],
                    'parcels' => [[
                        'length' => '30',
                        'width' => '20',
                        'height' => '15',
                        'distance_unit' => 'cm',
                        'weight' => '2',
                        'mass_unit' => 'kg',
                    ]],
                    'async' => false,
                ];

                $response = Http::withHeaders([
                    'Authorization' => 'ShippoToken ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])->timeout(15)->post("{$apiUrl}/shipments/", $payload);

                $codigoHttp = $response->status();

                if (!$response->successful()) {
                    throw new \RuntimeException("Shippo respondió HTTP {$codigoHttp}");
                }

                $referencia = (string) ($response->json('object_id') ?? ('shp_' . Str::random(16)));
                $detalle = 'cotizado';
            } else {
                $codigoHttp = 201;
                $referencia = 'shp_test_' . $pedidoId . '_' . Str::lower(Str::random(8));
                $detalle = 'cotizado';
            }

            BitacoraModel::create([
                'fecha' => now(),
                'direccion' => 'saliente',
                'sistema' => 'conector',
                'operacion' => 'ejecutar_accion',
                'estado' => 'exito',
                'codigo_http' => $codigoHttp,
                'intentos' => 1,
                'duracion_ms' => (int) round((microtime(true) - $inicio) * 1000),
                'correlacion' => $correlacion,
                'error' => null,
            ]);

            return [
                'estado' => 'exito',
                'detalle' => $detalle,
                'referencia_externa' => $referencia,
            ];
        } catch (Throwable $e) {
            BitacoraModel::create([
                'fecha' => now(),
                'direccion' => 'saliente',
                'sistema' => 'conector',
                'operacion' => 'ejecutar_accion',
                'estado' => 'fallo',
                'codigo_http' => isset($response) ? $response->status() : null,
                'intentos' => 1,
                'duracion_ms' => (int) round((microtime(true) - $inicio) * 1000),
                'correlacion' => $correlacion,
                'error' => mb_substr($e->getMessage(), 0, 250),
            ]);

            return [
                'estado' => 'fallo',
                'detalle' => 'error_conector',
                'referencia_externa' => null,
            ];
        }
    }
}