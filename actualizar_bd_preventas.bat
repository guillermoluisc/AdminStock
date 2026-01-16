@echo off

echo ========================================
echo   Actualizar BD - Agregar Pre-ventas
echo ========================================
echo.
echo Este script agregara las columnas necesarias
echo para el sistema de pre-ventas.
echo.
pause

:: Verificar si PHP existe
if not exist "servidor\php\php.exe" (
    echo [ERROR] PHP no encontrado
    pause
    exit /b
)

:: Hacer backup
if exist "app\database\database.db" (
    echo [*] Creando backup...
    if not exist "datos\backup" mkdir "datos\backup"
    set TIMESTAMP=%date:~-4,4%%date:~-10,2%%date:~-7,2%_%time:~0,2%%time:~3,2%%time:~6,2%
    set TIMESTAMP=%TIMESTAMP: =0%
    copy "app\database\database.db" "datos\backup\database_antes_preventas_%TIMESTAMP%.db"
    echo [OK] Backup creado
)

:: Ejecutar migracion
echo.
echo [*] Actualizando estructura de base de datos...
servidor\php\php.exe -r "require 'app/config/database.php'; define('DB_PATH', 'app/database/database.db'); Database::initialize(); echo '[OK] Base de datos actualizada';"

echo.
echo ========================================
echo   Migracion Completada
echo ========================================
echo.
echo Las siguientes columnas fueron agregadas:
echo - nombre_cliente
echo - estado
echo - fecha_formalizacion
echo.
pause