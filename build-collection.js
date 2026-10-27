const fs = require('fs');

function parseUrl(rawUrl) {
  if (!rawUrl || rawUrl.trim() === '') return { raw: '', host: [], path: [] };
  const raw = rawUrl.trim();
  const qIdx = raw.indexOf('?');
  const baseAndPath = qIdx >= 0 ? raw.slice(0, qIdx) : raw;
  const queryStr = qIdx >= 0 ? raw.slice(qIdx + 1) : null;
  const protocolMatch = baseAndPath.match(/^(https?:\/\/)/);
  let rest = baseAndPath;
  if (protocolMatch) {
    rest = baseAndPath.slice(protocolMatch[1].length);
  }
  const segments = rest.split('/');
  const host = [segments[0]];
  const pathParts = segments.slice(1).filter(function(s) { return s !== ''; });
  const urlObj = { raw: raw };
  if (protocolMatch) urlObj.protocol = protocolMatch[1].replace('://', '');
  urlObj.host = host;
  if (pathParts.length > 0) urlObj.path = pathParts;
  if (queryStr) {
    urlObj.query = queryStr.split('&').map(function(p) {
      const eqIdx = p.indexOf('=');
      const k = eqIdx >= 0 ? p.slice(0, eqIdx) : p;
      const v = eqIdx >= 0 ? p.slice(eqIdx + 1) : '';
      return { key: decodeURIComponent(k), value: decodeURIComponent(v) };
    });
  }
  return urlObj;
}

function buildEvents(scripts) {
  if (!scripts || scripts.length === 0) return [];
  return scripts.map(function(s) {
    return {
      listen: s.type === 'beforeRequest' ? 'prerequest' : 'test',
      script: {
        type: 'text/javascript',
        exec: s.code.split('\n')
      }
    };
  });
}

var echo01 = [
  {
    name: 'Echo GET con parametros',
    request: {
      method: 'GET',
      url: parseUrl('{{echo_url}}/get?giro=cafeteria&ciudad=Quesada'),
      header: []
    },
    event: []
  },
  {
    name: 'Echo POST pedido',
    request: {
      method: 'POST',
      url: parseUrl('{{echo_url}}/post'),
      header: [],
      body: {
        mode: 'raw',
        raw: '{\r\n    "pedido": 1001,\r\n    "productos": [\r\n    { "sku": "CAF-TAR-250", "cantidad": 2 }\r\n    ],\r\n    "total": 10400\r\n    }\r\n',
        options: { raw: { language: 'json' } }
      }
    },
    event: []
  },
  {
    name: 'Echo estado 404',
    request: {
      method: 'GET',
      url: parseUrl('{{echo_url}}/status/201'),
      header: []
    },
    event: []
  },
  {
    name: 'Echo demora 2 s',
    request: {
      method: 'GET',
      url: parseUrl('{{echo_url}}/delay/2'),
      header: []
    },
    event: []
  },
  {
    name: 'Direccion A-1',
    request: {
      method: 'GET',
      url: parseUrl(''),
      header: [],
      body: {
        mode: 'raw',
        raw: '{\r\n    "provincia": "Alajuela",\r\n    "canton": "San Carlos",\r\n    "distrito": "Quesada",\r\n    "otras_senas": "100 metros norte de la iglesia, casa verde",\r\n    "telefono": "8888-8888",\r\n    "confianza": 0.93\r\n    }\r\n',
        options: { raw: { language: 'json' } }
      }
    },
    event: buildEvents([{
      type: 'afterResponse',
      code: "const esquema = JSON.parse(pm.collectionVariables.get('esquema_direccion'));\r\n      const salida = pm.response.json().json;\r\n      pm.test('El esquema rechaza esta salida', function () {\r\n       "
    }])
  },
  {
    name: 'Direccion A-2',
    request: {
      method: 'GET',
      url: parseUrl(''),
      header: [],
      body: {
        mode: 'raw',
        raw: '{\r\n    "provincia": "San Carlos",\r\n    "canton": "San Carlos",\r\n    "distrito": "Quesada",\r\n    "otras_senas": "100 metros norte de la iglesia, casa verde",\r\n    "confianza": 0.93\r\n    }',
        options: { raw: { language: 'json' } }
      }
    },
    event: buildEvents([{
      type: 'afterResponse',
      code: "const esquema = JSON.parse(pm.collectionVariables.get('esquema_direccion'));\r\n      const salida = pm.response.json().json;\r\n      pm.test('El esquema rechaza esta salida', function () {\r\n       "
    }])
  },
  {
    name: 'Direccion D',
    request: {
      method: 'GET',
      url: parseUrl(''),
      header: [],
      body: {
        mode: 'raw',
        raw: '{\r\n    "provincia": "Alajuela",\r\n    "canton": "San Carlos",\r\n    "distrito": "Quesada",\r\n    "otras_senas": "100 metros norte de la iglesia, casa verde",\r\n    "confianza": 1.2\r\n    }',
        options: { raw: { language: 'json' } }
      }
    },
    event: buildEvents([{
      type: 'afterResponse',
      code: "const esquema = JSON.parse(pm.collectionVariables.get('esquema_direccion'));\r\n      const salida = pm.response.json().json;\r\n      pm.test('El esquema rechaza esta salida', function () {\r\n       \r\n"
    }])
  },
  {
    name: 'Direccion E',
    request: {
      method: 'POST',
      url: parseUrl('{{echo_url}}/post'),
      header: [],
      body: {
        mode: 'raw',
        raw: '{\r\n    "provincia": "Alajuela",\r\n    "canton": "San Carlos",\r\n    "distrito": "Quesada",\r\n    "otras_senas": "100 metros norte de la iglesia, casa verde",\r\n    "confianza": 0.93\r\n    }',
        options: { raw: { language: 'json' } }
      }
    },
    event: buildEvents([{
      type: 'afterResponse',
      code: "const esquema = JSON.parse(pm.collectionVariables.get('esquema_direccion'));\r\n      const salida = pm.response.json().json;\r\n      pm.test('La salida cumple el esquema', function () {\r\n       pm.expect(salida).to.have.jsonSchema(esquema);\r\n      });"
    }])
  }
];

