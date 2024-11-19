#!/bin/bash

set -e

# Clonar el repositorio si no está presente
if [ ! -d ".git" ]; then
    echo "Clonando el repositorio..."
    git clone https://github.com/tjhoan/projectDesarrollo.git .
fi

# Marcar el directorio como seguro para Git
git config --global --add safe.directory /var/www/html

# Instalar dependencias de Composer
echo "Instalando dependencias de Composer..."
composer install --no-dev --optimize-autoloader

# Configurar archivo .env si no existe
if [ ! -f ".env" ]; then
    echo "Configurando archivo .env..."
    cp .env.example .env
    sed -i "s/DB_DATABASE=.*/DB_DATABASE=laravel/" .env
    sed -i "s/DB_USERNAME=.*/DB_USERNAME=laravel/" .env
    sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=password/" .env
    php artisan key:generate
fi

# Ajustar permisos
echo "Configurando permisos..."
chown -R www-data:www-data /var/www/html
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

# Instalar dependencias de Node.js
echo "Instalando dependencias de Node.js..."
npm install --legacy-peer-deps

# Compilar recursos frontend
echo "Compilando recursos frontend..."
npm run dev

# Ejecutar migraciones y seeders
echo "Ejecutando migraciones y seeders..."
php artisan migrate --seed

# Iniciar Apache
echo "Iniciando Apache..."
apache2-foreground
