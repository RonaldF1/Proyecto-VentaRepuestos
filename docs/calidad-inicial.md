
# Calidad inicial de la tienda RCD Repuestos

## 1. Objetivo

Evaluar la calidad inicial de la tienda RCD Repuestos, desarrollada con WordPress y WooCommerce, mediante Google Lighthouse, para identificar oportunidades de mejora en rendimiento, accesibilidad, buenas prácticas y posicionamiento SEO.

## 2. Herramienta utilizada

- Herramienta: Google Lighthouse.
- Navegador: Microsoft Edge (Chromium).
- Modo: Navigation.
- Dispositivo: Mobile.
- Fecha de medición: 26 de octubre de 2026.
- Entorno evaluado: LocalWP.

### Páginas evaluadas

Página principal:
http://rcd-repuestos-local-dylan.local/

Página de producto:
http://rcd-repuestos-local-dylan.local/product/alternador-para-toyota/

## 3. Resultados de Lighthouse

| Categoría | Página principal | Alternador para Toyota |
|---|---:|---:|
| Rendimiento | 60 | 71 |
| Accesibilidad | 92 | 92 |
| Buenas prácticas | 78 | 74 |
| SEO | 92 | 92 |

### Indicadores adicionales

| Indicador | Inicio | Producto |
|---|---:|---:|
| First Contentful Paint (FCP) | 2,9 s | 2,7 s |
| Largest Contentful Paint (LCP) | 10,4 s | 14,6 s |

## 4. Análisis de resultados

Los resultados muestran puntuaciones favorables en accesibilidad y SEO, ambas con 92 puntos en las dos páginas evaluadas.

El rendimiento presenta oportunidades de mejora, especialmente por los tiempos elevados de Largest Contentful Paint.

La categoría de buenas prácticas obtuvo 78 puntos en la página principal y 74 puntos en la página de producto, lo que indica que deben revisarse aspectos técnicos del sitio.

## 5. Tres mejoras identificadas

### Mejora 1. Optimización de imágenes

Revisar el tamaño y formato de las imágenes del catálogo y los productos.

Se propone utilizar formatos modernos, como WebP, y ajustar las dimensiones para disminuir la transferencia de datos y contribuir a mejorar los tiempos de carga.

### Mejora 2. Optimización de recursos del sitio

Revisar los archivos CSS y JavaScript cargados por WordPress, WooCommerce, el tema y los plugins.

Se propone reducir recursos innecesarios y diferir scripts no esenciales para mejorar el rendimiento y reducir el tiempo de visualización del contenido principal.

### Mejora 3. Revisión de errores y buenas prácticas

Investigar los errores JavaScript observados en la consola del navegador, incluyendo el error relacionado con la generación del token del SDK de PayPal.

Se propone revisar las dependencias y configuraciones de los plugins, especialmente aquellas que no sean necesarias para el funcionamiento del proyecto.

Estas mejoras son propuestas iniciales que deberán verificarse mediante nuevas mediciones y pruebas.

## 6. Conclusión

La evaluación inicial permitió registrar ocho puntuaciones de Google Lighthouse, correspondientes a la página principal y a una página de producto de RCD Repuestos.

Los resultados reflejan un buen comportamiento inicial en accesibilidad y SEO, mientras que rendimiento y buenas prácticas presentan oportunidades de optimización.

Las mediciones se realizaron en LocalWP, por lo que constituyen una referencia del entorno de desarrollo. Para obtener una línea base de producción será necesario repetirlas en la tienda desplegada.

Estos resultados servirán como referencia para comparar la calidad del sistema durante el Proyecto Integrador.
