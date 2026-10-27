# Respaldo y restauración de RCD Repuestos

## 1. Objetivo

Establecer un procedimiento de respaldo y restauración para la tienda virtual RCD Repuestos, desarrollada con WordPress y WooCommerce, con el propósito de proteger los archivos, productos, imágenes, configuraciones y datos almacenados en la base de datos.

Este procedimiento permite recuperar una copia funcional de la tienda ante posibles errores, pérdida de información o fallos técnicos.

## 2. Herramientas utilizadas

- **WordPress:** sistema de gestión de contenidos utilizado para la tienda.
- **WooCommerce:** plugin encargado de administrar los productos y las funcionalidades comerciales.
- **LocalWP:** entorno de desarrollo local utilizado para realizar el respaldo y comprobar su restauración.
- **MySQL:** sistema de gestión de base de datos utilizado por la tienda.
- **Google Drive:** plataforma utilizada para compartir el video de demostración.
- **GitHub:** repositorio del código fuente y de la documentación del proyecto.

## 3. Procedimiento de respaldo de los archivos

Para realizar el respaldo se utilizó la herramienta de exportación de LocalWP, donde se encontraba funcionando la tienda RCD Repuestos.

El procedimiento realizado fue el siguiente:

1. Abrir la aplicación LocalWP.
2. Seleccionar el sitio denominado `RCD-Repuestos-Local-Dylan`.
3. Verificar que la tienda estuviera funcionando correctamente.
4. Acceder al menú de opciones del sitio y seleccionar **Export**.
5. Elegir la ubicación de almacenamiento del respaldo.
6. Ejecutar la exportación del sitio.
7. Guardar el archivo comprimido en formato ZIP.

El respaldo generado contiene los archivos necesarios para recuperar la instalación de WordPress, incluyendo los componentes de la tienda, plugins, temas e imágenes.

## 4. Procedimiento de respaldo de la base de datos

La tienda utiliza MySQL para almacenar la información correspondiente a los productos, precios, inventarios y configuraciones de WordPress y WooCommerce.

El respaldo de la base de datos se realizó mediante la función de exportación de LocalWP.

Esta herramienta permite generar un paquete que incluye los archivos del sitio y una exportación SQL de su base de datos.

El procedimiento fue:

1. Seleccionar la tienda en LocalWP.
2. Ejecutar la función de exportación.
3. Generar el archivo ZIP del sitio completo.
4. Conservar el respaldo para una posterior restauración.
5. Comprobar la recuperación de los datos mediante la importación en otra instalación local.

La recuperación exitosa del catálogo permitió verificar que los datos necesarios para mostrar los productos se restauraron correctamente.

## 5. Frecuencia de los respaldos

Se establece la siguiente política de respaldo manual para el proyecto:

| Tipo de respaldo | Frecuencia |
|---|---|
| Respaldo completo de archivos y base de datos | Una vez por semana |
| Respaldo previo a cambios importantes | Antes de actualizar plugins, modificar funcionalidades o realizar cambios significativos |
| Respaldo previo a entregables | Antes de generar la versión final que será entregada |

Esta frecuencia se establece como procedimiento de trabajo del equipo. La automatización de los respaldos no forma parte de la prueba realizada.

## 6. Almacenamiento de los respaldos

Los respaldos deben conservarse en una carpeta privada de respaldo, fuera del directorio del proyecto y del repositorio GitHub.

Se propone utilizar la siguiente ubicación local:

`C:\Users\dylan\Videos\Respaldos-RCD`

La carpeta debe contener las copias ZIP identificadas por su fecha de creación.

Como medida de seguridad adicional, se recomienda conservar una segunda copia en un espacio privado de almacenamiento externo o en la nube, con acceso restringido.

**Los respaldos de archivos y bases de datos no deben subirse al repositorio GitHub**, ya que pueden contener información sensible y archivos innecesarios para el control de versiones.
# Respaldo y restauración de RCD Repuestos

## 1. Objetivo

Establecer un procedimiento de respaldo y restauración para la tienda virtual RCD Repuestos, desarrollada con WordPress y WooCommerce, con el propósito de proteger los archivos, productos, imágenes, configuraciones y datos almacenados en la base de datos.

Este procedimiento permite recuperar una copia funcional de la tienda ante posibles errores, pérdida de información o fallos técnicos.

## 2. Herramientas utilizadas

