#!/bin/bash

# Función para manejar errores
error_exit() {
  echo "Error: $1"
  exit 1
}

echo "=== Deteniendo y eliminando contenedores existentes ==="
docker-compose down -v || error_exit "No se pudieron detener los contenedores."

echo "=== Eliminando imágenes y volúmenes antiguos ==="
docker system prune -a --volumes -f || error_exit "No se pudieron eliminar imágenes y volúmenes."

echo "=== Construyendo y levantando contenedores ==="
docker-compose up --build -d || error_exit "No se pudieron construir los contenedores."

echo "=== Verificando que los contenedores estén corriendo ==="
docker ps | grep "laravel-app" > /dev/null || error_exit "El contenedor laravel-app no está corriendo."
docker ps | grep "laravel-db" > /dev/null || error_exit "El contenedor laravel-db no está corriendo."

echo "=== Esperando a que la base de datos esté lista ==="
until docker exec laravel-db mysql -u laravel_user -plaravel_pass -e "SELECT 1;" > /dev/null 2>&1; do
  echo "Esperando a que MySQL esté disponible..."
  sleep 5
done
echo "Base de datos lista."

echo "=== Instalando dependencias de Laravel ==="
docker exec laravel-app composer install || error_exit "No se pudieron instalar las dependencias de Composer."
docker exec laravel-app npm install --legacy-peer-deps || error_exit "No se pudieron instalar las dependencias de npm."
docker exec laravel-app npm run dev || echo "Advertencia: npm run dev falló, verifica los logs para más detalles."

echo "=== Actualizando Node.js a la última versión ==="
docker exec laravel-app npm install -g npm@latest || error_exit "No se pudo actualizar npm."

echo "=== Generando clave de aplicación ==="
docker exec laravel-app php artisan key:generate || error_exit "No se pudo generar la clave de la aplicación."

echo "=== Ejecutando migraciones y seeders ==="
docker exec laravel-app php artisan migrate:fresh --seed || error_exit "No se pudieron ejecutar las migraciones."

echo "=== Proceso completado con éxito ==="
