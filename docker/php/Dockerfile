# ./docker/php/Dockerfile
FROM php:8.3-fpm-alpine

# Instalar dependencias del sistema
RUN apk add --no-cache \
    sqlite \
    git \
    zip \
    unzip \
    libzip-dev \
    $PHPIZE_DEPS

# Instalar y habilitar extensiones PHP
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    zip

# Configurar PHP
COPY ./docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini

# Crear directorio para la base de datos
RUN mkdir -p /var/www/html/database \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

WORKDIR /var/www/html