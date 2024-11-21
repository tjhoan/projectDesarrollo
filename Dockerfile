# Usa una imagen base con PHP y Apache
FROM php:7.4-apache

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
    git \
    curl \
    npm \
    nano \
    iputils-ping \
    && docker-php-ext-install pdo_mysql zip pcntl

# Habilita mod_rewrite para Apache
RUN a2enmod rewrite

# Configura permisos iniciales y agrega ServerName a Apache
RUN echo 'ServerName localhost' >> /etc/apache2/apache2.conf

# Copia el archivo .env al contenedor
COPY .env /var/www/html/.env

# Clona el repositorio de Laravel dentro del contenedor
RUN git clone https://github.com/tjhoan/projectDesarrollo.git /var/www/html

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
    && apt-get install -y nodejs \
    && npm install -g npm@latest

# Comando de inicio
CMD ["apache2-foreground"]
