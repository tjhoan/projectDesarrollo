#!/bin/bash

# Función para manejar errores
error_exit() {
  echo "Error: $1"
  exit 1
}

echo "========== Verificando puertos libres =========="
for port in 8000 3306; do
  if lsof -i :$port >/dev/null; then
    echo "Error: El puerto $port está en uso. Libéralo antes de continuar."
    exit 1
  fi
done

echo "========== Deteniendo y eliminando contenedores existentes =========="
docker ps -aq | xargs docker rm -f || error_exit "No se pudieron detener los contenedores."

echo "========== Eliminando imágenes y volúmenes antiguos =========="
docker system prune -a --volumes -f || error_exit "No se pudieron eliminar imágenes y volúmenes."

echo "========== Limpiando directorios previos en el sistema local =========="
rm -rf ./storage/logs/*
rm -rf ./bootstrap/cache/*

echo "========== Construyendo y levantando contenedores =========="
docker-compose up --build -d || error_exit "No se pudieron construir los contenedores."

echo "========== Verificando que los contenedores estén corriendo =========="
docker ps | grep "laravel-app" > /dev/null || error_exit "El contenedor laravel-app no está corriendo."
docker ps | grep "laravel-db" > /dev/null || error_exit "El contenedor laravel-db no está corriendo."

echo "========== Esperando a que la base de datos esté lista =========="
until docker exec laravel-db mysql -ularavel_user -plaravel_pass -e "SELECT 1;" > /dev/null 2>&1; do
  echo "Esperando a que MySQL esté disponible..."
  sleep 5
done
echo "Base de datos lista."

echo "========== Instalando dependencias de Laravel =========="
docker exec laravel-app composer install || error_exit "No se pudieron instalar las dependencias de Composer."
docker exec laravel-app npm install --legacy-peer-deps || error_exit "No se pudieron instalar las dependencias de npm."
docker exec laravel-app npm run dev || echo "Advertencia: npm run dev falló, verifica los logs para más detalles."

echo "========== Generando clave de aplicación =========="
docker exec laravel-app php artisan key:generate || error_exit "No se pudo generar la clave de la aplicación."

echo "========== Ejecutando migraciones y seeders =========="
docker exec laravel-app php artisan migrate:fresh --seed || error_exit "No se pudieron ejecutar las migraciones."

echo "========== Verificando estado de Apache =========="
docker exec laravel-app service apache2 restart || error_exit "No se pudo reiniciar Apache en el contenedor laravel-app."

echo "========== Proceso completado con éxito =========="
