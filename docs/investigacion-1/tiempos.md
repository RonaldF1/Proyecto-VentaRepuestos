
# Investigación Aplicada 1 — Tiempos de respuesta

## Proyecto
RDC Repuestos — Comparación EasyPost vs. Shippo

## Herramientas utilizadas
Postman y Newman.

## Resultados obtenidos

| Métrica | EasyPost | Shippo |
|---|---:|---:|
| Iteraciones       | 10     | 10      |
| Tiempo promedio   | 208 ms | 411 ms  |
| Tiempo mínimo     | 143 ms | 97 ms   |
| Tiempo máximo     | 473 ms | 1524 ms |
| Fallos reportados | 0      | 0       |

## Análisis de resultados

Las pruebas ejecutadas con Newman registraron un tiempo promedio de 208 ms para EasyPost y de 411 ms para Shippo.

Ambos proveedores completaron las ejecuciones reportadas sin fallos. Shippo presentó un tiempo máximo mayor, de 1524 ms, mientras que EasyPost registró un máximo de 473 ms.

## Limitaciones de la comparación

Las evidencias muestran que las ejecuciones no tuvieron exactamente la misma cantidad de solicitudes. Por tanto, estos resultados documentan el comportamiento observado, pero no constituyen todavía una comparación de rendimiento bajo condiciones idénticas.

## Evidencias

- evidencia-EasyPost.jpeg
- evidencia-shippo.png

Fuente: ejecuciones de Newman y registros del equipo, 9 de noviembre de 2026.
