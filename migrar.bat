@echo off

echo ========================================
echo   Migrar Sistema a Nueva Estructura
echo ========================================
echo.
echo ADVERTENCIA: Este proceso modificara la base de datos.
echo Se recomienda hacer una copia de seguridad antes.
echo.
pause

:: Hacer backup de la base de datos actual
if exist "app\database\database.db" (
    echo [*] Creando backup de la base de datos...
    if not exist "datos\backup" mkdir "datos\backup"
    set TIMESTAMP=%date:~-4,4%%date:~-10,2%%date:~-7,2%_%time:~0,2%%time:~3,2%%time:~6,2%
    set TIMESTAMP=%TIMESTAMP: =0%
    copy "app\database\database.db" "datos\backup\database_backup_%TIMESTAMP%.db"
    echo [OK] Backup creado: datos\backup\database_backup_%TIMESTAMP%.db
    echo.
)

:: Eliminar base de datos antigua
echo [*] Eliminando base de datos antigua...
if exist "app\database\database.db" (
    del "app\database\database.db"
    echo [OK] Base de datos antigua eliminada
)

:: Inicializar nueva base de datos
echo [*] Inicializando nueva estructura de base de datos...
servidor\php\php.exe -f app\index.php >nul 2>&1

if exist "app\database\database.db" (
    echo [OK] Nueva base de datos creada exitosamente
    echo.
    echo ========================================
    echo   MIGRACION COMPLETADA
    echo ========================================
    echo.
    echo La base de datos ha sido actualizada con la nueva estructura.
    echo.
    echo Nuevas tablas creadas:
    echo - productos_padre
    echo - variedades
    echo - promociones
    echo - pedidos y pedido_detalles
    echo - ventas y venta_detalles (actualizadas)
    echo.
    echo Puede iniciar el sistema con: iniciar.bat
) else (
    echo [ERROR] No se pudo crear la base de datos
)

echo.
pause