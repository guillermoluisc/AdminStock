@echo off

cls
echo ========================================
echo   Sistema de Gestión - INICIANDO
echo ========================================
echo.

:: Verificar si PHP existe
if not exist "servidor\php\php.exe" (
    echo [ERROR] PHP no encontrado. Ejecute primero instalador.bat
    pause
    exit /b
)

:: Buscar un puerto disponible
set PORT=8080
:check_port
netstat -ano | findstr ":%PORT%" >nul
if %errorlevel% equ 0 (
    set /a PORT=%PORT%+1
    goto check_port
)

echo [*] Puerto disponible: %PORT%
echo [*] Iniciando servidor PHP...
echo.

:: Iniciar servidor PHP
start /min cmd /c "servidor\php\php.exe -S localhost:%PORT% -t app > servidor\servidor.log 2>&1"

:: Esperar a que el servidor inicie
timeout /t 2 >nul

:: Verificar que el servidor está corriendo
netstat -ano | findstr ":%PORT%" >nul
if %errorlevel% equ 0 (
    echo [OK] Servidor iniciado correctamente
    echo.
    echo ========================================
    echo   SISTEMA EN FUNCIONAMIENTO
    echo ========================================
    echo.
    echo   URL: http://localhost:%PORT%
    echo.
    echo   Presione cualquier tecla para abrir
    echo   el navegador...
    echo.
    echo   Para detener el sistema ejecute:
    echo   detener.bat
    echo ========================================
    echo.
    
    :: Guardar el puerto para el script detener.bat
    echo %PORT% > servidor\puerto.txt
    
    pause
    
    :: Abrir navegador
    start http://localhost:%PORT%
    
    echo.
    echo El sistema está funcionando.
    echo NO CIERRE ESTA VENTANA.
    echo.
    echo Para detener el sistema:
    echo - Presione CTRL+C en esta ventana
    echo - O ejecute detener.bat
    echo.
    
    :: Mantener la ventana abierta
    pause >nul
) else (
    echo [ERROR] No se pudo iniciar el servidor
    pause
)

exit /b