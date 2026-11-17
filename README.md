# Proyecto-VentaRepuestos

**Nombre de la tienda:** RDC Repuestos

## Integrantes

- Ronald JimÃ©nez Morales â€” GitHub: RonaldF1
- Christopher FernÃ¡ndez Zamora â€” GitHub: ChristopherFZ14
- Dylan Soto â€” GitHub: dylan2006cr

---

## InvestigaciÃ³n Aplicada 1 â€” ComparaciÃ³n de proveedores de envÃ­o

### Objetivo

Evaluar los proveedores de servicios de envÃ­o EasyPost y Shippo mediante Postman y Newman, con el propÃ³sito de seleccionar la alternativa mÃ¡s adecuada para su integraciÃ³n en RDC Repuestos.

### Pedido de prueba en WooCommerce

Se creÃ³ el pedido de prueba n.Âº 99 en WooCommerce, utilizando un cliente ficticio y dos productos del catÃ¡logo:

- Filtro de aceite para Toyota: â‚¡6.500.
- BujÃ­a de encendido para Toyota: â‚¡4.500.

**Total del pedido:** â‚¡11.000.

**MÃ©todo de pago:** Contra reembolso.

**Estado:** Pendiente de pago.

El pedido se consultÃ³ mediante la API REST de WooCommerce utilizando:

`GET /wp-json/wc/v3/orders/99`

La respuesta se guardÃ³ en `pedido-prueba.json`, ocultando los identificadores sensibles de pago antes de compartirla en el repositorio.

### Pruebas realizadas con Postman y Newman

Se utilizaron colecciones de Postman y ejecuciones de Newman para comprobar la conectividad, el funcionamiento y los tiempos de respuesta de EasyPost y Shippo.

| MÃ©trica | EasyPost | Shippo |
|---|---:|---:|
| Iteraciones | 10 | 10 |
| Solicitudes ejecutadas | 10 | 40 |
| Tiempo promedio | 208 ms | 411 ms |
| Tiempo mÃ­nimo | 143 ms | 97 ms |
| Tiempo mÃ¡ximo | 473 ms | 1524 ms |
| Fallos reportados | 0 | 0 |

Ambos proveedores finalizaron las ejecuciones registradas sin fallos.

EasyPost presentÃ³ un menor tiempo promedio; sin embargo, las pruebas utilizaron cantidades diferentes de solicitudes. Por esta razÃ³n, estos resultados no constituyen una comparaciÃ³n de rendimiento bajo condiciones idÃ©nticas.

### EjecuciÃ³n de pruebas

Para reproducir las pruebas se necesita tener instalados Node.js y Newman, disponer de las colecciones de Postman y configurar las credenciales de prueba de cada proveedor en un entorno local.

Desde la raÃ­z del proyecto, se puede ejecutar la colecciÃ³n de EasyPost mediante:

`newman run postman/investigacion-1/easypost.postman_collection.json -e postman/investigacion-1/easypost.postman_environment.json`

Para Shippo se debe utilizar su colecciÃ³n y el entorno correspondiente dentro de `postman/investigacion-1/`.

**Importante:** antes de ejecutar las pruebas, configurar las variables requeridas en los entornos locales. No publicar tokens, contraseÃ±as ni claves privadas.

### Proveedor seleccionado: Shippo

DespuÃ©s de evaluar ambas alternativas, el equipo decidiÃ³ seleccionar **Shippo** como proveedor principal para RDC Repuestos.

Esta decisiÃ³n se fundamenta en las siguientes ventajas:

- Ofrece funcionalidades de cotizaciÃ³n y conexiÃ³n con transportistas adecuadas para el proyecto.
- Permite trabajar con informaciÃ³n logÃ­stica relacionada con direcciones, paquetes y envÃ­os.
- RespondiÃ³ satisfactoriamente durante las pruebas realizadas con Postman y Newman.
- Su integraciÃ³n se ajusta al flujo de trabajo previsto para RDC Repuestos.

