@echo off

echo ========================================
echo   INSTALADOR - Sistema de Gestión
echo ========================================
echo.

:: Verificar si ya existe la instalación
if exist "servidor\php\php.exe" (
    echo [OK] PHP ya está instalado
) else (
    echo [*] Descargando PHP portable...
    echo.
    echo NOTA: Este script requiere que descargues PHP manualmente.
    echo.
    echo Pasos:
    echo 1. Ve a: https://windows.php.net/download/
    echo 2. Descarga: PHP 8.2 Thread Safe x64 ZIP
    echo 3. Extrae el contenido en la carpeta: servidor\php\
    echo 4. Vuelve a ejecutar este instalador
    echo.
    pause
    exit /b
)

:: Configurar PHP
echo [*] Configurando PHP...
if not exist "servidor\php\php.ini" (
    copy "servidor\php\php.ini-development" "servidor\php\php.ini" >nul
    
    :: Habilitar extensiones necesarias
    powershell -Command "(gc servidor\php\php.ini) -replace ';extension=pdo_sqlite', 'extension=pdo_sqlite' | Out-File -encoding ASCII servidor\php\php.ini"
    powershell -Command "(gc servidor\php\php.ini) -replace ';extension=sqlite3', 'extension=sqlite3' | Out-File -encoding ASCII servidor\php\php.ini"
    powershell -Command "(gc servidor\php\php.ini) -replace ';extension=mbstring', 'extension=mbstring' | Out-File -encoding ASCII servidor\php\php.ini"
    
    echo [OK] PHP configurado correctamente
) else (
    echo [OK] PHP ya configurado
)

:: Crear estructura de directorios
echo [*] Creando estructura de directorios...
if not exist "app\database" mkdir "app\database"
if not exist "app\database\migrations" mkdir "app\database\migrations"
if not exist "datos\backup" mkdir "datos\backup"
echo [OK] Directorios creados

:: Inicializar base de datos
echo [*] Inicializando base de datos...
servidor\php\php.exe -f app\index.php >nul 2>&1
if exist "app\database\database.db" (
    echo [OK] Base de datos inicializada
) else (
    echo [ERROR] No se pudo crear la base de datos
    pause
    exit /b
)

echo.
echo ========================================
echo   INSTALACIÓN COMPLETADA
echo ========================================
echo.
echo Para iniciar el sistema ejecute: iniciar.bat
echo.
pause