# Usa una imagen base con PHP y Apache
FROM php:8.1-apache

# Instala dependencias necesarias
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    locales \
    zip \
    libzip-dev \
    unzip \
    git \
    curl \
    npm \
    nano \
    make \
    && docker-php-ext-install pdo_mysql zip

# Habilita mod_rewrite para Apache
RUN a2enmod rewrite

# Configura permisos iniciales y agrega ServerName a Apache
RUN echo 'ServerName localhost' >> /etc/apache2/apache2.conf

# Copia la configuración personalizada de Apache
COPY ./apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# Habilita el sitio y reinicia Apache
RUN a2ensite 000-default.conf

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

# Instala Node.js y npm (esencial para npm run dev)
RUN curl -sL https://deb.nodesource.com/setup_16.x | bash - \
    && apt-get install -y nodejs

# Ejecuta npm con la bandera --legacy-peer-deps
RUN npm install --legacy-peer-deps

# Comando de inicio para Apache
CMD ["apache2-foreground"]
