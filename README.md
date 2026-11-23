# Proyecto-VentaRepuestos

**Nombre de la tienda:** RDC Repuestos

## Integrantes

- Ronald Jiménez Morales — GitHub: [RonaldF1](https://github.com/RonaldF1)
- Christopher Fernández Zamora — GitHub: [ChristopherFZ14](https://github.com/ChristopherFZ14)
- Dylan Andrés Soto Jiménez — GitHub: [dylan2006cr](https://github.com/dylan2006cr)

---

## Investigación Aplicada 1 — Comparación de proveedores de envío

### Objetivo

Evaluar los proveedores de servicios de envío **EasyPost** y **Shippo** mediante Postman y Newman, con el propósito de seleccionar la alternativa más adecuada para su integración en RDC Repuestos.

### Pedido de prueba en WooCommerce

Se creó el pedido de prueba n.º 99 en WooCommerce, utilizando un cliente ficticio y dos productos del catálogo:

- Filtro de aceite para Toyota: ₡6.500.
- Bujía de encendido para Toyota: ₡4.500.

**Total del pedido:** ₡11.000.  
**Método de pago:** Contra reembolso.  
**Estado:** Pendiente de pago.

El pedido se consultó mediante la API REST de WooCommerce utilizando:

```http
GET /wp-json/wc/v3/orders/99
```

La respuesta se guardó en `pedido-prueba.json`, ocultando los identificadores sensibles de pago antes de compartirla en el repositorio.

### Pruebas realizadas con Postman y Newman

Se utilizaron colecciones de Postman y ejecuciones de Newman para comprobar la conectividad, el funcionamiento y los tiempos de respuesta de EasyPost y Shippo.

| Métrica | EasyPost | Shippo |
|---|---:|---:|
| Iteraciones | 10 | 10 |
| Solicitudes ejecutadas | 10 | 40 |
| Tiempo promedio | 208 ms | 411 ms |
| Tiempo mínimo | 143 ms | 97 ms |
| Tiempo máximo | 473 ms | 1524 ms |
| Fallos reportados | 0 | 0 |

Ambos proveedores finalizaron las ejecuciones registradas sin fallos. EasyPost presentó un menor tiempo promedio; sin embargo, las pruebas utilizaron cantidades diferentes de solicitudes, por lo que estos resultados no constituyen una comparación de rendimiento bajo condiciones idénticas.

### Ejecución de pruebas

Para reproducir las pruebas se necesita tener instalados **Node.js** y **Newman**, disponer de las colecciones de Postman y configurar las credenciales de prueba de cada proveedor en un entorno local.

Desde la raíz del proyecto, se puede ejecutar la colección de EasyPost mediante:

```bash
newman run postman/investigacion-1/easypost.postman_collection.json -e postman/investigacion-1/easypost.postman_environment.json
```

Para Shippo se debe utilizar su colección y el entorno correspondiente dentro de `postman/investigacion-1/`:

```bash
newman run postman/investigacion-1/shippo.postman_collection.json -e postman/investigacion-1/shippo.postman_environment.json
```

> **Importante:** Antes de ejecutar las pruebas, configure las variables requeridas en los entornos locales. No publique tokens, contraseñas ni claves privadas.

### Proveedor seleccionado: Shippo

Después de evaluar ambas alternativas, el equipo decidió seleccionar **Shippo** como proveedor principal para RDC Repuestos debido a las siguientes ventajas:

- Ofrece funcionalidades de cotización y conexión con transportistas adecuadas para el proyecto.
- Permite trabajar con información logística relacionada con direcciones, paquetes y envíos.
- Respondió satisfactoriamente durante las pruebas realizadas con Postman y Newman.
- Su integración se ajusta al flujo de trabajo previsto para RDC Repuestos.

Aunque EasyPost obtuvo un menor tiempo promedio, el equipo priorizó la adecuación funcional de Shippo y las necesidades de integración del proyecto.

### Documentación y evidencias

Los archivos correspondientes a la investigación se encuentran en `docs/investigacion-1/`:

- [Pedido de prueba de WooCommerce](docs/investigacion-1/pedido-prueba.json)
- [Tiempos de respuesta](docs/investigacion-1/tiempos.md)
- [ADR-001 — Decisión del proveedor](docs/investigacion-1/adr-001-proveedor.md)
- [Comparativa de proveedores](docs/investigacion-1/comparativa.md)
- [Evidencia de EasyPost](docs/investigacion-1/evidencia-EasyPost.jpeg)
- [Evidencia de Shippo](docs/investigacion-1/evidencia-shippo.png)

### Consideraciones de seguridad

- Las credenciales de WooCommerce, EasyPost y Shippo deben mantenerse fuera del repositorio.
- No se deben publicar claves privadas, contraseñas, tokens ni identificadores sensibles de pago.
- Las pruebas se desarrollaron en entornos de desarrollo utilizando datos ficticios; los resultados no representan operaciones comerciales reales.

### Limitaciones y riesgos

- La integración con Shippo depende de la disponibilidad del servicio, la configuración correcta de las credenciales y la validez de las direcciones utilizadas.
- Las pruebas con direcciones de Estados Unidos no garantizan por sí solas la disponibilidad de tarifas para envíos nacionales en Costa Rica, aspecto que debe verificarse antes de pasar a producción.

### Conclusión

La Investigación Aplicada 1 permitió evaluar las características funcionales y el comportamiento de EasyPost y Shippo. El equipo seleccionó **Shippo** porque sus funcionalidades se consideran más adecuadas para los objetivos de RDC Repuestos, priorizando la utilidad de la integración sobre la diferencia observada en los tiempos de respuesta.

---

## Avance 2 — El motor de integración en Laravel

### Descripción del sistema

El motor de integración es una API desarrollada en **Laravel 13 con PHP 8.5** que desacopla la tienda WooCommerce de los sistemas logísticos y de normalización de direcciones con Inteligencia Artificial. Cumple estrictamente con el contrato OpenAPI v1 del curso (`motor/openapi.yaml`).

### Puesta en marcha rápida (Reproducibilidad)

El motor puede levantarse de forma totalmente autónoma ejecutando desde la raíz del proyecto:

```bash
cd motor
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

> **Nota:** En Windows (CMD/PowerShell) utilice `copy .env.example .env` en lugar de `cp`.

### Arquitectura Hexagonal y Puertos/Adaptadores (`src/`)

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
- **Pruebas de arquitectura:** Verificadas mediante la suite `tests/Architecture/LayerDependencyTest.php`.

---

## Etapa 4 — Pruebas automatizadas y demostración del sistema

**Responsable:** Dylan Andrés Soto Jiménez

### 4.1 Pruebas automatizadas con Pest

Se desarrollaron siete pruebas automatizadas utilizando **Pest** para verificar diferentes validaciones del contrato de la API REST del motor RDC Repuestos.

Las pruebas se enfocan en comprobar el comportamiento del sistema ante solicitudes inválidas, incluyendo:

- Validación de SKU en la consulta de productos.
- Validación de parámetros de paginación.
- Comprobación de identificadores de pedidos.
- Validación de filtros de consulta de la bitácora.
- Verificación de respuestas HTTP ante datos incorrectos.

Las pruebas se ejecutaron en el entorno local sin realizar solicitudes a servicios externos.

- **Archivo de pruebas:** `motor/tests/Feature/ProductosContratoTest.php`
- **Comando de ejecución:**
  ```bash
  php artisan test tests/Feature/ProductosContratoTest.php
  ```
- **Resultado obtenido:** 7 pruebas aprobadas, 20 aserciones y 0 fallos.

### 4.2 Video de las pruebas automatizadas

Se realizó una demostración en VS Code que incluye la ejecución de las pruebas con Pest, la revisión de las rutas de la API REST y la documentación del proyecto.

- **Duración:** aproximadamente 3 minutos y 45 segundos.
- **Video de pruebas Pest:** [Ver demostración](PEGAR_ENLACE_VIDEO_PEST) *(Pendiente de agregar URL)*

### 4.3 Demostración del recorrido completo — Punto 6.10

Se realizó una segunda grabación para demostrar el recorrido de un pedido de prueba desde WooCommerce hasta el motor de integración desarrollado con Laravel.

Durante la demostración se presentan las siguientes operaciones:

1. **Creación del pedido:** Se utiliza WooCommerce para crear el pedido de prueba n.º 101, correspondiente a un filtro de aceite para Toyota por un total de ₡6.500.
2. **Webhook de WooCommerce:** Se muestra el mecanismo de envío de notificaciones al motor Laravel mediante el endpoint `POST /api/v1/webhooks/woocommerce`.
3. **Consulta del pedido:** Se utiliza Postman para consultar `GET /api/v1/pedidos/101`, obteniendo una respuesta HTTP `200 OK`.
4. **Bitácora:** Se consulta `GET /api/v1/bitacora` para revisar los eventos registrados por el motor.
5. **Conector Shippo:** Se presenta el resultado registrado por el sistema para la operación logística del pedido, utilizando el proveedor seleccionado por el equipo.
6. **Tratamiento de la dirección:** Se muestra el estado de la dirección de envío, ya sea normalizada o pendiente de revisión según el resultado del procesamiento.

La demostración se realizó en un entorno de desarrollo utilizando datos de prueba.

- **Video del recorrido completo:** [Ver demostración](https://drive.google.com/file/d/12IjbnC63Nh4nxlrFKy71O1s316Y3vpCW/view?usp=sharing)

### 4.4 Evidencias y entrega

Las evidencias de esta etapa incluyen:

- Archivo de pruebas automatizadas con Pest (`ProductosContratoTest.php`).
- Resultados de las pruebas ejecutadas.
- Consultas del motor mediante Postman.
- Video de las pruebas automatizadas.
- Video del recorrido completo de integración.
- Documentación actualizada en el `README.md`.

Los cambios de las pruebas fueron subidos a la rama `dylan-desarrollo` y presentados al equipo mediante el Pull Request #52:

- **Pull Request:** [Ver PR #52](https://github.com/RonaldF1/Proyecto-VentaRepuestos/pull/52)

> **Nota:** Las pruebas automatizadas del contrato de la API y la demostración del recorrido completo constituyen evidencias diferentes de la entrega.
