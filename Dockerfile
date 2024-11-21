# Usa una imagen base con PHP-FPM
FROM php:8.1-fpm

# Instala dependencias necesarias
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxslt-dev \
    locales \
    zip \
    libzip-dev \
    unzip \
    pcntl \
    git \
    curl \
    npm \
    nano \
    make \
    && docker-php-ext-install pdo_mysql zip

# Copia el código de Laravel al contenedor
COPY . /var/www/html

# Configura permisos para los directorios storage y bootstrap/cache
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Define el directorio de trabajo
WORKDIR /var/www/html

# Instala Composer
COPY --from=composer:2.5 /usr/bin/composer /usr/bin/composer

# Instala Node.js y npm
RUN curl -sL https://deb.nodesource.com/setup_16.x | bash - \
    && apt-get install -y nodejs

# Ejecuta npm con la bandera --legacy-peer-deps
RUN npm install --legacy-peer-deps

# Puerto de PHP-FPM
EXPOSE 9000

# Comando de inicio
CMD ["php-fpm"]
