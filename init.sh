#!/bin/bash
set -e

# Configurar permisos para /var/www/html
echo "Configurando permisos en /var/www/html..."
chown -R root:root /var/www/html
chmod -R 775 /var/www/html
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

# Configurar Git (resuelve el error de permisos)
if [ ! -f "/var/www/.gitconfig" ]; then
    echo "Creando configuración de Git..."
    git config --global --add safe.directory /var/www/html
fi

# Clonar el repositorio si no está presente
if [ ! -d ".git" ]; then
    echo "Clonando el repositorio..."
    git clone https://github.com/tjhoan/projectDesarrollo.git .
fi

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

# Instalar dependencias de Node.js
echo "Instalando dependencias de Node.js..."
npm install --legacy-peer-deps

# Instalar dependencias adicionales para Laravel Mix
echo "Instalando dependencias adicionales de Laravel Mix..."
npm install browser-sync browser-sync-webpack-plugin@^2.3.0 --save-dev --legacy-peer-deps

# Compilar recursos frontend
echo "Compilando recursos frontend..."
npm run dev || npm run production

# Ejecutar migraciones y seeders
echo "Ejecutando migraciones y seeders..."
php artisan migrate --seed

# Iniciar Apache
echo "Iniciando Apache..."
apache2-foreground
