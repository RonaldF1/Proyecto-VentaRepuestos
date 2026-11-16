<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Jobs\ProcesarPedidoWebhookJob;
use App\Models\Bitacora;
use App\Models\Pedido;
use App\Models\WebhookEntrega;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WebhookController extends Controller
{
    public function woocommerce(Request $request): JsonResponse
    {
        $inicio = microtime(true);
        $rawPayload = $request->getContent();
        $firmaRecibida = (string) $request->header('X-WC-Webhook-Signature', '');
        $deliveryId = trim((string) $request->header('X-WC-Webhook-Delivery-ID', ''));
        $secreto = (string) env('WC_WEBHOOK_SECRET', '');

        // 1. Validar firma HMAC-SHA256 (Punto 3.1)
        $firmaCalculada = base64_encode(hash_hmac('sha256', $rawPayload, $secreto, true));

        if ($secreto === '' || $firmaRecibida === '' || !hash_equals($firmaCalculada, $firmaRecibida)) {
            Bitacora::create([
                'fecha' => now(),
                'direccion' => 'entrante',
                'sistema' => 'tienda',
                'operacion' => 'webhook_pedido_creado',
                'estado' => 'firma_invalida',
                'codigo_http' => 401,
                'intentos' => 1,
                'duracion_ms' => (int) round((microtime(true) - $inicio) * 1000),
                'correlacion' => $deliveryId !== '' ? $deliveryId : null,
                'error' => 'Firma HMAC-SHA256 ausente o inválida.',
            ]);

            return $this->errorResponse(
                'no_autenticado',
                'La firma del webhook no es válida.',
                [],
                401
            );
        }

        // Ping inicial de verificación cuando WooCommerce crea/activa el webhook
        if ($request->has('webhook_id') && !$request->has('id')) {
            return response()->json([
                'estado' => 'activo',
                'mensaje' => 'Webhook verificado correctamente.',
            ], 200);
        }

        // Validar presencia de X-WC-Webhook-Delivery-ID
        if ($deliveryId === '') {
            return $this->errorResponse(
                'solicitud_invalida',
                'Falta el encabezado X-WC-Webhook-Delivery-ID.',
                [['campo' => 'X-WC-Webhook-Delivery-ID', 'mensaje' => 'El encabezado es obligatorio.']],
                400
            );
        }

        // 2. Idempotencia por Delivery-ID (Punto 3.2)
        if (WebhookEntrega::where('delivery_id', $deliveryId)->exists()) {
            Bitacora::create([
                'fecha' => now(),
                'direccion' => 'entrante',
                'sistema' => 'tienda',
                'operacion' => 'webhook_pedido_creado',
                'estado' => 'duplicado',
                'codigo_http' => 200,
                'intentos' => 1,
                'duracion_ms' => (int) round((microtime(true) - $inicio) * 1000),
                'correlacion' => $deliveryId,
                'error' => null,
            ]);

            return response()->json([
                'estado' => 'duplicado',
                'mensaje' => 'La entrega ya fue procesada anteriormente.',
            ], 200);
        }

        // 3. Validar estructura mínima del payload del pedido
        $data = json_decode($rawPayload, true);
        if (!is_array($data) || empty($data['id']) || empty($data['line_items']) || !is_array($data['line_items'])) {
            Bitacora::create([
                'fecha' => now(),
                'direccion' => 'entrante',
                'sistema' => 'tienda',
                'operacion' => 'webhook_pedido_creado',
                'estado' => 'fallo',
                'codigo_http' => 422,
                'intentos' => 1,
                'duracion_ms' => (int) round((microtime(true) - $inicio) * 1000),
                'correlacion' => $deliveryId,
                'error' => 'Payload de pedido incompleto o JSON malformado.',
            ]);

            return $this->errorResponse(
                'validacion',
                'Los datos del pedido no son válidos.',
                [['campo' => 'payload', 'mensaje' => 'Se requiere id y al menos una línea en line_items.']],
                422
            );
        }

        $pedidoId = (int) $data['id'];

        // Mapear líneas de pedido (montos en enteros, contrato C1/C5)
        $lineas = array_map(function ($item) {
            $cantidad = max(1, (int) ($item['quantity'] ?? 1));
            $subtotal = (int) round((float) ($item['subtotal'] ?? $item['total'] ?? 0));
            $precioUnitario = isset($item['price'])
                ? (int) round((float) $item['price'])
                : (int) round($subtotal / $cantidad);

            return [
                'sku' => !empty($item['sku']) ? (string) $item['sku'] : null,
                'nombre' => (string) ($item['name'] ?? 'Producto'),
                'cantidad' => $cantidad,
                'precio_unitario' => $precioUnitario,
                'subtotal' => $subtotal,
            ];
        }, $data['line_items']);

        // Extraer dirección SIN datos personales (Contrato C8: sin nombre, correo ni teléfono)
        $shipping = is_array($data['shipping'] ?? null) ? $data['shipping'] : [];
        $billing = is_array($data['billing'] ?? null) ? $data['billing'] : [];

        $partesDireccion = array_filter([
            $shipping['state'] ?? $billing['state'] ?? null,
            $shipping['city'] ?? $billing['city'] ?? null,
            $shipping['address_1'] ?? $billing['address_1'] ?? null,
            $shipping['address_2'] ?? $billing['address_2'] ?? null,
        ], fn ($v) => is_string($v) && trim($v) !== '');

        $direccionTexto = !empty($partesDireccion)
            ? implode(', ', array_map('trim', $partesDireccion))
            : 'Sin dirección especificada';

        $creadoEn = !empty($data['date_created_gmt'])
            ? Carbon::parse($data['date_created_gmt'], 'UTC')->setTimezone('America/Costa_Rica')
            : (!empty($data['date_created']) ? Carbon::parse($data['date_created']) : now());

        // Guardar entrega, pedido y bitácora de forma atómica
        DB::transaction(function () use ($deliveryId, $pedidoId, $data, $creadoEn, $lineas, $direccionTexto, $inicio) {
            WebhookEntrega::create([
                'delivery_id' => $deliveryId,
            ]);

            Pedido::updateOrCreate(
                ['id' => $pedidoId],
                [
                    'estado' => (string) ($data['status'] ?? 'pending'),
                    'moneda' => 'CRC',
                    'total' => (int) round((float) ($data['total'] ?? 0)),
                    'creado_en' => $creadoEn,
                    'lineas' => $lineas,
                    'direccion_estado' => 'sin_procesar',
                    'direccion_texto' => $direccionTexto,
                    'direccion_normalizada' => null,
                    'conector_estado' => 'pendiente',
                    'conector_detalle' => null,
                    'conector_referencia_externa' => null,
                    'conector_actualizado_en' => null,
                ]
            );

            Bitacora::create([
                'fecha' => now(),
                'direccion' => 'entrante',
                'sistema' => 'tienda',
                'operacion' => 'webhook_pedido_creado',
                'estado' => 'exito',
                'codigo_http' => 202,
                'intentos' => 1,
                'duracion_ms' => (int) round((microtime(true) - $inicio) * 1000),
                'correlacion' => $deliveryId,
                'error' => null,
            ]);
        });

        // 4. Despachar procesamiento asíncrono a la cola (Punto 3.3)
        ProcesarPedidoWebhookJob::dispatch($pedidoId);

        return response()->json([
            'estado' => 'aceptado',
            'pedido_id' => $pedidoId,
            'mensaje' => 'Webhook recibido y encolado para procesamiento.',
        ], 202);
    }

    private function errorResponse(string $codigo, string $mensaje, array $detalles, int $status): JsonResponse
    {
        return response()->json([
            'error' => [
                'codigo' => $codigo,
                'mensaje' => $mensaje,
                'detalles' => $detalles,
            ],
        ], $status);
    }
}