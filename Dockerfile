# Dockerfile

FROM php:7.4-fpm-alpine

# Instalar dependencias necesarias
RUN apk add --no-cache \
    bash \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libxml2-dev \
    oniguruma-dev \
    autoconf \
    gcc \
    g++ \
    make \
    icu-dev \
    libzip-dev \
    linux-headers

# Configurar extensiones de PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    gd \
    intl \
    zip \
    bcmath \
    pcntl \
    soap \
    sockets

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

# Crear directorio de trabajo
WORKDIR /app

# Copiar archivos del proyecto al contenedor
COPY . .

# Instalar dependencias de Laravel
RUN composer install && \
    mkdir -p /app/storage/logs && \
    chmod -R 775 /app/storage && \
    chmod -R 775 /app/bootstrap/cache

# Exponer puerto para el servidor
EXPOSE 8000

# Comando predeterminado para iniciar Laravel
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]