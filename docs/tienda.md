# Configuración de la Tienda (Avance 1)

## 1. Tabla de Plugins Instalados

> **Nota:** Se excluyen de esta tabla WooCommerce y el plugin propio de la tienda.

| Nombre del Plugin | Versión | Propósito / Uso | Última Actualización | Estado de Vulnerabilidades (WPScan / Patchstack) |
| :--- | :---: | :--- | :---: | :--- |
| **Rank Math SEO** | 1.0.280 | Optimización SEO en buscadores y generación del mapa del sitio XML. | 26/10/2026 | **Sin vulnerabilidades reportadas** *(Consultado el 26/10/2026)* |
| **All-In-One Security (AIOS)** | 5.5.0 | Firewall, protección contra ataques y limitación de intentos de login. | 26/10/2026 | **Sin vulnerabilidades críticas activas** *(Consultado el 26/10/2026)* |
| **UpdraftPlus WP Backup** | 1.24.14 | Gestión de respaldos automatizados de archivos y base de datos. | 26/10/2026 | **Sin vulnerabilidades reportadas** *(Consultado el 26/10/2026)* |

---

## 2. Lista de Verificación de Seguridad y Configuración

- [x] No existe un usuario llamado `admin`.
- [x] La edición de archivos desde el panel de administración está desactivada.
- [x] XML-RPC está desactivado.
- [x] Los intentos de inicio de sesión están limitados (vía AIOS).
- [x] WordPress, el tema y los plugins están actualizados a la fecha.
- [x] Los enlaces permanentes son legibles (estructura por nombre de entrada).
- [x] El mapa del sitio XML está activo y disponible en:  
  `http://rdc-repuestos-local-christopher.local/sitemap_index.xml`