var tienda02 = [
  {
    name: 'Listar productos Sin credenciales',
    request: {
      method: 'GET',
      url: parseUrl('{{tienda_url}}/wp-json/wc/v3/products?per_page=5&page=1'),
      header: [],
      auth: { type: 'noauth' }
    },
    event: buildEvents([{
      type: 'afterResponse',
      code: "pm.test('Responde 401', function () {\r\n       pm.response.to.have.status(401);\r\n      });\r\n      pm.test('El c\u00f3digo dice que no puede ver', function () {\r\n       const codigo = pm.response.json().code;\r\n       pm.expect(codigo).to.eql('woocommerce_rest_cannot_view');\r\n      });"
    }])
  },
  {
    name: 'Listar productos',
    request: {
      method: 'GET',
      url: parseUrl('{{tienda_url}}/wp-json/wc/v3/products?per_page=5&page=1'),
      header: []
    },
    event: buildEvents([{
      type: 'afterResponse',
      code: "const productos = pm.response.json();\r\n      pm.environment.set('producto_id', productos[0].id);\r\n      pm.test('Responde 200', function () {\r\n       pm.response.to.have.status(200);\r\n      });\r\n      pm.test('Trae la cabecera X-WP-TotalPages', function () {\r\n       pm.response.to.have.header('X-WP-TotalPages');\r\n      });\r\n      pm.test('Devuelve como m\u00e1ximo 5 productos', function () {\r\n       pm.expect(pm.response.json().length).to.be.at.most(5);\r\n      });\r\n      pm.test('Responde en menos de 3 s', function () {\r\n       pm.expect(pm.response.responseTime).to.be.below(3000);\r\n      });\r\n"
    }])
  },
  {
    name: 'Obtener un producto',
    request: {
      method: 'GET',
      url: parseUrl('{{tienda_url}}/wp-json/wc/v3/products/{{producto_id}}'),
      header: []
    },
    event: buildEvents([{
      type: 'afterResponse',
      code: "const esquemaProducto = {\r\n       type: 'object',\r\n       required: ['id', 'name', 'sku', 'price', 'stock_quantity'],\r\n       properties: {\r\n       id: { type: 'integer' },\r\n       name: { type: 'string' },\r\n       sku: { type: 'string' },\r\n       price: { type: 'string' },\r\n       stock_quantity: { type: ['integer', 'null'] }\r\n       }\r\n      };\r\n      pm.test('Cumple el esquema del producto', function () {\r\n       pm.response.to.have.jsonSchema(esquemaProducto);\r\n      });\r\n      pm.test('Trae el campo propio del plugin', function () {\r\n       const campo = pm.environment.get('campo_propio');\r\n       pm.expect(pm.response.json()).to.have.property(campo);\r\n      });"
    }])
  },
  {
    name: 'per_page fuera de rango',
    request: {
      method: 'GET',
      url: parseUrl('{{tienda_url}}/wp-json/wc/v3/products?per_page=101'),
      header: []
    },
    event: buildEvents([{
      type: 'afterResponse',
      code: "pm.test('Responde 400', function () {\r\n       pm.response.to.have.status(400);\r\n      });\r\n      pm.test('El c\u00f3digo es rest_invalid_param', function () {\r\n       const codigo = pm.response.json().code;\r\n       pm.expect(codigo).to.eql('rest_invalid_param');\r\n      });\r\n"
    }])
  },
  {
    name: 'Producto inexistente',
    request: {
      method: 'GET',
      url: parseUrl('{{tienda_url}}/wp-json/wc/v3/products/999999'),
      header: []
    },
    event: buildEvents([{
      type: 'afterResponse',
      code: "pm.test('Responde 404', function () {\r\n       pm.response.to.have.status(404);\r\n      });\r\n      pm.test('El c\u00f3digo dice que el id no es v\u00e1lido', function () {\r\n       const codigo = pm.response.json().code;\r\n       pm.expect(codigo).to.eql('woocommerce_rest_product_invalid_id');\r\n      });"
    }])
  }
];

