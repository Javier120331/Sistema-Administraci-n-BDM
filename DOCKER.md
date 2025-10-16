# Suelditas Beta - Configuración Docker

Este documento explica cómo levantar el proyecto Suelditas Beta usando Docker.

## Prerrequisitos

1. **Docker Desktop**: Instala Docker Desktop desde [https://www.docker.com/products/docker-desktop](https://www.docker.com/products/docker-desktop)
2. **Git**: Para clonar el repositorio (si es necesario)

## Configuración rápida

### Opción 1: Usando el script automatizado (Recomendado)

**En Windows:**

```bash
./docker-setup.bat
```

**En Linux/Mac:**

```bash
chmod +x docker-setup.sh
./docker-setup.sh
```

### Opción 2: Configuración manual

1. **Copia el archivo de entorno:**

    ```bash
    copy .env.docker .env
    ```

2. **Construye e inicia los contenedores:**

    ```bash
    docker-compose up -d --build
    ```

3. **Instala las dependencias:**

    ```bash
    docker-compose exec app composer install
    ```

4. **Genera la clave de aplicación:**

    ```bash
    docker-compose exec app php artisan key:generate
    ```

5. **Ejecuta las migraciones:**

    ```bash
    docker-compose exec app php artisan migrate
    ```

6. **Crea el enlace simbólico para storage:**
    ```bash
    docker-compose exec app php artisan storage:link
    ```

## Acceso a la aplicación

-   **Aplicación web**: [http://localhost:8080](http://localhost:8080)
-   **PHPMyAdmin**: [http://localhost:8081](http://localhost:8081)

### Credenciales de la base de datos

-   **Host**: localhost (o `db` desde dentro de los contenedores)
-   **Puerto**: 3306
-   **Base de datos**: suelditas_db
-   **Usuario**: suelditas_user
-   **Contraseña**: suelditas_password
-   **Usuario root**: root
-   **Contraseña root**: root_password

## Servicios incluidos

-   **app**: Aplicación Laravel con PHP 7.4-FPM
-   **webserver**: Servidor web Nginx
-   **db**: Base de datos MySQL 8.0
-   **phpmyadmin**: Interfaz web para gestión de base de datos

## Comandos útiles

### Gestión de contenedores

```bash
# Iniciar contenedores
docker-compose up -d

# Detener contenedores
docker-compose down

# Ver logs
docker-compose logs -f

# Ver logs de un servicio específico
docker-compose logs -f app
```

### Comandos Laravel

```bash
# Ejecutar comandos Artisan
docker-compose exec app php artisan [comando]

# Ejemplos:
docker-compose exec app php artisan migrate
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:list
```

### Acceso a contenedores

```bash
# Acceder al contenedor de la aplicación
docker-compose exec app bash

# Acceder al contenedor de la base de datos
docker-compose exec db mysql -u suelditas_user -p suelditas_db
```

### Composer

```bash
# Instalar dependencias
docker-compose exec app composer install

# Actualizar dependencias
docker-compose exec app composer update

# Instalar nueva dependencia
docker-compose exec app composer require vendor/package
```

## Solución de problemas

### Error de permisos

Si encuentras errores de permisos, ejecuta:

```bash
docker-compose exec app chown -R www-data:www-data /var/www/storage
docker-compose exec app chown -R www-data:www-data /var/www/bootstrap/cache
```

### Limpiar todo y empezar de nuevo

```bash
# Detener y eliminar contenedores, redes e imágenes
docker-compose down --rmi all --volumes --remove-orphans

# Reconstruir todo
docker-compose up -d --build
```

### Ver el estado de los contenedores

```bash
docker-compose ps
```

### Verificar logs si algo no funciona

```bash
docker-compose logs -f app
docker-compose logs -f db
docker-compose logs -f webserver
```

## Estructura de archivos Docker

```
proyecto/
├── docker/
│   ├── nginx/
│   │   └── conf.d/
│   │       └── app.conf          # Configuración de Nginx
│   ├── php/
│   │   └── local.ini              # Configuración de PHP
│   └── mysql/
│       └── init/                  # Scripts de inicialización de MySQL
├── Dockerfile                     # Imagen de la aplicación
├── docker-compose.yml             # Configuración de servicios
├── .env.docker                    # Variables de entorno para Docker
├── .dockerignore                  # Archivos ignorados por Docker
├── docker-setup.sh               # Script de configuración para Linux/Mac
└── docker-setup.bat              # Script de configuración para Windows
```

## Notas adicionales

-   Los archivos del proyecto se sincronizan automáticamente entre tu máquina y el contenedor
-   Los cambios en el código se reflejan inmediatamente sin necesidad de reconstruir
-   La base de datos persiste entre reinicios de contenedores
-   El proyecto está configurado para Laravel 5.7 con PHP 7.4