- **WordPress:** sistema de gestión de contenidos utilizado para la tienda.
- **WooCommerce:** plugin encargado de administrar los productos y las funcionalidades comerciales.
- **LocalWP:** entorno de desarrollo local utilizado para realizar el respaldo y comprobar su restauración.
- **MySQL:** sistema de gestión de base de datos utilizado por la tienda.
- **Google Drive:** plataforma utilizada para compartir el video de demostración.
- **GitHub:** repositorio del código fuente y de la documentación del proyecto.

## 3. Procedimiento de respaldo de los archivos

Para realizar el respaldo se utilizó la herramienta de exportación de LocalWP, donde se encontraba funcionando la tienda RCD Repuestos.

El procedimiento realizado fue el siguiente:

1. Abrir la aplicación LocalWP.
2. Seleccionar el sitio denominado `RCD-Repuestos-Local-Dylan`.
3. Verificar que la tienda estuviera funcionando correctamente.
4. Acceder al menú de opciones del sitio y seleccionar **Export**.
5. Elegir la ubicación de almacenamiento del respaldo.
6. Ejecutar la exportación del sitio.
7. Guardar el archivo comprimido en formato ZIP.

El respaldo generado contiene los archivos necesarios para recuperar la instalación de WordPress, incluyendo los componentes de la tienda, plugins, temas e imágenes.

## 4. Procedimiento de respaldo de la base de datos

La tienda utiliza MySQL para almacenar la información correspondiente a los productos, precios, inventarios y configuraciones de WordPress y WooCommerce.

El respaldo de la base de datos se realizó mediante la función de exportación de LocalWP.

Esta herramienta permite generar un paquete que incluye los archivos del sitio y una exportación SQL de su base de datos.

El procedimiento fue:

1. Seleccionar la tienda en LocalWP.
2. Ejecutar la función de exportación.
3. Generar el archivo ZIP del sitio completo.
4. Conservar el respaldo para una posterior restauración.
5. Comprobar la recuperación de los datos mediante la importación en otra instalación local.

La recuperación exitosa del catálogo permitió verificar que los datos necesarios para mostrar los productos se restauraron correctamente.

## 5. Frecuencia de los respaldos

Se establece la siguiente política de respaldo manual para el proyecto:

| Tipo de respaldo | Frecuencia |
|---|---|
| Respaldo completo de archivos y base de datos | Una vez por semana |
| Respaldo previo a cambios importantes | Antes de actualizar plugins, modificar funcionalidades o realizar cambios significativos |
| Respaldo previo a entregables | Antes de generar la versión final que será entregada |

Esta frecuencia se establece como procedimiento de trabajo del equipo. La automatización de los respaldos no forma parte de la prueba realizada.

## 6. Almacenamiento de los respaldos

Los respaldos deben conservarse en una carpeta privada de respaldo, fuera del directorio del proyecto y del repositorio GitHub.

Se propone utilizar la siguiente ubicación local:

`C:\Users\dylan\Documents\Respaldos-RCD`

La carpeta debe contener las copias ZIP identificadas por su fecha de creación.

Como medida de seguridad adicional, se recomienda conservar una segunda copia en un espacio privado de almacenamiento externo o en la nube, con acceso restringido.

**Los respaldos de archivos y bases de datos no deben subirse al repositorio GitHub**, ya que pueden contener información sensible y archivos innecesarios para el control de versiones.

## 7. Procedimiento de restauración en LocalWP

Para comprobar la recuperación de la tienda se realizó una restauración utilizando el archivo ZIP obtenido durante el respaldo local.

El procedimiento realizado fue el siguiente:

1. Abrir la aplicación LocalWP.
2. Seleccionar la opción **File → Import Site**.
3. Buscar y seleccionar el archivo ZIP generado previamente.
4. Asignar el nombre `RCD-Prueba-Restauracion` al nuevo sitio.
5. Seleccionar el entorno de ejecución correspondiente.
6. Iniciar el proceso de importación.
7. Esperar a que LocalWP completara la extracción de archivos y la restauración de la base de datos.
8. Iniciar el nuevo sitio y abrirlo desde el navegador.
9. Acceder al catálogo de productos.
10. Comprobar que los productos, imágenes, nombres y precios se visualizaran correctamente.

### Resultado de la restauración

La restauración local se completó satisfactoriamente.

Se verificó la presencia de **15 productos de la tienda RCD Repuestos**, incluyendo sus imágenes y precios en colones.

El sitio restaurado funcionó de manera independiente del sitio original, lo que permitió comprobar la recuperación del catálogo sin modificar la instalación inicial.

## 8. Video de demostración de restauración

Como evidencia del procedimiento se realizó una grabación de la restauración de la tienda RCD Repuestos en LocalWP.

