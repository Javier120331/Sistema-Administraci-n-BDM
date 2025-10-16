@echo off
echo ========================================
echo Script de reparacion MySQL XAMPP
echo ========================================
echo.

REM Detener MySQL si está corriendo
echo [1/4] Deteniendo MySQL...
taskkill /F /IM mysqld.exe 2>nul
timeout /t 2 >nul

REM Verificar puerto 3306
echo [2/4] Verificando puerto 3306...
netstat -ano | findstr :3306
if %ERRORLEVEL% EQU 0 (
    echo ADVERTENCIA: El puerto 3306 esta ocupado
    echo Cierra cualquier programa que use MySQL
    pause
    exit
)

REM Backup de configuración
echo [3/4] Creando backup de my.ini...
if exist "C:\xampp\mysql\bin\my.ini" (
    copy "C:\xampp\mysql\bin\my.ini" "C:\xampp\mysql\bin\my.ini.backup_%date:~-4,4%%date:~-10,2%%date:~-7,2%"
)

REM Intentar reparar
echo [4/4] Intentando reparar base de datos...
cd C:\xampp\mysql\bin
if exist "C:\xampp\mysql\data\aria_log_control" (
    echo Eliminando archivos de log corruptos...
    del /F /Q "C:\xampp\mysql\data\aria_log.*" 2>nul
    del /F /Q "C:\xampp\mysql\data\ib_logfile*" 2>nul
)

echo.
echo ========================================
echo Reparacion completada
echo ========================================
echo.
echo AHORA:
echo 1. Abre XAMPP Control Panel
echo 2. Inicia MySQL
echo 3. Si sigue fallando, revisa los logs
echo.
pause
