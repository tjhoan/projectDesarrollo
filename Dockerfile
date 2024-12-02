# Usar la imagen base de PHP con FPM
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

# Configurar las extensiones de PHP necesarias
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

# Instalar las dependencias de Laravel
RUN composer install --no-dev --optimize-autoloader

# Establecer permisos adecuados en las carpetas de Laravel
RUN mkdir -p /app/storage/logs /app/storage/framework/sessions /app/storage/framework/views /app/storage/framework/cache && \
    chmod -R 775 /app/storage /app/bootstrap/cache

# Configurar el entorno de producción (especificar APP_ENV)
RUN cp .env.example .env && \
    sed -i 's/APP_ENV=local/APP_ENV=production/' .env

# Exponer el puerto 9000 para el contenedor PHP-FPM
EXPOSE 9000

# Iniciar PHP-FPM
CMD ["php-fpm"]
