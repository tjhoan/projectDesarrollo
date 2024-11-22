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
    nano \
    iputils-ping \
    net-tools \
    iproute2 \
    && docker-php-ext-install pdo_mysql zip pcntl

# Habilita mod_rewrite para Apache
RUN a2enmod rewrite

# Configura permisos iniciales y agrega ServerName a Apache
RUN echo 'ServerName localhost' >> /etc/apache2/apache2.conf

# Agrega el VirtualHost predeterminado
COPY ./apache/000-default.conf /etc/apache2/sites-available/000-default.conf
RUN a2ensite 000-default.conf

# Configura permisos iniciales
RUN mkdir -p /var/www/html \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html

# Define el directorio de trabajo
WORKDIR /var/www/html

# Instala Composer
COPY --from=composer:2.5 /usr/bin/composer /usr/bin/composer

# Instala Node.js (versión 20.x) y npm
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm@latest

# Exponer el puerto 8000 en lugar del 80
EXPOSE 8000

# Comando de inicio que asegura la ejecución de servicios requeridos
CMD ["apache2-foreground"]
