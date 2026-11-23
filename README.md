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

---

## Avance 2 â€” El motor de integraciÃ³n en Laravel

### DescripciÃ³n del sistema

El motor de integraciÃ³n es una API desarrollada en **Laravel 13 con PHP 8.5** que desacopla la tienda WooCommerce de los sistemas logÃ­sticos y de normalizaciÃ³n de direcciones con Inteligencia Artificial. Cumple estrictamente con el contrato OpenAPI v1 del curso (`motor/openapi.yaml`).

---

### Puesta en marcha rÃ¡pida (Reproducibilidad)

El motor puede levantarse de forma totalmente autÃ³noma ejecutando desde la raÃ­z del proyecto:

```bash
cd motor
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve

## Etapa 4 â€” Pruebas automatizadas y demostraciÃ³n del sistema

**Responsable:** Dylan AndrÃ©s Soto JimÃ©nez

### 4.1 Pruebas automatizadas con Pest

Se desarrollaron siete pruebas automatizadas utilizando Pest para verificar diferentes validaciones del contrato de la API REST del motor RDC Repuestos, desarrollado con Laravel.

Las pruebas se enfocan en comprobar el comportamiento del sistema ante solicitudes invÃ¡lidas, incluyendo:

- ValidaciÃ³n de SKU en la consulta de productos.
- ValidaciÃ³n de parÃ¡metros de paginaciÃ³n.
- ComprobaciÃ³n de identificadores de pedidos.
- ValidaciÃ³n de filtros de consulta de la bitÃ¡cora.
- VerificaciÃ³n de respuestas HTTP ante datos incorrectos.

Las pruebas se ejecutaron en el entorno local, sin realizar solicitudes a servicios externos.

**Archivo de pruebas:** `motor/tests/Feature/ProductosContratoTest.php`

**Comando de ejecuciÃ³n:**

`php artisan test tests/Feature/ProductosContratoTest.php`

**Resultado obtenido:**
- 7 pruebas aprobadas.
- 20 aserciones.
- Sin fallos en la ejecuciÃ³n mostrada.

### 4.2 Video de las pruebas automatizadas

Se realizÃ³ una demostraciÃ³n en VS Code que incluye la ejecuciÃ³n de las pruebas con Pest, la revisiÃ³n de las rutas de la API REST y la documentaciÃ³n del proyecto.

**DuraciÃ³n:** aproximadamente 3 minutos y 45 segundos.

**Video de pruebas Pest:** [Ver demostraciÃ³n](PEGAR_ENLACE_VIDEO_PEST)

### 4.3 DemostraciÃ³n del recorrido completo â€” Punto 6.10

Se realizÃ³ una segunda grabaciÃ³n para demostrar el recorrido de un pedido de prueba desde WooCommerce hasta el motor de integraciÃ³n desarrollado con Laravel.

Durante la demostraciÃ³n se presentan las siguientes operaciones:

1. **CreaciÃ³n del pedido:** se utiliza WooCommerce para crear el pedido de prueba n.Âº 101, correspondiente a un filtro de aceite para Toyota, por un total de â‚¡6.500.
2. **Webhook de WooCommerce:** se muestra el mecanismo de envÃ­o de notificaciones al motor Laravel mediante el endpoint `POST /api/v1/webhooks/woocommerce`.
3. **Consulta del pedido:** se utiliza Postman para consultar `GET /api/v1/pedidos/101`, obteniendo una respuesta HTTP `200 OK`.
4. **BitÃ¡cora:** se consulta `GET /api/v1/bitacora` para revisar los eventos registrados por el motor.
5. **Conector Shippo:** se presenta el resultado registrado por el sistema para la operaciÃ³n logÃ­stica del pedido, utilizando el proveedor seleccionado por el equipo.
6. **Tratamiento de la direcciÃ³n:** se muestra el estado de la direcciÃ³n de envÃ­o, ya sea normalizada o pendiente de revisiÃ³n, segÃºn el resultado del procesamiento.

La demostraciÃ³n se realizÃ³ en un entorno de desarrollo, utilizando datos de prueba.

**Video del recorrido completo:** [Ver demostraciÃ³n] https://drive.google.com/file/d/12IjbnC63Nh4nxlrFKy71O1s316Y3vpCW/view?usp=sharing

### 4.4 Evidencias y entrega

Las evidencias de esta etapa incluyen:

- Archivo de pruebas automatizadas con Pest.
- Resultados de las pruebas ejecutadas.
- Consultas del motor mediante Postman.
- Video de las pruebas automatizadas.
- Video del recorrido completo de integraciÃ³n.
- DocumentaciÃ³n actualizada en el README.

Los cambios de las pruebas fueron subidos a la rama `dylan-desarrollo` y presentados al equipo mediante el Pull Request #52.

**Pull Request:** [Ver PR #52](https://github.com/RonaldF1/Proyecto-VentaRepuestos/pull/52)

**Nota:** Las pruebas automatizadas del contrato de la API y la demostraciÃ³n del recorrido completo constituyen evidencias diferentes de la entrega.



### 4.5 Arquitectura Hexagonal y Puertos/Adaptadores (`src/`)

Siguiendo las directrices arquitectónicas del curso, la lógica de negocio y de integración vive en `motor/src/` bajo el namespace `Src\`, estructurada por contextos delimitados, módulos y capas con aislamiento estricto:

```text
src/
├── Catalogo/
│   └── Productos/
│       ├── Domain/          # Entidad Producto y contratos de repositorio (PHP puro)
│       ├── Application/     # Casos de uso de catálogo y sincronización
│       └── Infrastructure/  # Controladores, rutas, modelo Eloquent y comando de sincronización
├── Pedidos/
│   └── Pedidos/
│       ├── Domain/          # Entidad Pedido, puertos ConectorLogisticoPort y NormalizadorDireccionPort
│       ├── Application/     # Casos de uso de procesamiento y cotización
│       └── Infrastructure/  # Controladores, adaptadores Shippo/Gemini, modelos Eloquent y Job asíncrono
├── Integracion/
│   └── Bitacora/
│       ├── Domain/          # Entidad y puerto de Bitácora
│       └── Infrastructure/  # Controlador, modelo Eloquent y persistencia de eventos
└── Shared/
    └── Infrastructure/      # ApiController base para respuestas uniformes JSON y paginación
```

- **Regla estricta de dependencias:** Las capas internas `Domain` y `Application` son agnósticas al framework; no dependen de `Illuminate\`, `Laravel\` ni de Eloquent.
- **Pruebas de arquitectura:** Verificada mediante la suite `tests/Architecture/LayerDependencyTest.php`.