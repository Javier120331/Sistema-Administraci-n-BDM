FROM php:7.3-fpm AS base

# Variables de entorno para Composer
ENV COMPOSER_MEMORY_LIMIT=-1 \
    APP_ENV=local

# Dependencias del sistema necesarias
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    zip \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    g++ \
    nano \
    && docker-php-ext-configure gd --with-gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) pdo_mysql mbstring exif pcntl bcmath gd zip intl \
    && rm -rf /var/lib/apt/lists/*

# Opcache para rendimiento (opcional)
RUN docker-php-ext-enable opcache || true

# Instalar Composer 1.x (compatibilidad paquetes antiguos)
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
 && php composer-setup.php --version=1.10.26 --install-dir=/usr/local/bin --filename=composer \
 && php -r "unlink('composer-setup.php');"

WORKDIR /var/www

# Copiar solo archivos de Composer para aprovechar cache de capas
COPY composer.json composer.lock* ./

# Crear usuario no root
RUN groupadd -g 1000 www && useradd -u 1000 -ms /bin/bash -g www www

# Instalar dependencias de PHP (sin código aún)
RUN composer install --no-scripts --no-autoloader --prefer-dist --no-dev || true

# Copiar el resto del código (con ownership correcto)
COPY --chown=www:www . /var/www

# Ejecutar install completo ahora que está el código
RUN composer install --prefer-dist --no-dev \
    && composer dump-autoload -o

# Permisos para Laravel
RUN chown -R www:www storage bootstrap/cache \
    && chmod -R ug+rwx storage bootstrap/cache

USER www

EXPOSE 9000
CMD ["php-fpm"]