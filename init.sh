#!/bin/bash

# Función para manejar errores
error_exit() {
  echo "Error: $1"
  exit 1
}

echo "========== Deteniendo y eliminando contenedores existentes =========="
docker-compose down -v || error_exit "No se pudieron detener los contenedores."

echo "========== Construyendo y levantando contenedores =========="
docker-compose up --build -d || error_exit "No se pudieron construir los contenedores."

echo "========== Verificando que los contenedores estén corriendo =========="
docker ps | grep "laravel-container" > /dev/null || error_exit "El contenedor laravel-container no está corriendo."
docker ps | grep "mysql-container" > /dev/null || error_exit "El contenedor mysql-container no está corriendo."

echo "========== Esperando a que la base de datos esté lista =========="
until docker exec mysql-container mysql -u laravel_user -plaravel_pass -e "SELECT 1;" > /dev/null 2>&1; do
  echo "Esperando a que MySQL esté disponible..."
  sleep 5
done
echo "Base de datos lista."

echo "========== Ejecutando migraciones y seeders =========="
docker exec laravel-container php artisan migrate:fresh --seed || error_exit "No se pudieron ejecutar las migraciones y seeders."

echo "========== Proceso completado con éxito =========="
