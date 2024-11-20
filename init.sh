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

echo "Iniciando contenedores..."
docker-compose up -d
