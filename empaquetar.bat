@echo off

echo ========================================
echo   Empaquetar Sistema para Distribución
echo ========================================
echo.

set NOMBRE_PAQUETE=sistema-gestion-v1.0

echo [*] Creando directorio temporal...
if exist "dist" rmdir /s /q "dist"
mkdir "dist\%NOMBRE_PAQUETE%"

echo [*] Copiando archivos del sistema...
xcopy "app" "dist\%NOMBRE_PAQUETE%\app\" /E /I /Y >nul
xcopy "servidor" "dist\%NOMBRE_PAQUETE%\servidor\" /E /I /Y >nul

echo [*] Copiando scripts de ejecución...
copy "instalador.bat" "dist\%NOMBRE_PAQUETE%\" >nul
copy "iniciar.bat" "dist\%NOMBRE_PAQUETE%\" >nul
copy "detener.bat" "dist\%NOMBRE_PAQUETE%\" >nul
copy "migrar.bat" "dist\%NOMBRE_PAQUETE%\" >nul
copy "README.md" "dist\%NOMBRE_PAQUETE%\" >nul

echo [*] Limpiando base de datos de desarrollo...
if exist "dist\%NOMBRE_PAQUETE%\app\database\database.db" (
    del "dist\%NOMBRE_PAQUETE%\app\database\database.db"
)

echo [*] Creando estructura de carpetas...
if not exist "dist\%NOMBRE_PAQUETE%\datos\backup" mkdir "dist\%NOMBRE_PAQUETE%\datos\backup"

echo [*] Creando archivo ZIP...
powershell Compress-Archive -Path "dist\%NOMBRE_PAQUETE%\*" -DestinationPath "dist\%NOMBRE_PAQUETE%.zip" -Force

if exist "dist\%NOMBRE_PAQUETE%.zip" (
    echo [OK] Paquete creado: dist\%NOMBRE_PAQUETE%.zip
    echo.
    echo Tamaño:
    dir "dist\%NOMBRE_PAQUETE%.zip" | findstr "%NOMBRE_PAQUETE%"
) else (
    echo [ERROR] No se pudo crear el ZIP
)

echo.
echo Archivos incluidos:
dir /s /b "dist\%NOMBRE_PAQUETE%" | find /c /v ""
echo archivos en total

echo.
echo ========================================
echo   Listo para distribuir
echo ========================================
echo.
echo El paquete incluye:
echo - Todo el código fuente
echo - PHP portable configurado
echo - Scripts de instalación
echo - Documentación completa
echo.
echo NO incluye:
echo - Base de datos (se crea en instalación)
echo - Datos del desarrollador
echo.
pause