Aunque EasyPost obtuvo un menor tiempo promedio, el equipo priorizÃ³ la adecuaciÃ³n funcional de Shippo y las necesidades de integraciÃ³n del proyecto.

### DocumentaciÃ³n y evidencias

Los archivos correspondientes a la investigaciÃ³n se encuentran en `docs/investigacion-1/`.

- [Pedido de prueba de WooCommerce](docs/investigacion-1/pedido-prueba.json)
- [Tiempos de respuesta](docs/investigacion-1/tiempos.md)
- [ADR-001 â€” DecisiÃ³n del proveedor](docs/investigacion-1/adr-001-proveedor.md)
- [Comparativa de proveedores](docs/investigacion-1/comparativa.md)
- [Evidencia de EasyPost](docs/investigacion-1/evidencia-EasyPost.jpeg)
- [Evidencia de Shippo](docs/investigacion-1/evidencia-shippo.png)

### Consideraciones de seguridad

Las credenciales de WooCommerce, EasyPost y Shippo deben mantenerse fuera del repositorio.

No se deben publicar claves privadas, contraseÃ±as, tokens ni identificadores sensibles de pago.

Las pruebas se desarrollaron en entornos de desarrollo, utilizando datos ficticios. Los resultados no representan operaciones comerciales reales.

### Limitaciones y riesgos

La integraciÃ³n con Shippo depende de la disponibilidad del servicio, de la configuraciÃ³n correcta de las credenciales y de la validez de las direcciones utilizadas.

Las pruebas con direcciones de Estados Unidos no garantizan por sÃ­ solas la disponibilidad de tarifas para envÃ­os nacionales en Costa Rica. Este aspecto debe verificarse antes de utilizar el proveedor en producciÃ³n.

### ConclusiÃ³n

La InvestigaciÃ³n Aplicada 1 permitiÃ³ evaluar las caracterÃ­sticas funcionales y el comportamiento de EasyPost y Shippo.

El equipo seleccionÃ³ **Shippo** porque sus funcionalidades se consideran mÃ¡s adecuadas para los objetivos de RDC Repuestos, priorizando la utilidad de la integraciÃ³n sobre la diferencia observada en los tiempos de respuesta.

## Avance 2 — El motor de integración en Laravel

### Descripción del sistema

El motor de integración es una API desarrollada en **Laravel 13 con PHP 8.5** que desacopla la tienda WooCommerce de los sistemas logísticos y de normalización de direcciones con Inteligencia Artificial. Cumple estrictamente con el contrato OpenAPI v1 del curso (`motor/openapi.yaml`).

---

### Puesta en marcha rápida (Reproducibilidad)

El motor puede levantarse de forma totalmente autónoma ejecutando desde la raíz del proyecto:

```bash
cd motor
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

El servidor quedará escuchando en `http://127.0.0.1:8000`.

---

### Sincronización de productos desde WooCommerce

Se implementó el comando de consola Artisan para sincronizar el catálogo de la tienda hacia la base de datos local del motor, respetando la paginación de WooCommerce (`X-WP-TotalPages`), reintentos con backoff exponencial y registro en la bitácora:

```bash
php artisan tienda:sincronizar-productos
```

- **Mapeo:** Guarda productos y replica el campo personalizado de repuestos `numero_parte_oem`.
- **Resiliencia:** Configurado con timeout de 10 segundos y hasta 3 reintentos ante fallos de conexión, respuestas `5xx` o `429` (sin reintentar en `4xx`).
- **Trazabilidad:** Cada invocación registra su resultado en la tabla de bitácora (`saliente`, `tienda`, `listar_productos`).

---

### Arquitectura de integración y componentes

