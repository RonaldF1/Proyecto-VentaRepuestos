<?php

use App\Conectores\ShippoConector;
use App\Jobs\ProcesarPedidoWebhookJob;
use App\Models\Bitacora;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\WebhookEntrega;
use App\Services\NormalizadorDireccionService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    Http::preventStrayRequests();
    Config::set('services.webhook.secret', 'secreto-test-pest-2026');
    Config::set('services.woocommerce.url', 'https://tienda-repuestos-test.local');
    Config::set('services.woocommerce.key', 'ck_test');
    Config::set('services.woocommerce.secret', 'cs_test');
    Config::set('services.shippo.key', 'shippo_test_token_123');
    Config::set('services.shippo.url', 'https://api.goshippo.com');
    WebhookEntrega::where('delivery_id', 'like', 'wc-delivery-%')->delete();
    Pedido::whereIn('id', [7001, 7002, 7003, 7005, 7006])->delete();
    Bitacora::whereIn('correlacion', ['wc-delivery-test-001', 'wc-delivery-invalida-002', 'wc-delivery-repetida-003', '7005', '7006', 'pagina-1'])->delete();
});

// 1. Webhook con firma vÃ¡lida guarda el pedido
it('1. guarda el pedido cuando recibe un webhook con firma HMAC-SHA256 valida', function () {
    $secret = config('services.webhook.secret');
    $deliveryId = 'wc-delivery-test-001';
    $payload = [
        'id' => 7001,
        'status' => 'processing',
        'total' => '24500',
        'currency' => 'CRC',
        'line_items' => [
            [
                'id' => 10,
                'name' => 'Pastillas de freno Toyota',
                'sku' => 'FRN-TOY-01',
                'quantity' => 2,
                'price' => '12250',
                'total' => '24500',
            ],
        ],
        'shipping' => [
            'state' => 'Alajuela',
            'city' => 'San Carlos',
            'address_1' => '200 metros este del parque',
        ],
    ];

    $rawPayload = json_encode($payload);
    $signature = base64_encode(hash_hmac('sha256', $rawPayload, $secret, true));

    $response = $this->call(
        'POST',
        '/api/v1/webhooks/woocommerce',
        [],
        [],
        [],
        [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_WC_WEBHOOK_SIGNATURE' => $signature,
            'HTTP_X_WC_WEBHOOK_DELIVERY_ID' => $deliveryId,
        ],
        $rawPayload
    );

    $response->assertStatus(202);
    $this->assertDatabaseHas('pedidos', ['id' => 7001]);
    $this->assertDatabaseHas('webhook_entregas', ['delivery_id' => $deliveryId]);
});

// 2. Webhook con firma invÃ¡lida responde 401 y no guarda nada
it('2. responde 401 y no guarda nada ante una firma HMAC invalida', function () {
    $deliveryId = 'wc-delivery-invalida-002';
    $payload = [
        'id' => 7002,
        'status' => 'processing',
        'line_items' => [
            ['name' => 'BujÃ­a', 'quantity' => 1, 'price' => 3000],
        ],
    ];

    $rawPayload = json_encode($payload);

    $response = $this->call(
        'POST',
        '/api/v1/webhooks/woocommerce',
        [],
        [],
        [],
        [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_WC_WEBHOOK_SIGNATURE' => 'firma_totalmente_falsa==',
            'HTTP_X_WC_WEBHOOK_DELIVERY_ID' => $deliveryId,
        ],
        $rawPayload
    );

    $response->assertStatus(401);
    $response->assertJsonPath('error.codigo', 'no_autenticado');
    $this->assertDatabaseMissing('pedidos', ['id' => 7002]);
});

// 3. Entrega duplicada (Delivery-ID repetido) no se procesa dos veces (idempotencia)
it('3. garantiza idempotencia respondiendo 200 duplicado si el Delivery-ID ya fue procesado', function () {
    $deliveryId = 'wc-delivery-repetida-003';
    WebhookEntrega::updateOrCreate(['delivery_id' => $deliveryId]);

    $secret = config('services.webhook.secret');
    $payload = [
        'id' => 7003,
        'status' => 'processing',
        'line_items' => [
            ['name' => 'Filtro', 'quantity' => 1, 'price' => 5000],
        ],
    ];
    $rawPayload = json_encode($payload);
    $signature = base64_encode(hash_hmac('sha256', $rawPayload, $secret, true));

    $response = $this->call(
        'POST',
        '/api/v1/webhooks/woocommerce',
        [],
        [],
        [],
        [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_WC_WEBHOOK_SIGNATURE' => $signature,
            'HTTP_X_WC_WEBHOOK_DELIVERY_ID' => $deliveryId,
        ],
        $rawPayload
    );

    $response->assertStatus(200);
    $response->assertJsonPath('estado', 'duplicado');
});