var contrato03 = [
  {
    name: 'Productos pagina 2',
    request: {
      method: 'GET',
      url: parseUrl('{{base_url}}/productos?pagina=2&por_pagina=5'),
      header: []
    },
    event: buildEvents([{
      type: 'afterResponse',
      code: "pm.test('Responde 200', function () {\n pm.response.to.have.status(200);\n});\npm.test('C2 \u00b7 Respeta pagina y por_pagina', function () {\n const r = pm.response.json();\n pm.expect(r.meta.pagina).to.eql(2);\n pm.expect(r.meta.por_pagina).to.eql(5);\n pm.expect(r.datos.length).to.be.at.most(5);\n});\n"
    }])
  },
  {
    name: 'por_pagina fuera de rango',
    request: {
      method: 'GET',
      url: parseUrl('{{base_url}}/productos?por_pagina=101'),
      header: []
    },
    event: buildEvents([{
      type: 'afterResponse',
      code: "pm.test('Responde 422', function () {\n pm.response.to.have.status(422);\n});\npm.test('C3 \u00b7 error.codigo es validacion', function () {\n pm.expect(pm.response.json().error.codigo).to.eql('validacion');\n});\n"
    }])
  },
  {
    name: 'Pedido 1001',
    request: {
      method: 'GET',
      url: parseUrl('{{base_url}}/pedidos/1001'),
      header: []
    },
    event: buildEvents([{
      type: 'afterResponse',
      code: "const esquema = JSON.parse(pm.collectionVariables.get('esquema_direccion'));\r\n      const pedido = pm.response.json();\r\n      pm.test('Responde 200', function () {\r\n       pm.response.to.have.status(200);\r\n      });\r\n      pm.test('C7 \u00b7 La direcci\u00f3n normalizada cumple el esquema', function () {\r\n       pm.expect(pedido.direccion.normalizada).to.have.jsonSchema(esquema);\r\n      });\r\n      pm.test('C8 \u00b7 No trae el tel\u00e9fono del cliente', function () {\r\n       pm.expect(pedido).to.not.have.property('telefono');\r\n      });"
    }])
  },
  {
    name: 'Pedido inexistente',
    request: {
      method: 'GET',
      url: parseUrl('{{base_url}}/pedidos/999999'),
      header: []
    },
    event: buildEvents([{
      type: 'afterResponse',
      code: "pm.test('Responde 404', function () {\r\n       pm.response.to.have.status(404);\r\n      });\r\n      pm.test('C3 \u00b7 error.codigo es no_encontrado', function () {\r\n       pm.expect(pm.response.json().error.codigo).to.eql('no_encontrado');\r\n      });"
    }])
  },
  {
    name: 'Bitacora del conector',
    request: {
      method: 'GET',
      url: parseUrl('{{base_url}}/bitacora?sistema=conector'),
      header: []
    },
    event: buildEvents([{
      type: 'afterResponse',
      code: "pm.test('Responde 200', function () {\r\n       pm.response.to.have.status(200);\r\n      });\r\n      pm.test('C6 \u00b7 Solo registros del conector', function () {\r\n       pm.response.json().datos.forEach(function (x) {\r\n       pm.expect(x.sistema).to.eql('conector');\r\n       });\r\n      });\r\n"
    }])
  }
];

function makeItem(r) {
  var item = { name: r.name, request: r.request, response: [] };
  if (r.event && r.event.length > 0) item.event = r.event;
  return item;
}

var collection = {
  info: {
    _postman_id: 'a1b2c3d4-e5f6-7890-abcd-ef1234567890',
    name: 'Practica guiada Ronald',
    schema: 'https://schema.getpostman.com/json/collection/v2.1.0/collection.json'
  },
  item: [
    { name: '01 Echo', item: echo01.map(makeItem) },
    { name: '02 Tienda', item: tienda02.map(makeItem) },
    { name: '03 Contrato', item: contrato03.map(makeItem) }
  ],
  variable: [
    { key: 'echo_url', value: '', type: 'string' },
    { key: 'esquema_direccion', value: '', type: 'string' }
  ]
};

fs.writeFileSync('practica-guiada.postman_collection.json', JSON.stringify(collection, null, 2), 'utf8');
console.log('OK - File written');
console.log('Size: ' + fs.statSync('practica-guiada.postman_collection.json').size + ' bytes');
