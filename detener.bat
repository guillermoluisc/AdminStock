@echo off

echo ========================================
echo   Sistema de Gestión - DETENIENDO
echo ========================================
echo.

:: Leer el puerto guardado
if exist "servidor\puerto.txt" (
    set /p PORT=<servidor\puerto.txt
) else (
    set PORT=8080
)

echo [*] Buscando proceso PHP en puerto %PORT%...

:: Encontrar y matar el proceso PHP
for /f "tokens=5" %%a in ('netstat -ano ^| findstr ":%PORT%"') do (
    echo [*] Deteniendo proceso %%a...
    taskkill /F /PID %%a >nul 2>&1
)

:: Buscar todos los procesos php.exe y detenerlos
tasklist | findstr "php.exe" >nul
if %errorlevel% equ 0 (
    echo [*] Deteniendo todos los procesos PHP...
    taskkill /F /IM php.exe >nul 2>&1
)

echo [OK] Sistema detenido
echo.
pause