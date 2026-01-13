# 📦 Sistema de Gestión PHP Portable

Sistema completo de gestión de artículos con PHP y SQLite, 100% portable y sin necesidad de instalación en el sistema operativo.

## ✨ Características

- ✅ **100% Portable**: No modifica el registro de Windows ni instala servicios
- ✅ **Base de datos local**: SQLite sin necesidad de servidor
- ✅ **Instalación simple**: 2 clics para instalar y ejecutar
- ✅ **Compatible**: Windows 7, 8, 10, 11
- ✅ **CRUD Completo**: Alta, Baja, Modificación de artículos
- ✅ **Búsqueda**: Búsqueda por código, nombre o descripción
- ✅ **Migraciones**: Sistema de actualización de base de datos
- ✅ **Respaldo automático**: Los datos persisten entre actualizaciones

## 📁 Estructura del proyecto

```
mi-sistema/
├── instalador.bat          # Instalador del sistema
├── iniciar.bat            # Inicia el servidor
├── detener.bat            # Detiene el servidor
├── migrar.bat             # Ejecuta migraciones
├── README.md              # Este archivo
│
├── servidor/
│   └── php/               # PHP portable (descargar)
│
├── app/
│   ├── index.php          # Router principal
│   ├── config/
│   │   └── database.php   # Configuración BD
│   ├── models/
│   │   └── Articulo.php   # Modelo
│   ├── controllers/
│   │   └── ArticuloController.php
│   ├── views/
│   │   ├── layout.php
│   │   └── articulos/
│   │       ├── index.php
│   │       ├── crear.php
│   │       └── editar.php
│   └── database/
│       ├── database.db    # Base de datos SQLite
│       └── migrations/    # Scripts de migración
│
└── datos/
    └── backup/            # Respaldos
```

## 🚀 Instalación Inicial

### Paso 1: Descargar PHP Portable

1. Ve a: https://windows.php.net/download/
2. Descarga: **PHP 8.2 Thread Safe x64 ZIP** (o superior)
3. Extrae **TODO el contenido** del ZIP en `servidor\php\`
4. Verifica que exista: `servidor\php\php.exe`

### Paso 2: Ejecutar el instalador

Doble clic en: `instalador.bat`

Esto configurará:
- PHP con las extensiones necesarias
- Estructura de directorios
- Base de datos inicial

### Paso 3: Iniciar el sistema

Doble clic en: `iniciar.bat`

El navegador se abrirá automáticamente en `http://localhost:8080`

## 📖 Uso del sistema

### Iniciar el sistema
```batch
iniciar.bat
```
- Inicia el servidor PHP
- Abre automáticamente el navegador
- Muestra la URL de acceso

### Detener el sistema
```batch
detener.bat
```
- Detiene el servidor PHP
- Cierra todos los procesos relacionados

### Gestión de artículos

El sistema incluye:
- **Listado** con búsqueda
- **Crear** nuevo artículo
- **Editar** artículo existente
- **Eliminar** (baja lógica)

Campos disponibles:
- Código (único)
- Nombre
- Descripción
- Precio
- Stock
- Estado (Activo/Inactivo)

## 🔄 Actualizar el sistema en cliente

### Para actualizar el código (sin tocar datos)

1. **Respaldo** (opcional pero recomendado):
   - Copia `app\database\database.db` a `datos\backup\`

2. **Reemplazar archivos**:
   - Copia los nuevos archivos PHP
   - NO reemplaces `app\database\database.db`

3. **Ejecutar migraciones** (si hay cambios en BD):
   ```batch
   migrar.bat
   ```

### Crear una migración

Crea un archivo en `app\database\migrations\`:

```php
<?php
// migration_002_nueva_funcionalidad.php
return [
    'version' => '002_nueva_funcionalidad',
    'descripcion' => 'Agrega nueva columna',
    'sql' => "
        ALTER TABLE articulos ADD COLUMN nueva_columna TEXT;
    "
];
?>
```

Nombra los archivos: `migration_XXX_descripcion.php`

## 🛠️ Desarrollo

### Agregar nuevo módulo

1. **Crear Model** en `app/models/`:
```php
<?php
class NuevoModulo {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    // Métodos CRUD
}
?>
```

2. **Crear Controller** en `app/controllers/`:
```php
<?php
class NuevoModuloController {
    private $model;
    
    public function __construct() {
        $this->model = new NuevoModulo();
    }
    
    public function index() {
        // Lógica
        $this->render('nuevomodulo/index', $data);
    }
    
    private function render($view, $data = []) {
        extract($data);
        ob_start();
        require BASE_PATH . '/views/' . $view . '.php';
        $content = ob_get_clean();
        require BASE_PATH . '/views/layout.php';
    }
}
?>
```

3. **Crear Vista** en `app/views/nuevomodulo/`:
```php
<!-- index.php -->
<h2>Nuevo Módulo</h2>
<!-- Contenido -->
```

4. **Acceder**: `http://localhost:8080?c=nuevomodulo&a=index`

### Estructura de URLs

```
?c=controlador&a=accion&id=123
```

Ejemplos:
- `?c=articulo&a=index` → Lista artículos
- `?c=articulo&a=crear` → Formulario crear
- `?c=articulo&a=editar&id=5` → Editar artículo 5

## 📦 Distribución a clientes

### Preparar paquete

1. Incluye todo excepto:
   - `app\database\database.db` (se crea automáticamente)
   - `datos\backup\*` (respaldos del desarrollador)

2. Comprime en ZIP:
   ```
   mi-sistema.zip
   ├── instalador.bat
   ├── iniciar.bat
   ├── detener.bat
   ├── README.md
   ├── servidor\
   └── app\
   ```

### Instrucciones para el cliente

1. Descomprimir en cualquier carpeta
2. Ejecutar `instalador.bat`
3. Ejecutar `iniciar.bat`
4. Usar el sistema

## 🔒 Seguridad

⚠️ **Este sistema está diseñado para uso LOCAL**

- No exponer a Internet sin medidas de seguridad adicionales
- Los datos se almacenan en archivo local SQLite
- No hay autenticación de usuarios por defecto
- Validar y sanitizar inputs si se expone públicamente

## 🐛 Solución de problemas

### El navegador no abre
- Verifica que el puerto 8080 esté libre
- Abre manualmente: `http://localhost:8080`

### Error "PHP no encontrado"
- Verifica que `servidor\php\php.exe` exista
- Ejecuta nuevamente `instalador.bat`

### Error de base de datos
- Verifica permisos de escritura en `app\database\`
- Elimina `database.db` y reinicia el sistema

### El servidor no detiene
- Ejecuta `detener.bat` varias veces
- O: `taskkill /F /IM php.exe` desde CMD

## 📝 Notas técnicas

- **PHP**: 8.2+ (Thread Safe)
- **Base de datos**: SQLite 3
- **Servidor web**: PHP Built-in Server
- **Sistema operativo**: Windows 7+
- **Arquitectura**: MVC simple
- **Persistencia**: PDO con SQLite

## 🎯 Próximas mejoras sugeridas

- [ ] Autenticación de usuarios
- [ ] Exportar/Importar datos (CSV, Excel)
- [ ] Generación de reportes PDF
- [ ] Sistema de respaldos automáticos
- [ ] Interfaz de escritorio (Electron o similar)
- [ ] Multi-idioma
- [ ] Temas personalizables

## 📄 Licencia

Sistema de uso libre para proyectos personales y comerciales.

---

**¿Necesitas ayuda?** Consulta este README o revisa los comentarios en el código.