#### 1. Conector logístico desacoplado (Inversión de dependencias)
- **Interfaz obligatoria:** `App\Conectores\ConectorInterface` (`motor/app/Conectores/ConectorInterface.php`).
- **Implementación seleccionada:** `App\Conectores\ShippoConector` para cotización de envíos con Shippo.
- **Service Container:** Registrado en `AppServiceProvider` utilizando la variable de entorno `CONECTOR_PROVEEDOR` (sin acoplar nombres de proveedores en controladores ni jobs).

#### 2. Normalización de direcciones con IA
- **Servicio:** `App\Services\NormalizadorDireccionService`.
- **Modelos soportados:** Google Gemini (`gemini-1.5-flash`) y OpenAI (`gpt-4o-mini`), además de un respaldo determinista para pruebas locales.
- **Validación de esquema:** Valida la estructura devuelta contra `direccion-normalizada.schema.json` (Costa Rica: provincia, cantón, distrito, otras señas, confianza).
- **Control de confianza:** Si la confianza es menor a 0.85 o la respuesta no cumple el esquema, la dirección se marca como `pendiente_revision` y el pedido no se envía al conector automáticamente.

#### 3. Webhooks de WooCommerce
- **Ruta:** `POST /api/v1/webhooks/woocommerce`.
- **Seguridad:** Validación de firma HMAC-SHA256 (`X-WC-Webhook-Signature`).
- **Idempotencia:** Verificación contra entregas previas mediante `X-WC-Webhook-Delivery-ID` en la tabla `webhook_entregas`.
- **Asincronía:** El webhook persiste el pedido inmediatamente con código 202 y delega la normalización y el conector a la cola mediante `ProcesarPedidoWebhookJob`.

---

### Pruebas automatizadas con Pest PHP

Se desarrollaron dos suites de pruebas con Pest que se ejecutan de manera aislada sin realizar llamadas a servicios externos reales (`Http::fake()` y `Http::preventStrayRequests()`):

```bash
# Ejecutar todas las pruebas de integración
php artisan test tests/Feature/MotorIntegracionTest.php

# Ejecutar las pruebas de contrato y validaciones
php artisan test tests/Feature/ProductosContratoTest.php

# Ejecutar la suite completa
php artisan test
```

#### Pruebas obligatorias implementadas (`MotorIntegracionTest.php`):
1. **Webhook con firma válida:** Guarda el pedido y la entrega en base de datos.
2. **Webhook con firma inválida:** Responde HTTP 401 (`no_autenticado`) y no guarda datos.
3. **Idempotencia:** Un `Delivery-ID` duplicado responde 200 con estado `duplicado`.
4. **Resiliencia de tienda:** Ante respuestas 500 de WooCommerce, el motor reintenta 3 veces y anota el fallo en la bitácora.
5. **Aislamiento del conector:** Si Shippo falla, el pedido persiste en base de datos y se registra el fallo en la bitácora.
6. **Validación de IA:** Si la confianza es menor a 0.85 o el esquema es inválido, la dirección queda en `pendiente_revision` sin normalizar.
7. **Paginación del contrato:** `GET /api/v1/productos` cumple con los metadatos de paginación (`meta` y `enlaces`).

---

### Validación del Contrato OpenAPI con Newman

El motor pasa al **100% (64/64 aserciones, 12 peticiones)** la colección oficial del docente:

```bash
# Con el servidor Laravel encendido en http://127.0.0.1:8000:
newman run postman/motor-contrato.postman_collection.json -e postman/motor-local.postman_environment.json
```

---

### Declaración de Seguridad y Uso de Inteligencia Artificial

- **Seguridad:** No se almacenan secretos, contraseñas ni tokens en el repositorio Git. El archivo `.env.example` contiene las plantillas con valores vacíos.
- **Declaración de IA:** Para el desarrollo de este avance se utilizó asistencia de modelos de lenguaje para el diseño de pruebas automatizadas en Pest, la redacción del comando de sincronización de WooCommerce con reintentos y la validación del esquema JSON territorial costarricense.