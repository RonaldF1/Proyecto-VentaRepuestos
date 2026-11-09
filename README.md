# Proyecto-VentaRepuestos

**Nombre de la tienda:** RDC Repuestos

## Integrantes

- Ronald Jiménez Morales — GitHub: RonaldF1
- Christopher Fernández Zamora — GitHub: ChristopherFZ14
- Dylan Soto — GitHub: dylan2006cr

---

## Investigación Aplicada 1 — Comparación de proveedores de envío

### Objetivo

Evaluar los proveedores de servicios de envío EasyPost y Shippo mediante Postman y Newman, con el propósito de seleccionar la alternativa más adecuada para su integración en RDC Repuestos.

### Pedido de prueba en WooCommerce

Se creó el pedido de prueba n.º 99 en WooCommerce, utilizando un cliente ficticio y dos productos del catálogo:

- Filtro de aceite para Toyota: ₡6.500.
- Bujía de encendido para Toyota: ₡4.500.

**Total del pedido:** ₡11.000.

**Método de pago:** Contra reembolso.

**Estado:** Pendiente de pago.

El pedido se consultó mediante la API REST de WooCommerce utilizando:

`GET /wp-json/wc/v3/orders/99`

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

Ambos proveedores finalizaron las ejecuciones registradas sin fallos.

EasyPost presentó un menor tiempo promedio; sin embargo, las pruebas utilizaron cantidades diferentes de solicitudes. Por esta razón, estos resultados no constituyen una comparación de rendimiento bajo condiciones idénticas.

### Ejecución de pruebas

Para reproducir las pruebas se necesita tener instalados Node.js y Newman, disponer de las colecciones de Postman y configurar las credenciales de prueba de cada proveedor en un entorno local.

Desde la raíz del proyecto, se puede ejecutar la colección de EasyPost mediante:

`newman run postman/investigacion-1/easypost.postman_collection.json -e postman/investigacion-1/easypost.postman_environment.json`

Para Shippo se debe utilizar su colección y el entorno correspondiente dentro de `postman/investigacion-1/`.

**Importante:** antes de ejecutar las pruebas, configurar las variables requeridas en los entornos locales. No publicar tokens, contraseñas ni claves privadas.

### Proveedor seleccionado: Shippo

Después de evaluar ambas alternativas, el equipo decidió seleccionar **Shippo** como proveedor principal para RDC Repuestos.

Esta decisión se fundamenta en las siguientes ventajas:

- Ofrece funcionalidades de cotización y conexión con transportistas adecuadas para el proyecto.
- Permite trabajar con información logística relacionada con direcciones, paquetes y envíos.
- Respondió satisfactoriamente durante las pruebas realizadas con Postman y Newman.
- Su integración se ajusta al flujo de trabajo previsto para RDC Repuestos.

Aunque EasyPost obtuvo un menor tiempo promedio, el equipo priorizó la adecuación funcional de Shippo y las necesidades de integración del proyecto.

### Documentación y evidencias

Los archivos correspondientes a la investigación se encuentran en `docs/investigacion-1/`.

- [Pedido de prueba de WooCommerce](docs/investigacion-1/pedido-prueba.json)
- [Tiempos de respuesta](docs/investigacion-1/tiempos.md)
- [ADR-001 — Decisión del proveedor](docs/investigacion-1/adr-001-proveedor.md)
- [Comparativa de proveedores](docs/investigacion-1/comparativa.md)
- [Evidencia de EasyPost](docs/investigacion-1/evidencia-EasyPost.jpeg)
- [Evidencia de Shippo](docs/investigacion-1/evidencia-shippo.png)

### Consideraciones de seguridad

Las credenciales de WooCommerce, EasyPost y Shippo deben mantenerse fuera del repositorio.

No se deben publicar claves privadas, contraseñas, tokens ni identificadores sensibles de pago.

Las pruebas se desarrollaron en entornos de desarrollo, utilizando datos ficticios. Los resultados no representan operaciones comerciales reales.

### Limitaciones y riesgos

La integración con Shippo depende de la disponibilidad del servicio, de la configuración correcta de las credenciales y de la validez de las direcciones utilizadas.

Las pruebas con direcciones de Estados Unidos no garantizan por sí solas la disponibilidad de tarifas para envíos nacionales en Costa Rica. Este aspecto debe verificarse antes de utilizar el proveedor en producción.

### Conclusión

La Investigación Aplicada 1 permitió evaluar las características funcionales y el comportamiento de EasyPost y Shippo.

El equipo seleccionó **Shippo** porque sus funcionalidades se consideran más adecuadas para los objetivos de RDC Repuestos, priorizando la utilidad de la integración sobre la diferencia observada en los tiempos de respuesta.