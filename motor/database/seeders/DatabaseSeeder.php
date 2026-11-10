<?php

namespace Database\Seeders;

use App\Models\Bitacora;
use App\Models\Pedido;
use App\Models\Producto;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Limpiar datos existentes
        Bitacora::truncate();
        Pedido::truncate();
        Producto::truncate();

        // 2. Productos según contrato de prueba
        $nombres = [
            ['CONTRATO-001', 'Café Tarrazú en grano 250 g', 'simple', 5200, 12, 'Café en grano', 'tarrazu'],
            ['CAF-TAR', 'Café Tarrazú molido', 'variable', 5200, null, 'Café molido', 'tarrazu'],
            ['CAF-BRU-500', 'Café Brunca en grano 500 g', 'simple', 9800, 8, 'Café en grano', 'brunca'],
            ['CAF-ORO-250', 'Café Orosi molido 250 g', 'simple', 4900, 20, 'Café molido', 'orosi'],
            ['CAF-TUR-250', 'Café Turrialba en grano 250 g', 'simple', 5100, 0, 'Café en grano', 'turrialba'],
            ['CAF-GUA-1K', 'Café Guanacaste en grano 1 kg', 'simple', 17500, 4, 'Café en grano', 'guanacaste'],
            ['CAF-VCE-250', 'Café Valle Central molido 250 g', 'simple', 4600, 15, 'Café molido', 'valle-central'],
            ['CAF-VOC-500', 'Café Valle Occidental en grano 500 g', 'simple', 9200, 6, 'Café en grano', 'valle-occidental'],
            ['CAF-TRI-250', 'Café Tres Ríos molido 250 g', 'simple', 5600, 9, 'Café molido', 'tres-rios'],
            ['TAZ-CER-01', 'Taza de cerámica', 'simple', 6500, 20, 'Accesorios', null],
            ['PRE-FRA-01', 'Prensa francesa 600 ml', 'simple', 18900, 5, 'Accesorios', null],
            ['MOL-MAN-01', 'Molino manual de café', 'simple', 24500, 3, 'Accesorios', null],
            ['FIL-PAP-100', 'Filtros de papel, 100 unidades', 'simple', 2300, 40, 'Accesorios', null],
            ['KIT-BAR-01', 'Kit de barista', 'agrupado', 45000, null, 'Accesorios', null],
        ];

        foreach ($nombres as $i => $item) {
            [$sku, $nombre, $tipo, $precio, $existencias, $categoria, $origen] = $item;
            Producto::create([
                'id' => $i + 1,
                'id_tienda' => 40 + $i + 1,
                'sku' => $sku,
                'nombre' => $nombre,
                'tipo' => $tipo,
                'precio' => $precio,
                'existencias' => $existencias,
                'categorias' => [$categoria],
                'campo_propio_nombre' => 'origen_grano',
                'campo_propio_valor' => $origen,
                'sincronizado_en' => Carbon::parse('2026-11-03T15:20:11-06:00'),
            ]);
        }

        // 3. Pedidos según contrato
        Pedido::create([
            'id' => 1001,
            'estado' => 'processing',
            'moneda' => 'CRC',
            'total' => 16900,
            'creado_en' => Carbon::parse('2026-11-10T14:02:55-06:00'),
            'lineas' => [
                ['sku' => 'CONTRATO-001', 'nombre' => 'Café Tarrazú en grano 250 g', 'cantidad' => 2, 'precio_unitario' => 5200, 'subtotal' => 10400],
                ['sku' => 'TAZ-CER-01', 'nombre' => 'Taza de cerámica', 'cantidad' => 1, 'precio_unitario' => 6500, 'subtotal' => 6500],
            ],
            'direccion_estado' => 'normalizada',
            'direccion_texto' => '100 metros norte de la iglesia de Ciudad Quesada, casa verde',
            'direccion_normalizada' => [
                'provincia' => 'Alajuela',
                'canton' => 'San Carlos',
                'distrito' => 'Quesada',
                'otras_senas' => '100 metros norte de la iglesia, casa verde',
                'confianza' => 0.93,
            ],
            'conector_estado' => 'exito',
            'conector_detalle' => 'enviado',
            'conector_referencia_externa' => 'msg-88231',
            'conector_actualizado_en' => Carbon::parse('2026-11-10T14:02:58-06:00'),
        ]);

        Pedido::create([
            'id' => 1002,
            'estado' => 'on-hold',
            'moneda' => 'CRC',
            'total' => 9800,
            'creado_en' => Carbon::parse('2026-11-10T15:10:00-06:00'),
            'lineas' => [
                ['sku' => 'CAF-BRU-500', 'nombre' => 'Café Brunca en grano 500 g', 'cantidad' => 1, 'precio_unitario' => 9800, 'subtotal' => 9800],
            ],
            'direccion_estado' => 'pendiente_revision',
            'direccion_texto' => 'donde siempre, frente a la pulpería',
            'direccion_normalizada' => null,
            'conector_estado' => 'fallo',
            'conector_detalle' => null,
            'conector_referencia_externa' => null,
            'conector_actualizado_en' => Carbon::parse('2026-11-10T15:10:04-06:00'),
        ]);

        Pedido::create([
            'id' => 1003,
            'estado' => 'pending',
            'moneda' => 'CRC',
            'total' => 18900,
            'creado_en' => Carbon::parse('2026-11-10T16:45:12-06:00'),
            'lineas' => [
                ['sku' => 'PRE-FRA-01', 'nombre' => 'Prensa francesa 600 ml', 'cantidad' => 1, 'precio_unitario' => 18900, 'subtotal' => 18900],
            ],
            'direccion_estado' => 'sin_procesar',
            'direccion_texto' => 'Florencia de San Carlos, 200 m sur de la plaza',
            'direccion_normalizada' => null,
            'conector_estado' => 'pendiente',
            'conector_detalle' => null,
            'conector_referencia_externa' => null,
            'conector_actualizado_en' => null,
        ]);

        // 4. Bitácora según contrato
        $plantilla = [
            ['entrante', 'tienda', 'webhook_pedido_creado', 'exito', 200, 1, 15, 'contrato-entrega-001', null],
            ['saliente', 'conector', 'ejecutar_accion', 'exito', 200, 1, 412, '1001', null],
            ['saliente', 'ia', 'normalizar_direccion', 'exito', 200, 1, 1830, '1001', null],
            ['entrante', 'tienda', 'webhook_pedido_creado', 'duplicado', 200, 1, 9, 'contrato-entrega-001', null],
            ['entrante', 'tienda', 'webhook_pedido_creado', 'exito', 200, 1, 14, 'contrato-entrega-002', null],
            ['saliente', 'conector', 'ejecutar_accion', 'fallo', 503, 3, 9120, '1002', 'El proveedor respondió 503 en los tres intentos'],
            ['saliente', 'ia', 'normalizar_direccion', 'fallo', 200, 1, 2210, '1002', 'La salida no cumple el esquema: falta distrito'],
            ['entrante', 'tienda', 'webhook_pedido_creado', 'firma_invalida', 401, 1, 4, 'contrato-entrega-003', 'La firma no coincide'],
            ['saliente', 'tienda', 'listar_productos', 'exito', 200, 1, 640, null, null],
            ['saliente', 'tienda', 'listar_productos', 'fallo', null, 3, 30050, null, 'Tiempo de espera agotado'],
        ];

        for ($i = 0; $i < 24; $i++) {
            [$direccion, $sistema, $operacion, $estado, $codigo_http, $intentos, $duracion_ms, $correlacion, $error] = $plantilla[$i % count($plantilla)];
            $minutos = 59 - $i * 2;
            $h = $minutos >= 0 ? 15 : 14;
            $m = str_pad((string) (($minutos + 60) % 60), 2, '0', STR_PAD_LEFT);
            $fecha = Carbon::parse("2026-11-10T{$h}:{$m}:00-06:00");

            Bitacora::create([
                'id' => 24 - $i,
                'fecha' => $fecha,
                'direccion' => $direccion,
                'sistema' => $sistema,
                'operacion' => $operacion,
                'estado' => $estado,
                'codigo_http' => $codigo_http,
                'intentos' => $intentos,
                'duracion_ms' => $duracion_ms,
                'correlacion' => $correlacion,
                'error' => $error,
            ]);
        }
    }
}
