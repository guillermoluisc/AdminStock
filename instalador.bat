@echo off

echo ========================================
echo   INSTALADOR - Sistema de Gesti n
echo ========================================
echo.

:: Verificar PHP
if exist "servidor\php\php.exe" (
    echo [OK] PHP ya est  instalado
) else (
    echo [ERROR] PHP no encontrado
    echo.
    echo Pasos para instalar PHP:
    echo 1. Descarga: https://windows.php.net/download/
    echo 2. Busca: PHP 8.2 Thread Safe x64 ZIP
    echo 3. Extrae el contenido en: servidor\php\
    echo 4. Vuelve a ejecutar este instalador
    echo.
    pause
    exit /b
)

:: Configurar PHP
echo [*] Configurando PHP...
if not exist "servidor\php\php.ini" (
    copy "servidor\php\php.ini-development" "servidor\php\php.ini" >nul
    
    powershell -Command "(gc servidor\php\php.ini) -replace ';extension=pdo_sqlite', 'extension=pdo_sqlite' | Out-File -encoding ASCII servidor\php\php.ini"
    powershell -Command "(gc servidor\php\php.ini) -replace ';extension=sqlite3', 'extension=sqlite3' | Out-File -encoding ASCII servidor\php\php.ini"
    powershell -Command "(gc servidor\php\php.ini) -replace ';extension=mbstring', 'extension=mbstring' | Out-File -encoding ASCII servidor\php\php.ini"
    
    echo [OK] PHP configurado
) else (
    echo [OK] PHP ya configurado
)

:: Crear estructura de directorios
echo [*] Creando estructura de directorios...
if not exist "app\database" mkdir "app\database"
if not exist "app\database\migrations" mkdir "app\database\migrations"
if not exist "datos\backup" mkdir "datos\backup"
echo [OK] Directorios creados

:: Verificar si ya existe base de datos
if exist "app\database\database.db" (
    echo.
    echo [!] ADVERTENCIA: Ya existe una base de datos
    echo.
    echo Desea ELIMINAR la base de datos actual y crear una nueva? (SI/NO)
    set /p REINSTALAR=
 
    
    if "%REINSTALAR%"=="SI" (
        echo [*] Creando backup de seguridad...
        set TIMESTAMP=%date:~-4,4%%date:~-10,2%%date:~-7,2%_%time:~0,2%%time:~3,2%%time:~6,2%
        set TIMESTAMP=%TIMESTAMP: =0%
        copy "app\database\database.db" "datos\backup\database_backup_%TIMESTAMP%.db" >nul
        echo [OK] Backup guardado
        
        del "app\database\database.db"
        echo [OK] Base de datos antigua eliminada
    ) else (
        echo [*] Se mantendr  la base de datos existente
        echo.
        echo ========================================
        echo   INSTALACI N COMPLETADA
        echo ========================================
        echo.
        echo Para iniciar el sistema ejecute: iniciar.bat
        echo.
        pause
        exit /b
    )
)

:: Inicializar nueva base de datos
echo [*] Inicializando base de datos vac a...
servidor\php\php.exe -f app\index.php >nul 2>&1

if exist "app\database\database.db" (
    echo [OK] Base de datos inicializada correctamente
) else (
    echo [ERROR] No se pudo crear la base de datos
    pause
    exit /b
)

echo.
echo ========================================
echo   INSTALACI N COMPLETADA
echo ========================================
echo.
echo ? Sistema instalado correctamente
echo ? Base de datos vac a creada
echo.
echo Para iniciar el sistema ejecute: iniciar.bat
echo.
pause