# Correspondencia con la interfaz común: Shippo

Este documento describe **solo al proveedor elegido** y es el puente hacia el código del Avance 2 (módulo del conector en Laravel).
 

## 1. Peticiones que usa cada operación de la interfaz

| Operación de la interfaz | Método | Ruta | Para qué sirve |
|---|---|---|---|
| `probarConexion()` | `GET` | `https://api.goshippo.com/carrier_accounts/` | Consulta de solo lectura que confirma que las credenciales funcionan. `[VERIFICAR]` que responde 200 con la credencial de prueba. |
| `ejecutarAccion(Pedido $pedido)` | `POST` | `https://api.goshippo.com/shipments/` | Crea el envío con los datos del pedido y devuelve las tarifas disponibles (precio y tiempo de entrega). |

Autenticación en ambas peticiones: encabezado `Authorization: ShippoToken <token>` (`CONECTOR_TOKEN`).

## 2. Datos del pedido y campo del proveedor

El pedido sale de `GET /wp-json/wc/v3/orders/{id}` (`pedido-prueba.json`).

| Dato del pedido (WooCommerce) | Campo del proveedor | Transformación necesaria |
|---|---|---|
| `number` | `metadata` | Ninguna (texto). |
| `shipping.first_name` + `shipping.last_name` | `address_to.name` | Concatenar con un espacio. |
| `shipping.address_1`, `shipping.address_2` | `address_to.street1`, `address_to.street2` | Ninguna. |
| `shipping.city` | `address_to.city` | Ninguna. |
| `shipping.state` | `address_to.state` | Usar el código que acepta el proveedor (en EE. UU., dos letras). |
| `shipping.postcode` | `address_to.zip` | Ninguna. |
| `shipping.country` | `address_to.country` | Código ISO de dos letras (WooCommerce ya lo usa). |
| `billing.phone` | `address_to.phone` | Formato internacional (`+506########`). |
| `line_items[].quantity` × `weight` del producto | `parcels[0].weight` con `mass_unit` | El peso no viene en el pedido: se consulta en `GET /wp-json/wc/v3/products/{id}`. Sumar por cantidad; si la tienda usa kg, enviar `mass_unit: "kg"` sin conversión. |
| `dimensions` de los productos | `parcels[0].length`, `width`, `height` con `distance_unit` | Definir el paquete; si la tienda usa cm, enviar `distance_unit: "cm"` sin conversión. |
| Dirección de la tienda (no está en el pedido) | `address_from.*` | Se toma de las variables `CONECTOR_ORIGEN_*`. |

Transformaciones a programar en el Avance 2: teléfono a formato internacional (ejemplo: `8888-8888` → `+50688888888`); nombre completo como `first_name` + espacio + `last_name`; peso total como suma de `cantidad × peso del producto`; país como código ISO de dos letras.