La demostración presenta el proceso de importación del respaldo y la comprobación del catálogo de productos en el sitio restaurado.

**Enlace del video:**

[Ver video de restauración en Google Drive](https://drive.google.com/file/d/1ZWVCxxTc_Kh3PMKkb7nzEIuV7SofZhXT/view?usp=sharing)

La grabación debe respetar el límite de tres minutos y el requisito de continuidad establecidos en el enunciado.

## 9. Alcance de la prueba

La restauración documentada se realizó a partir de un respaldo generado en LocalWP.

Adicionalmente, la tienda original se compartió temporalmente mediante la función Live Link de LocalWP para comprobar su acceso desde otros dispositivos.

Live Link permite acceder a la instalación local a través de Internet, pero no constituye un despliegue independiente en hosting.

Por lo tanto, esta práctica demuestra el procedimiento técnico de respaldo y restauración local. El requisito de restaurar un respaldo procedente de una tienda desplegada en hosting deberá verificarse por separado para cumplir íntegramente el apartado 5.5 del enunciado.

## 10. Conclusión

La práctica permitió aplicar un procedimiento de respaldo y recuperación de una tienda desarrollada con WordPress y WooCommerce.

Mediante la exportación e importación realizada en LocalWP se comprobó la recuperación de los archivos y de los datos necesarios para visualizar el catálogo de 15 productos.

Esta experiencia demuestra la importancia de mantener copias de seguridad periódicas, conservarlas fuera del repositorio y comprobar que puedan restaurarse correctamente.

El procedimiento documentado constituye una base para futuras estrategias de respaldo y recuperación del sistema RCD Repuestos.
## 7. Procedimiento de restauración en LocalWP

Para comprobar la recuperación de la tienda se realizó una restauración utilizando el archivo ZIP obtenido durante el respaldo local.

El procedimiento realizado fue el siguiente:

1. Abrir la aplicación LocalWP.
2. Seleccionar la opción **File → Import Site**.
3. Buscar y seleccionar el archivo ZIP generado previamente.
4. Asignar el nombre `RCD-Prueba-Restauracion` al nuevo sitio.
5. Seleccionar el entorno de ejecución correspondiente.
6. Iniciar el proceso de importación.
7. Esperar a que LocalWP completara la extracción de archivos y la restauración de la base de datos.
8. Iniciar el nuevo sitio y abrirlo desde el navegador.
9. Acceder al catálogo de productos.
10. Comprobar que los productos, imágenes, nombres y precios se visualizaran correctamente.

### Resultado de la restauración

La restauración local se completó satisfactoriamente.

Se verificó la presencia de **15 productos de la tienda RCD Repuestos**, incluyendo sus imágenes y precios en colones.

El sitio restaurado funcionó de manera independiente del sitio original, lo que permitió comprobar la recuperación del catálogo sin modificar la instalación inicial.

## 8. Video de demostración de restauración

Como evidencia del procedimiento se realizó una grabación de la restauración de la tienda RCD Repuestos en LocalWP.

La demostración presenta el proceso de importación codel respaldo y la comprobación del catálogo de productos en el sitio restaurado.

**Enlace del video:**

[Ver video de restauración en Google Drive](https://drive.google.com/file/d/1ZWVCxxTc_Kh3PMKkb7nzEIuV7SofZhXT/view?usp=sharing)

La grabación debe respetar el límite de tres minutos y el requisito de continuidad establecidos en el enunciado.

## 9. Alcance de la prueba

La restauración documentada se realizó a partir de un respaldo generado en LocalWP.

Adicionalmente, la tienda original se compartió temporalmente mediante la función Live Link de LocalWP para comprobar su acceso desde otros dispositivos.

Live Link permite acceder a la instalación local a través de Internet, pero no constituye un despliegue independiente en hosting.

Por lo tanto, esta práctica demuestra el procedimiento técnico de respaldo y restauración local. El requisito de restaurar un respaldo procedente de una tienda desplegada en hosting deberá verificarse por separado para cumplir íntegramente el apartado 5.5 del enunciado.

## 10. Conclusión

La práctica permitió aplicar un procedimiento de respaldo y recuperación de una tienda desarrollada con WordPress y WooCommerce.

Mediante la exportación e importación realizada en LocalWP se comprobó la recuperación de los archivos y de los datos necesarios para visualizar el catálogo de 15 productos.

Esta experiencia demuestra la importancia de mantener copias de seguridad periódicas, conservarlas fuera del repositorio y comprobar que puedan restaurarse correctamente.

El procedimiento documentado constituye una base para futuras estrategias de respaldo y recuperación del sistema RCD Repuestos.