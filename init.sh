#!/bin/bash

# Función para manejar errores
error_exit() {
  echo "Error: $1"
  exit 1
}

echo "========== Configurando archivo de entorno =========="
if [ -f .env.docker ]; then
  mv .env.docker .env || error_exit "No se pudo renombrar .env.docker a .env"
fi

cat > .env <<EOL
APP_NAME=Laravel
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

USER_ID=1000
GROUP_ID=1000

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DRIVER=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120
EOL

echo "Archivo .env configurado correctamente."

echo "========== Eliminando contenedores y volúmenes específicos del proyecto =========="
sudo docker-compose down -v
sudo docker system prune -a --volumes -f || error_exit "No se pudieron detener y eliminar los contenedores y volúmenes"

echo "========== Construyendo y levantando contenedores =========="
sudo docker-compose up --build -d || error_exit "No se pudieron construir los contenedores"

echo "========== Esperando a que la base de datos esté lista =========="
until sudo docker exec mysql mysql -u laravel_user -plaravel_pass -e "SELECT 1;" > /dev/null 2>&1; do
  echo "Esperando a que MySQL esté disponible..."
  sleep 3
done
echo "Base de datos lista"

echo "========== Instalando dependencias de Composer =========="
sudo docker exec laravel composer install --no-dev --optimize-autoloader || error_exit "No se pudieron instalar las dependencias de Composer"

echo "========== Generando clave de cifrado para Laravel =========="
sudo docker exec laravel php artisan key:generate || error_exit "No se pudo generar la clave de cifrado"

echo "========== Ejecutando migraciones y seeders =========="
sudo docker exec laravel php artisan migrate:fresh --seed --force || error_exit "No se pudieron ejecutar las migraciones y seeders"

echo "========== Ejecutar comandos para limpieza de cache =========="
sudo docker exec laravel php artisan cache:clear || error_exit "No se pudo limpiar la caché"
sudo docker exec laravel php artisan route:clear || error_exit "No se pudo limpiar la caché de rutas"
sudo docker exec laravel php artisan view:clear || error_exit "No se pudo limpiar la caché de vistas"
sudo docker exec laravel php artisan config:clear || error_exit "No se pudo limpiar la caché de configuración"

echo "========== Proceso completado con éxito =========="