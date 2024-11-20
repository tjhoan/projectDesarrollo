#!/bin/bash

echo "Deteniendo y eliminando contenedores existentes..."
docker-compose down -v

echo "Eliminando imágenes y volúmenes..."
docker system prune -a --volumes -f

echo "Construyendo contenedores..."
docker-compose up --build -d

echo "Instalando dependencias de Laravel..."
docker exec -it laravel-app composer install
docker exec -it laravel-app npm install --legacy-peer-deps
docker exec -it laravel-app npm run dev

echo "Generando clave de aplicación..."
docker exec -it laravel-app php artisan key:generate

echo "Configurando permisos..."
docker exec -it laravel-app chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
docker exec -it laravel-app chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

echo "Reiniciando contenedores..."
docker-compose restart
