@echo off
setlocal enabledelayedexpansion

REM Archivo de salida
set OUTPUT=contenido_total.txt

REM Limpiar archivo si ya existe
if exist "%OUTPUT%" del "%OUTPUT%"

REM Directorio actual
set BASEDIR=%CD%

echo Procesando archivos en: %BASEDIR%
echo.

REM Buscar archivos .bat y .php recursivamente
for /r "%BASEDIR%" %%F in (*.bat *.php) do (
    echo Ruta del archivo: %%F>>"%OUTPUT%"
    echo Contenido:>>"%OUTPUT%"
    echo ---------------------------------------->>"%OUTPUT%"

    REM Copiar contenido del archivo
    type "%%F">>"%OUTPUT%"

    echo.>>"%OUTPUT%"
    echo ========================================>>"%OUTPUT%"
    echo.>>"%OUTPUT%"
)

echo.
echo Proceso finalizado.
echo Archivo generado: %OUTPUT%
pause
