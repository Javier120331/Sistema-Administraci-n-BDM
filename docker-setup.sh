#!/bin/bash

echo "🚀 Iniciando configuración del proyecto Suelditas Beta con Docker..."

# Verificar si Docker está instalado
if ! command -v docker &> /dev/null; then
    echo "❌ Docker no está instalado. Por favor instala Docker Desktop primero."
    exit 1
fi

if ! command -v docker-compose &> /dev/null; then
    echo "❌ Docker Compose no está instalado. Por favor instala Docker Compose primero."
    exit 1
fi

echo "✅ Docker y Docker Compose están instalados."

# Copiar archivo de entorno
if [ ! -f .env ]; then
    echo "📝 Copiando archivo de configuración de entorno..."
    cp .env.docker .env
else
    echo "⚠️  El archivo .env ya existe. Si quieres usar la configuración de Docker, ejecuta: cp .env.docker .env"
fi

# Construir e iniciar los contenedores
echo "🔨 Construyendo e iniciando contenedores Docker..."
docker-compose up -d --build

# Esperar a que los servicios estén listos
echo "⏳ Esperando a que los servicios estén listos..."
sleep 30

# Instalar dependencias de Composer
echo "📦 Instalando dependencias de Composer..."
docker-compose exec app composer install

# Generar clave de aplicación
echo "🔑 Generando clave de aplicación..."
docker-compose exec app php artisan key:generate

# Ejecutar migraciones
echo "🗄️  Ejecutando migraciones de base de datos..."
docker-compose exec app php artisan migrate

# Crear enlace simbólico para storage
echo "🔗 Creando enlace simbólico para storage..."
docker-compose exec app php artisan storage:link

# Limpiar cache
echo "🧹 Limpiando cache..."
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear

echo ""
echo "🎉 ¡Configuración completada!"
echo ""
echo "📋 Información de acceso:"
echo "   🌐 Aplicación: http://localhost:8080"
echo "   🗄️  PHPMyAdmin: http://localhost:8081"
echo "   📊 Base de datos:"
echo "      - Host: localhost"
echo "      - Puerto: 3306"
echo "      - Base de datos: suelditas_db"
echo "      - Usuario: suelditas_user"
echo "      - Contraseña: suelditas_password"
echo ""
echo "⚡ Comandos útiles:"
echo "   - Detener contenedores: docker-compose down"
echo "   - Ver logs: docker-compose logs -f"
echo "   - Ejecutar comandos Artisan: docker-compose exec app php artisan [comando]"
echo "   - Acceder al contenedor: docker-compose exec app bash"
echo ""