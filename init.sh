#!/bin/bash

# Función para manejar errores
error_exit() {
  echo "Error: $1"
  exit 1
}

echo "========== Cambiar la propiedad del archivo .env al usuario que ejecuta el script =========="
sudo chown -R $(whoami):$(whoami) .
chmod -R 755 .

sudo chmod -R 777 /var/www/html/projectdesarrollo
sudo chown -R ubuntu:ubuntu /var/www/html/projectdesarrollo

echo "========== Eliminando contenedores y volúmenes específicos del proyecto =========="
sudo docker-compose docker-compose.yml down -v
sudo docker system prune -a --volumes -f || error_exit "No se pudieron detener y eliminar los contenedores y volúmenes"

echo "========== Construyendo y levantando contenedores =========="
sudo docker-compose docker-compose.yml up --build -d || error_exit "No se pudieron construir los contenedores"

echo "========== Esperando a que la base de datos esté lista =========="
until sudo docker exec mysql-prod mysql -u laravel_user -plaravel_pass -e "SELECT 1;" > /dev/null 2>&1; do
  echo "Esperando a que MySQL esté disponible..."
  sleep 3
done
echo "Base de datos lista"

echo "========== Instalando dependencias de Composer =========="
sudo docker exec laravel-prod composer install --no-dev --optimize-autoloader || error_exit "No se pudieron instalar las dependencias de Composer"

echo "========== Generando clave de cifrado para Laravel =========="
sudo docker exec laravel-prod php artisan key:generate || error_exit "No se pudo generar la clave de cifrado"

echo "========== Ejecutando migraciones y seeders =========="
sudo docker exec laravel-prod php artisan migrate:fresh --seed --force || error_exit "No se pudieron ejecutar las migraciones y seeders"

echo "========== Ejecutar comandos para limpieza de cache =========="
sudo docker exec laravel-prod php artisan cache:clear || error_exit "No se pudo limpiar la caché"
sudo docker exec laravel-prod php artisan route:clear || error_exit "No se pudo limpiar la caché de rutas"
sudo docker exec laravel-prod php artisan view:clear || error_exit "No se pudo limpiar la caché de vistas"
sudo docker exec laravel-prod php artisan config:clear || error_exit "No se pudo limpiar la caché de configuración"

echo "========== Proceso completado con éxito =========="
