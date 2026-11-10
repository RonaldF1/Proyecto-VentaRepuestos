# Comparativa de proveedores de envío

| | |
|---|---|
| **Proyecto** | RDC Repuestos |
| **Proveedores** | EasyPost y Shippo |
| **Acción evaluada** | Cotizar el envío de un pedido |
| **Consulta de documentación oficial** | 9 de noviembre de 2026 |

En cada criterio se resumen los datos disponibles y se enlaza su fuente. Cuando un valor no está publicado o no se puede comprobar en el repositorio, se indica explícitamente.

---

## Resumen

| Criterio | EasyPost | Shippo |
|---|---|---|
| **1. Tipo de autenticación**

 | API key como usuario de HTTP Basic, sin contraseña. [Fuente](https://docs.easypost.com/docs/authentication) 

| Token en el encabezado `Authorization: ShippoToken …`. [Fuente](https://docs.goshippo.com/guides/api-quickstart) |

| **2. Costo en producción por cotización** 
| No publica un precio unitario por cotización; cobra por etiqueta comprada. Confirmar el costo de la cuenta. [Fuente](https://www.easypost.com/shipping-api/) 

| **US$ 0,01** por generación de tarifa (plan API Starter). [Fuente](https://webflow.goshippo.com/pricing/api) |

| **3. Pruebas y límites de tasa** 

| Claves de prueba sin costo. Límite documentado: **5 solicitudes por segundo** en endpoints de listado. [Fuente](https://docs.easypost.com/markdown/guides/rate-limiting-guide.md) 

| Modo de prueba sin cargos. Límites en pruebas: **50 por minuto** al crear un shipment, **400** al consultar uno y **10** al listar. [Fuente](https://docs.goshippo.com/docs/api_concepts/ratelimits) |

| **4. Calidad de la documentación (0–5)** | 
**3 / 5** · [detalle](#calidad-de-la-documentación) 

| **3 / 5** · [detalle](#calidad-de-la-documentación) |

| **5. Tiempo de respuesta de la acción** 

| **Pendiente:** falta medir la carpeta `accion`. [Detalle](./tiempos.md) 

| Promedio **764 ms** · mínimo **655 ms** · máximo **958 ms** (10 ejecuciones de `accion`). [Detalle](./tiempos.md) |


| **6. Respuesta ante errores** | Documentados `4xx`/`5xx` con JSON (`code`, `message`, `errors`). Sin captura de un error real. [Fuente](https://docs.easypost.com/docs/errors)

 | **Observado:** `401` con `{"detail":"Token does not exist"}` y `400` con errores por campo. [Detalle](#6-respuesta-ante-errores) |

---

## Detalle por criterio

*Todas las fuentes fueron consultadas el 09/11/2026.*

### 1. Tipo de autenticación

- **EasyPost:** API key como usuario de HTTP Basic Authentication, sin contraseña. [Autenticación oficial](https://docs.easypost.com/docs/authentication)
- **Shippo:** token en el encabezado `Authorization: ShippoToken …`. [API quickstart](https://docs.goshippo.com/guides/api-quickstart)

### 2. Costo en producción por cotización

- **EasyPost:** la información comercial consultada indica cobro por etiqueta comprada, pero **no publica un precio unitario por generar una cotización**. Hay que confirmar el costo aplicable a la cuenta. [Información de precios](https://www.easypost.com/shipping-api/)
- **Shippo:** **US$ 0,01 por generación de tarifa** en el plan API Starter. Otros servicios o cargos pueden cobrarse aparte. [Precios de Shippo API](https://webflow.goshippo.com/pricing/api)

### 3. Pruebas y límites de tasa

- **EasyPost:** las claves de prueba permiten probar sin costo ([Autenticación y claves de prueba](https://docs.easypost.com/docs/authentication)). La guía de límites documenta 5 solicitudes por segundo en los endpoints de listado, con respuesta `429` al excederlas ([Guía de límites](https://docs.easypost.com/markdown/guides/rate-limiting-guide.md)).
- **Shippo:** el modo de prueba permite probar las funciones sin cargos ([Modo de prueba](https://docs.goshippo.com/guides/testing)). Los límites son por minuto, con `429` al excederlos ([Rate limits](https://docs.goshippo.com/docs/api_concepts/ratelimits)):

| Operación sobre un shipment | Producción | Pruebas |
|---|---|---|
| Crear (`POST`) | 500 / min | 50 / min |
| Consultar uno (`GET` simple) | 4 000 / min | 400 / min |
| Listar (`GET` múltiple) | 50 / min | 10 / min |

### Calidad de la documentación

Un punto por cada elemento que el proveedor ofrece.

| Elemento | EasyPost | Shippo |
|---|---|---|
| Referencia de los endpoints del spike | ✓ | ✓ |
| Ejemplos de respuestas de error | ✓ | — *(no verificado)* |
| Especificación OpenAPI | — *(no verificado)* | ✓ |
| Ambiente de pruebas documentado | ✓ | ✓ |
| Registro de cambios o política de versiones | — *(no verificado)* | — *(no verificado)* |
| **Puntaje** | **3 / 5** | **3 / 5** |

Fuentes EasyPost: [Envíos](https://docs.easypost.com/docs/shipments) · [Errores](https://docs.easypost.com/docs/errors) · [Pruebas](https://docs.easypost.com/docs/authentication)
Fuentes Shippo: [Crear envío y OpenAPI](https://docs.goshippo.com/api-reference/shipments/create-a-new-shipment) · [Pruebas](https://docs.goshippo.com/guides/testing)

### 5. Tiempo de respuesta de la acción

Medición con Newman sobre la carpeta `accion`: 10 ejecuciones y 1 segundo de pausa (`--folder accion -n 10 --delay-request 1000`).

| Proveedor | Promedio | Mínimo | Máximo | Desv. estándar | Estado |
|---|---|---|---|---|---|
| EasyPost | — | — | — | — | **Pendiente:** falta medir `accion` |
| Shippo | **764 ms** | **655 ms** | **958 ms** | 85 ms | Medido: 10 ejecuciones, 0 fallos |

El resumen completo de Newman y el tiempo de cada ejecución están en [tiempos.md](./tiempos.md). La medición de Shippo reemplaza la anterior de la colección completa (411 ms), que incluía peticiones de `conexion` y `errores` y no corresponde a este criterio.

### 6. Respuesta ante errores

- **EasyPost:** la documentación describe errores `4xx`/`5xx` con un JSON que puede incluir `code`, `message` y `errors`. No hay captura de un error real de EasyPost en el spike. [Formato de errores](https://docs.easypost.com/docs/errors)
- **Shippo (observado en el spike, 10 de 10 ejecuciones):**

| Caso | Código | Cuerpo de la respuesta |
|---|---|---|
| Credenciales inválidas | `401 Unauthorized` | `{"detail":"Token does not exist"}` |
| Datos inválidos (destino sin país) | `400 Bad Request` | `{"address_to":[{"country":["In order to create an Address, at least a country must be provided."]}]}` |

  Shippo usa dos formatos de error: un mensaje general (`detail`) y un objeto de errores por campo. Peticiones: [Credenciales inválidas](../../postman/collections/Shippo%20-%20envios%20y%20rastreo%20%28Investigacion%201%29/errores/Credenciales%20invalidas.request.yaml) · [Datos inválidos](../../postman/collections/Shippo%20-%20envios%20y%20rastreo%20%28Investigacion%201%29/errores/Datos%20invalidos%20%28direccion%20de%20destino%20vacia%29.request.yaml)

---

## Conclusión

Para RDC Repuestos, **Shippo parece la opción más conveniente para continuar la integración**: publica una tarifa de US$ 0,01 por cotización y ya existe una petición preparada para cotizar con ese proveedor. EasyPost sigue siendo una alternativa viable, pero no se identificó un costo unitario público por cotización en las fuentes consultadas.

La recomendación es provisional. Antes de cerrarla falta:

1. medir `accion` en EasyPost bajo las mismas condiciones que Shippo (ya medida: 764 ms de promedio);
2. confirmar la disponibilidad de tarifas para Costa Rica antes de producción.
