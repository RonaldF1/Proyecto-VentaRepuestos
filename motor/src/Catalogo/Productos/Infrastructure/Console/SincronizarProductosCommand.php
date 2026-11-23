<?php

declare(strict_types=1);

namespace Src\Catalogo\Productos\Infrastructure\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Src\Catalogo\Productos\Infrastructure\Persistence\Models\ProductoModel;
use Src\Integracion\Bitacora\Infrastructure\Persistence\Models\BitacoraModel;
use Throwable;

class SincronizarProductosCommand extends Command
{
    protected $signature = 'tienda:sincronizar-productos {--pagina= : Página específica a sincronizar}';
    protected $description = 'Sincroniza el catálogo de productos desde la API REST de WooCommerce';

    public function handle(): int
    {
        $baseUrl = rtrim((string) (config('services.woocommerce.url') ?: env('WOOCOMMERCE_URL', '')), '/');
        $consumerKey = (string) (config('services.woocommerce.key') ?: env('WOOCOMMERCE_KEY', ''));
        $consumerSecret = (string) (config('services.woocommerce.secret') ?: env('WOOCOMMERCE_SECRET', ''));

        if (empty($baseUrl)) {
            $this->error('Error: WOOCOMMERCE_URL no está configurada en el entorno.');
            return self::FAILURE;
        }

        $endpoint = "{$baseUrl}/wp-json/wc/v3/products";
        $paginaActual = (int) ($this->option('pagina') ?: 1);
        $totalPaginas = $paginaActual;
        $totalSincronizados = 0;

        $this->info("Iniciando sincronización de productos desde: {$endpoint}");

        do {
            $this->line("Consultando página {$paginaActual}...");
            $inicio = microtime(true);
            $intentos = 0;
            $maxIntentos = 3;
            $backoffMs = [200, 500, 1000];
            $response = null;
            $ultimoError = null;

            while ($intentos < $maxIntentos) {
                $intentos++;
                try {
                    $req = Http::timeout(10);
                    if (!empty($consumerKey) && !empty($consumerSecret)) {
                        $req = $req->withBasicAuth($consumerKey, $consumerSecret);
                    }

                    $response = $req->get($endpoint, [
                        'page' => $paginaActual,
                        'per_page' => 50,
                    ]);

                    $status = $response->status();

                    if ($response->successful()) {
                        break;
                    }

                    if ($status >= 400 && $status < 500 && $status !== 429) {
                        $ultimoError = "HTTP {$status}: " . mb_substr($response->body(), 0, 200);
                        break;
                    }

                    $ultimoError = "HTTP {$status} en intento {$intentos}";
                } catch (Throwable $e) {
                    $ultimoError = "Error de conexión ({$e->getMessage()}) en intento {$intentos}";
                }

                if ($intentos < $maxIntentos) {
                    usleep($backoffMs[$intentos - 1] * 1000);
                }
            }

            $duracionMs = (int) round((microtime(true) - $inicio) * 1000);
            $statusCode = $response ? $response->status() : null;

            if (!$response || !$response->successful()) {
                BitacoraModel::create([
                    'fecha' => now(),
                    'direccion' => 'saliente',
                    'sistema' => 'tienda',
                    'operacion' => 'listar_productos',
                    'estado' => 'fallo',
                    'codigo_http' => $statusCode,
                    'intentos' => $intentos,
                    'duracion_ms' => $duracionMs,
                    'correlacion' => 'pagina-' . $paginaActual,
                    'error' => mb_substr($ultimoError ?: 'Fallo al consultar WooCommerce', 0, 250),
                ]);

                $this->error("Fallo al obtener la página {$paginaActual}: {$ultimoError}");
                return self::FAILURE;
            }

            BitacoraModel::create([
                'fecha' => now(),
                'direccion' => 'saliente',
                'sistema' => 'tienda',
                'operacion' => 'listar_productos',
                'estado' => 'exito',
                'codigo_http' => $statusCode,
                'intentos' => $intentos,
                'duracion_ms' => $duracionMs,
                'correlacion' => 'pagina-' . $paginaActual,
                'error' => null,
            ]);

            $totalPaginasHeader = $response->header('X-WP-TotalPages');
            if ($totalPaginasHeader !== null && is_numeric($totalPaginasHeader)) {
                $totalPaginas = (int) $totalPaginasHeader;
            }

            $productos = $response->json();
            if (!is_array($productos)) {
                $this->warn("La respuesta de la página {$paginaActual} no es un arreglo de productos.");
                break;
            }

            foreach ($productos as $item) {
                if (!is_array($item) || empty($item['id'])) {
                    continue;
                }

                $oem = $item['numero_parte_oem'] ?? null;
                if (!$oem && !empty($item['meta_data']) && is_array($item['meta_data'])) {
                    foreach ($item['meta_data'] as $meta) {
                        if (($meta['key'] ?? '') === '_numero_parte_oem' || ($meta['key'] ?? '') === 'numero_parte_oem') {
                            $oem = $meta['value'] ?? null;
                            break;
                        }
                    }
                }

                $categorias = [];
                if (!empty($item['categories']) && is_array($item['categories'])) {
                    foreach ($item['categories'] as $cat) {
                        if (!empty($cat['name'])) {
                            $categorias[] = (string) $cat['name'];
                        }
                    }
                }

                $sku = !empty($item['sku']) ? (string) $item['sku'] : ('WC-' . $item['id']);
                $precio = isset($item['price']) && is_numeric($item['price'])
                    ? (int) round((float) $item['price'])
                    : null;
                $existencias = isset($item['stock_quantity']) && is_numeric($item['stock_quantity'])
                    ? (int) $item['stock_quantity']
                    : null;

                ProductoModel::updateOrCreate(
                    ['id_tienda' => (int) $item['id']],
                    [
                        'sku' => $sku,
                        'nombre' => (string) ($item['name'] ?? 'Producto WooCommerce'),
                        'tipo' => (string) ($item['type'] ?? 'simple'),
                        'precio' => $precio,
                        'existencias' => $existencias,
                        'categorias' => $categorias,
                        'campo_propio_nombre' => 'numero_parte_oem',
                        'campo_propio_valor' => $oem !== null ? (string) $oem : null,
                        'sincronizado_en' => now(),
                    ]
                );

                $totalSincronizados++;
            }

            $paginaActual++;
        } while ($paginaActual <= $totalPaginas && !$this->option('pagina'));

        $this->info("Sincronización completada exitosamente. Total de productos procesados: {$totalSincronizados}");
        return self::SUCCESS;
    }
}