// 4. Si la tienda responde 500, el motor reintenta 3 veces y anota en bitÃ¡cora
it('4. reintenta 3 veces y anota en bitacora si la API de WooCommerce responde 500', function () {
    Http::fake([
        'https://tienda-repuestos-test.local/wp-json/wc/v3/products*' => Http::response([
            'error' => 'Internal Server Error'
        ], 500),
    ]);

    $exitCode = Artisan::call('tienda:sincronizar-productos');

    expect($exitCode)->toBe(1);

    $bitacora = Bitacora::where('sistema', 'tienda')
        ->where('operacion', 'listar_productos')
        ->where('estado', 'fallo')
        ->latest('id')
        ->first();

    expect($bitacora)->not->toBeNull();
    expect($bitacora->intentos)->toBe(3);
    expect($bitacora->codigo_http)->toBe(500);
});

// 5. Si el conector falla, el pedido queda guardado y el fallo en bitÃ¡cora
it('5. mantiene el pedido guardado y registra el fallo en bitacora si el conector logistico falla', function () {
    Http::fake([
        'https://api.goshippo.com/shipments/' => Http::response([
            'error' => 'Carrier connection error'
        ], 500),
    ]);

    $pedido = Pedido::updateOrCreate(
        ['id' => 7005],
        [
            'estado' => 'processing',
            'moneda' => 'CRC',
            'total' => 15000,
            'creado_en' => now(),
            'lineas' => [['nombre' => 'Amortiguador', 'cantidad' => 1, 'precio_unitario' => 15000]],
            'direccion_estado' => 'normalizada',
            'direccion_texto' => 'Ciudad Quesada, Alajuela',
            'direccion_normalizada' => [
                'provincia' => 'Alajuela',
                'canton' => 'San Carlos',
                'distrito' => 'Quesada',
                'otras_senas' => '200 metros norte',
                'confianza' => 0.95,
            ],
            'conector_estado' => 'pendiente',
        ]
    );

    $conector = new ShippoConector();
    $resultado = $conector->ejecutar($pedido);

    expect($resultado['estado'])->toBe('fallo');

    $pedido->update([
        'conector_estado' => $resultado['estado'],
        'conector_detalle' => $resultado['detalle'],
    ]);

    $this->assertDatabaseHas('pedidos', [
        'id' => 7005,
        'conector_estado' => 'fallo',
    ]);

    $bitacora = Bitacora::where('sistema', 'conector')
        ->where('operacion', 'ejecutar_accion')
        ->where('estado', 'fallo')
        ->latest('id')
        ->first();

    expect($bitacora)->not->toBeNull();
});

// 6. Si la IA devuelve algo fuera de esquema o con baja confianza, la direcciÃ³n queda pendiente_revision
it('6. marca la direccion como pendiente_revision si la respuesta de IA tiene baja confianza o esquema invalido', function () {
    // Simulamos respuesta de IA con confianza baja (menor a 0.85)
    Http::fake([
        '*generativelanguage.googleapis.com*' => Http::response([
            'candidates' => [
                [
                    'content' => [
                        'parts' => [
                            [
                                'text' => json_encode([
                                    'provincia' => 'Alajuela',
                                    'canton' => 'San Carlos',
                                    'distrito' => 'Quesada',
                                    'otras_senas' => 'por donde Juan',
                                    'confianza' => 0.55, // Menor al umbral de 0.85
                                ]),
                            ],
                        ],
                    ],
                ],
            ],
        ], 200),
    ]);

    Config::set('services.gemini.key', 'gemini_test_key_abc');

    $normalizador = new NormalizadorDireccionService();
    $resultado = $normalizador->normalizar('por donde Juan', 7006);

    expect($resultado['estado'])->toBe('pendiente_revision');
    expect($resultado['normalizada'])->toBeNull();

    $this->assertDatabaseHas('bitacoras', [
        'sistema' => 'ia',
        'operacion' => 'normalizar_direccion',
    ]);
});

// 7. GET /api/v1/productos responde con la paginaciÃ³n del contrato
it('7. responde con el formato y metadatos de paginacion del contrato en GET /api/v1/productos', function () {
    // Asegurar productos de prueba
    Producto::updateOrCreate(
        ['id_tienda' => 8001],
        [
            'sku' => 'TEST-SKU-001',
            'nombre' => 'Filtro de aire',
            'tipo' => 'simple',
            'precio' => 8500,
            'existencias' => 10,
            'categorias' => ['Filtros'],
            'campo_propio_nombre' => 'numero_parte_oem',
            'campo_propio_valor' => 'OEM-998877',
            'sincronizado_en' => now(),
        ]
    );

    $response = $this->getJson('/api/v1/productos?pagina=1&por_pagina=5');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'meta' => [
                'pagina',
                'por_pagina',
                'total',
                'total_paginas',
            ],
            'enlaces' => [
                'anterior',
                'siguiente',
            ],
            'datos' => [
                '*' => [
                    'id',
                    'id_tienda',
                    'sku',
                    'nombre',
                    'tipo',
                    'precio',
                    'existencias',
                    'categorias',
                    'campo_propio' => [
                        'nombre',
                        'valor',
                    ],
                    'sincronizado_en',
                ],
            ],
        ]);
});
