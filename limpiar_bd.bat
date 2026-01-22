@echo off

echo ========================================
echo   Limpiar Base de Datos para Distribucion
echo ========================================
echo.
echo ADVERTENCIA: Este script eliminara TODOS los datos
echo del sistema, dejando solo la estructura.
echo.
echo Esto incluye:
echo - Productos padre
echo - Variedades
echo - Ventas
echo - Pedidos
echo - Egresos
echo - Movimientos de caja
echo.
echo Use esto solo para preparar el sistema para
echo instalar en una nueva PC.
echo.
pause

:: Crear backup antes de limpiar
if exist "app\database\database.db" (
    echo [*] Creando backup de seguridad...
    if not exist "datos\backup" mkdir "datos\backup"
    set TIMESTAMP=%date:~-4,4%%date:~-10,2%%date:~-7,2%_%time:~0,2%%time:~3,2%%time:~6,2%
    set TIMESTAMP=%TIMESTAMP: =0%
    copy "app\database\database.db" "datos\backup\database_antes_limpiar_%TIMESTAMP%.db"
    echo [OK] Backup creado
    echo.
)

:: Confirmar nuevamente
echo.
echo ULTIMA CONFIRMACION:
echo [Esta accion NO se puede deshacer]
echo.
set /p CONFIRMAR=Escriba SI para continuar: 

if not "%CONFIRMAR%"=="SI" (
    echo.
    echo [*] Operacion cancelada
    pause
    exit /b
)

:: Eliminar la base de datos
echo.
echo [*] Eliminando base de datos actual...
if exist "app\database\database.db" (
    del "app\database\database.db"
    echo [OK] Base de datos eliminada
)

:: Crear nueva base de datos vacia
echo [*] Creando nueva base de datos vacia...
servidor\php\php.exe -f app\index.php >nul 2>&1

if exist "app\database\database.db" (
    echo [OK] Base de datos limpia creada
    echo.
    echo ========================================
    echo   Limpieza Completada
    echo ========================================
    echo.
    echo La base de datos esta ahora vacia y lista
    echo para ser instalada en otra PC.
    echo.
    echo El backup se guardo en:
    echo datos\backup\database_antes_limpiar_%TIMESTAMP%.db
) else (
    echo [ERROR] No se pudo crear la base de datos
)

echo.
pause