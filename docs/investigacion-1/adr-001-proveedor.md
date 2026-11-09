# ADR-001 — Selección del proveedor de envíos

**Proyecto:** RDC Repuestos
**Fecha:** 9 de noviembre de 2026
**Estado:** Aprobado por el equipo
**Proveedor seleccionado:** Shippo

## 1. Contexto

RDC Repuestos necesita integrar un servicio de envíos que permita cotizar paquetes y gestionar información logística desde su tienda WooCommerce y el motor del proyecto.

Se investigaron EasyPost y Shippo mediante Postman y Newman, evaluando conectividad, funcionamiento, estabilidad y tiempos de respuesta. También se creó el pedido de prueba n.º 99 en WooCommerce, con dos productos y pago contra reembolso.

## 2. Resultados obtenidos

| Métrica | EasyPost | Shippo |
|---|---:|---:|
| Iteraciones | 10 | 10 |
| Solicitudes ejecutadas | 10 | 40 |
| Tiempo promedio | 208 ms | 411 ms |
| Tiempo mínimo | 143 ms | 97 ms |
| Tiempo máximo | 473 ms | 1524 ms |
| Fallos reportados | 0 | 0 |

Ambos proveedores completaron las ejecuciones registradas sin fallos. EasyPost presentó un menor tiempo promedio, pero las colecciones ejecutaron distintas cantidades de solicitudes. Por eso, los resultados no representan una comparación de velocidad bajo condiciones idénticas.

## 3. Decisión

El equipo seleccionó **Shippo** como proveedor principal para RDC Repuestos.

## 4. Justificación

La elección se fundamenta en las siguientes ventajas para el proyecto:

- **Integración logística:** sus funcionalidades de cotización y conexión con transportistas se ajustan al flujo de envíos que necesita RDC Repuestos.
- **Funcionamiento comprobado:** las pruebas con Postman y Newman registraron ejecuciones sin fallos.
- **Cobertura funcional:** permite trabajar con solicitudes relacionadas con direcciones, paquetes y cotizaciones.
- **Adecuación al proyecto:** el equipo priorizó la utilidad funcional de Shippo sobre la diferencia observada en tiempos de respuesta.

La decisión considera las necesidades de implementación y no únicamente la velocidad de la API.

## 5. Consecuencias y riesgos

La implementación se enfocará en Shippo, manteniendo EasyPost como alternativa evaluada.

Se deberán controlar las credenciales, gestionar errores y validar las direcciones de envío. Además, las pruebas con direcciones estadounidenses no demuestran todavía la disponibilidad de tarifas para Costa Rica.

Los costos comerciales y las condiciones de uso deberán verificarse en la documentación oficial antes de una implementación productiva.

## 6. Evidencias

- `pedido-prueba.json`
- `tiempos.md`
- `comparativa.md`
- `evidencia-EasyPost.jpeg`
- `evidencia-shippo.png`

## 7. Conclusión

**Shippo fue elegido porque sus capacidades funcionales responden mejor a los objetivos de RDC Repuestos.** Aunque EasyPost registró un menor tiempo promedio, el equipo consideró más importante contar con una integración logística adecuada, respaldada por las pruebas realizadas.