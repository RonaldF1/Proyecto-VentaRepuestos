<?php

namespace App\Services;

use App\Models\Bitacora;
use App\Models\Pedido;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class ConectorLogisticoService
{
    /**
     * Ejecuta la cotización / creación de envío en el conector logístico (Shippo).
     */
    public function procesarEnvio(Pedido $pedido): void
    {
        $inicio = microtime(true);
        $correlacion = 'pedido-' . $pedido->id;
        $apiKey = env('SHIPPO_API_KEY');
        $apiUrl = rtrim((string) env('SHIPPO_API_URL', 'https://api.goshippo.com'), '/');

        try {
            $dir = $pedido->direccion_normalizada ?? [];
            $provincia = $dir['provincia'] ?? 'Alajuela';
            $canton = $dir['canton'] ?? 'San Carlos';
            $distrito = $dir['distrito'] ?? 'Quesada';
            $senas = $dir['otras_senas'] ?? $pedido->direccion_texto;

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
                        'name' => 'Destinatario Pedido #' . $pedido->id, // Sin PII real (Contrato C8)
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
                // Modo Sandbox local cuando no se provee SHIPPO_API_KEY
                $codigoHttp = 201;
                $referencia = 'shp_test_' . $pedido->id . '_' . Str::lower(Str::random(8));
                $detalle = 'cotizado';
            }

            $pedido->update([
                'conector_estado' => 'exito',
                'conector_detalle' => $detalle,
                'conector_referencia_externa' => $referencia,
                'conector_actualizado_en' => now(),
            ]);

            Bitacora::create([
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
        } catch (Throwable $e) {
            $pedido->update([
                'conector_estado' => 'fallo',
                'conector_detalle' => 'error_conector',
                'conector_actualizado_en' => now(),
            ]);

            Bitacora::create([
                'fecha' => now(),
                'direccion' => 'saliente',
                'sistema' => 'conector',
                'operacion' => 'ejecutar_accion',
                'estado' => 'fallo',
                'codigo_http' => null,
                'intentos' => 1,
                'duracion_ms' => (int) round((microtime(true) - $inicio) * 1000),
                'correlacion' => $correlacion,
                'error' => mb_substr($e->getMessage(), 0, 250),
            ]);

            throw $e; // Permite que el Job reintente según su backoff si aplica
        }
    }
}