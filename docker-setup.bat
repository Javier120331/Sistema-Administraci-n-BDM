@echo off
echo 🚀 Iniciando configuración del proyecto Suelditas Beta con Docker...

REM Verificar si Docker está instalado
docker --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ Docker no está instalado. Por favor instala Docker Desktop primero.
    pause
    exit /b 1
)

docker-compose --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ Docker Compose no está instalado. Por favor instala Docker Compose primero.
    pause
    exit /b 1
)

echo ✅ Docker y Docker Compose están instalados.

REM Copiar archivo de entorno
if not exist .env (
    echo 📝 Copiando archivo de configuración de entorno...
    copy .env.docker .env
) else (
    echo ⚠️  El archivo .env ya existe. Si quieres usar la configuración de Docker, ejecuta: copy .env.docker .env
)

REM Construir e iniciar los contenedores
echo 🔨 Construyendo e iniciando contenedores Docker...
docker-compose up -d --build

REM Esperar a que los servicios estén listos
echo ⏳ Esperando a que los servicios estén listos...
timeout /t 30 /nobreak > nul

REM Instalar dependencias de Composer
echo 📦 Instalando dependencias de Composer...
docker-compose exec app composer install

REM Generar clave de aplicación
echo 🔑 Generando clave de aplicación...
docker-compose exec app php artisan key:generate

REM Ejecutar migraciones
echo 🗄️  Ejecutando migraciones de base de datos...
docker-compose exec app php artisan migrate

REM Crear enlace simbólico para storage
echo 🔗 Creando enlace simbólico para storage...
docker-compose exec app php artisan storage:link

REM Limpiar cache
echo 🧹 Limpiando cache...
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear

echo.
echo 🎉 ¡Configuración completada!
echo.
echo 📋 Información de acceso:
echo    🌐 Aplicación: http://localhost:8080
echo    🗄️  PHPMyAdmin: http://localhost:8081
echo    📊 Base de datos:
echo       - Host: localhost
echo       - Puerto: 3306
echo       - Base de datos: suelditas_db
echo       - Usuario: suelditas_user
echo       - Contraseña: suelditas_password
echo.
echo ⚡ Comandos útiles:
echo    - Detener contenedores: docker-compose down
echo    - Ver logs: docker-compose logs -f
echo    - Ejecutar comandos Artisan: docker-compose exec app php artisan [comando]
echo    - Acceder al contenedor: docker-compose exec app bash
echo.
pause