Datos del pedido que **no** se usan en esta acción: `total`, `currency` y los precios de las líneas (la cotización no necesita valor declarado).`

## 3. Cómo se llena `ResultadoConector`

### Cuando la acción tiene éxito

| Campo | `probarConexion()` | `ejecutarAccion()` |
|---|---|---|
| `exito` | `true` | `true` |
| `estado` | `"conectado"` | `"cotizado"` |
| `referenciaExterna` | `null` | `object_id` del shipment |
| `codigoHttp` | `200` | `201` |
| `reintentable` | `false` | `false` |
| `mensaje` | `"Credenciales válidas"` | `"Tarifa cotizada"` |
| `datos` | `[]` | `{ tarifa_id, transportista, servicio, precio, moneda, dias_entrega }` de la tarifa elegida |

Selección de la tarifa: de `rates[]` se toma la de menor `amount` (texto numérico: convertir a número) que tenga `estimated_days` distinto de `null`; `amount` es el precio, `currency` la moneda y `estimated_days` el tiempo de entrega (`duration_terms` da el detalle en texto). `[VERIFICAR]`


Ejemplo de resultado exitoso de `ejecutarAccion()` (los valores son ilustrativos; reemplácenlos por los de su ejecución real):

```json
{
    "object_id": "c9020f609e7c4b388178bac778777c52",
    "object_created": "2026-11-10T00:14:41.488Z",
    "object_updated": "2026-11-10T00:14:42.593Z",
    "object_owner": "fabianmorales1204@gmail.com",
    "test": true,
    "metadata": "RDC-TEST-001",
    "messages": [
        {
            "source": "Shippo",
            "code": "",
            "text": "Carrier account shippo_chronopost_account doesn't support one or more shipment options"
        },
        {
            "source": "Shippo",
            "code": "",
            "text": "Your account is not approved to use this carrier through Shippo's shared account. Contact support for approval."
        },
        {
            "source": "Shippo",
            "code": "",
            "text": "Carrier account COURIERSPLEASE_SHIPPO_TIER_1 doesn't support one or more shipment options"
        },
        {
            "source": "Shippo",
            "code": "",
            "text": "Carrier account shippo_dpd_de_account doesn't support one or more shipment options"
        },
        {
            "source": "Shippo",
            "code": "",
            "text": "Carrier account shippo_hermes_uk_account doesn't support one or more shipment options"
        },
        {
            "source": "Shippo",
            "code": "",
            "text": "Carrier account shippo_canada_post_master doesn't support one or more shipment options"
        },
        {
            "source": "Shippo",
            "code": "",
            "text": "Your account is not approved to use this carrier through Shippo's shared account. Contact support for approval."
        },
        {
            "source": "Shippo",
            "code": "",
            "text": "Your account is not approved to use this carrier through Shippo's shared account. Contact support for approval."
        },
        {
            "source": "Shippo",
            "code": "",
            "text": "Your account is not approved to use this carrier through Shippo's shared account. Contact support for approval."
        },
        {
            "source": "Shippo",
            "code": "",
            "text": "Carrier account shippo_deutsche_post_account doesn't support one or more shipment options"
        },
        {
            "source": "Shippo",
            "code": "",
            "text": "Carrier account shippo_dpd_uk_account doesn't support one or more shipment options"
        },
        {
            "source": "Shippo",
            "code": "",
            "text": "Carrier account shippo_colissimo_account doesn't support one or more shipment options"
        },
        {
            "source": "Shippo",
            "code": "",
            "text": "Carrier account shippo_correos_account doesn't support one or more shipment options"
        }
    ],
    "extra": {},
    "order": null,
    "carrier_accounts": [],
    "address_from": {
        "name": "RDC Repuestos",
        "street1": "Calle Central, Avenida 1",
        "street2": "",
        "street3": "",
        "city": "Alajuela",
        "state": "CA",
        "zip": "94107",
        "country": "US",
        "street_no": "",
        "phone": "0050622221111",
        "email": "",
        "company": "",
        "is_residential": null,
        "test": true,
        "object_id": "c3f06a7313244d0c830e0daa56caaf97",
        "is_complete": true,
        "validation_results": {}
    },
    "address_to": {
        "name": "Cliente de Prueba",
        "street1": "Avenida 3, Calle 5",
        "street2": "",
        "street3": "",
        "city": "Heredia",
        "state": "NY",
        "zip": "10001",
        "country": "US",
        "street_no": "",
        "phone": "0050688882222",
        "email": "",
        "company": "",
        "is_residential": null,
        "test": true,
        "object_id": "3c291fd940e142528b8f8afd3571e64e",
        "is_complete": true,
        "validation_results": {}
    },
    "parcels": [
        {
            "object_owner": "fabianmorales1204@gmail.com",
            "object_state": "VALID",
            "mass_unit": "kg",
            "template": null,
            "extra": {},
            "metadata": "",
            "test": true,
            "object_id": "59c9cb241dcd48669c3751fe7d5f39f4",
            "object_created": "2026-11-10T00:14:41.488Z",
            "object_updated": "2026-11-10T00:14:41.488Z",
            "length": "20.0000",
            "width": "15.0000",
            "height": "10.0000",
            "distance_unit": "cm",
            "weight": "1.5000",
            "line_items": []
        }
    ],
    "status": "SUCCESS",
    "shipment_date": "2026-11-10T00:14:41.487Z",
    "address_return": {
        "name": "RDC Repuestos",
        "street1": "Calle Central, Avenida 1",
        "street2": "",
        "street3": "",
        "city": "Alajuela",
        "state": "CA",
        "zip": "94107",
        "country": "US",
        "street_no": "",
        "phone": "0050622221111",
        "email": "",
        "company": "",
        "is_residential": null,
        "test": true,
        "object_id": "c3f06a7313244d0c830e0daa56caaf97",
        "is_complete": true,
        "validation_results": {}
    },
    "rates": [
        {
            "object_id": "8d3207bd4e7742a795d7b3fe8d33e851",
            "object_created": "2026-11-10T00:14:42.578Z",
            "object_owner": "fabianmorales1204@gmail.com",
            "shipment": "c9020f609e7c4b388178bac778777c52",
            "amount": "13.95",
            "currency": "USD",
            "amount_local": "13.95",
            "currency_local": "USD",
            "attributes": [],
            "provider": "USPS",
            "provider_image_75": "https://shippo-static.s3.amazonaws.com/providers/75/USPS.png",
            "provider_image_200": "https://shippo-static.s3.amazonaws.com/providers/200/USPS.png",
            "arrives_by": null,
            "duration_terms": "Delivery within 1, 2, or 3 days based on where your package started and where it’s being sent.",
            "messages": [],
            "carrier_account": "5d012e1897294ccd94844d8ffd22d862",
            "zone": "8",
            "test": true,
            "servicelevel": {
                "name": "Priority Mail",
                "token": "usps_priority",
                "terms": "",
                "extended_token": "usps_priority",
                "display_name": null,
                "parent_servicelevel": null
            },
            "estimated_days": 3,
            "included_insurance_price": null
        },
        {
            "object_id": "a021b0a8ce484851a27bc41d95e35d69",
            "object_created": "2026-11-10T00:14:42.578Z",
            "object_owner": "fabianmorales1204@gmail.com",
            "shipment": "c9020f609e7c4b388178bac778777c52",
            "amount": "85.74",
            "currency": "USD",
            "amount_local": "85.74",
            "currency_local": "USD",
            "attributes": [
                "FASTEST"
            ],
            "provider": "USPS",
            "provider_image_75": "https://shippo-static.s3.amazonaws.com/providers/75/USPS.png",
            "provider_image_200": "https://shippo-static.s3.amazonaws.com/providers/200/USPS.png",
            "arrives_by": null,
            "duration_terms": "Overnight delivery to most U.S. locations.",
            "messages": [],
            "carrier_account": "5d012e1897294ccd94844d8ffd22d862",
            "zone": "8",
            "test": true,
            "servicelevel": {
                "name": "Priority Mail Express",
                "token": "usps_priority_express",
                "terms": "",
                "extended_token": "usps_priority_express",
                "display_name": null,
                "parent_servicelevel": null
            },
            "estimated_days": 1,
            "included_insurance_price": null
        },
        {
            "object_id": "ecbd63308f274b30889f082be811b098",
            "object_created": "2026-11-10T00:14:42.578Z",
            "object_owner": "fabianmorales1204@gmail.com",
            "shipment": "c9020f609e7c4b388178bac778777c52",
            "amount": "8.76",
            "currency": "USD",
            "amount_local": "8.76",
            "currency_local": "USD",
            "attributes": [
                "CHEAPEST",
                "BESTVALUE"
            ],
            "provider": "USPS",
            "provider_image_75": "https://shippo-static.s3.amazonaws.com/providers/75/USPS.png",
            "provider_image_200": "https://shippo-static.s3.amazonaws.com/providers/200/USPS.png",
            "arrives_by": null,
            "duration_terms": "Delivery in 2 to 5 days.",
            "messages": [],
            "carrier_account": "5d012e1897294ccd94844d8ffd22d862",
            "zone": "8",
            "test": true,
            "servicelevel": {
                "name": "Ground Advantage",
                "token": "usps_ground_advantage",
                "terms": "",
                "extended_token": "usps_ground_advantage",
                "display_name": null,
                "parent_servicelevel": null
            },
            "estimated_days": 5,
            "included_insurance_price": null
        }
    ],
    "alternate_address_to": null,
    "customs_declaration": null
}
```

### Cuando falla

| Campo | Valor |
|---|---|
| `exito` | `false` |
| `estado` | `"error"` |
| `referenciaExterna` | `null` |
| `codigoHttp` | el código HTTP recibido (`0` si no hubo respuesta por error de conexión) |
| `reintentable` | `true` si fue error de conexión, 5xx o 429; `false` para cualquier otro 4xx |
| `mensaje` | el mensaje del cuerpo de error. `[OBSERVAR]` el formato real en la carpeta `errores` y anotarlo aquí. |
| `datos` | `[]` o el detalle de errores por campo cuando el proveedor lo entregue |


Ejemplo de resultado fallido por límite de tasa (429):

```json
{
  "exito": false,
  "estado": "error",
  "referenciaExterna": null,
  "codigoHttp": 429,
  "reintentable": true,
  "mensaje": "<mensaje del cuerpo de la respuesta>",
  "datos": []
}
```


## 5. Variables de entorno del módulo (sin valores)

```dotenv
CONECTOR_PROVEEDOR=
CONECTOR_BASE_URL=
CONECTOR_TOKEN=
CONECTOR_TIMEOUT_SEGUNDOS=
CONECTOR_ORIGEN_NOMBRE=
CONECTOR_ORIGEN_CALLE=
CONECTOR_ORIGEN_CIUDAD=
CONECTOR_ORIGEN_ESTADO=
CONECTOR_ORIGEN_CP=
CONECTOR_ORIGEN_PAIS=
CONECTOR_ORIGEN_TELEFONO=
```
