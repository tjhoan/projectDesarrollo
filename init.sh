#!/bin/bash

# Función para manejar errores
error_exit() {
  echo "Error: $1"
  exit 1
}

# Verificar que se pase un entorno como argumento
if [ -z "$1" ]; then
  echo "Por favor, especifica el entorno: dev, prod o test"
  exit 1
fi

ENV=$1

# Configurar variables según el entorno
case $ENV in
  dev)
    COMPOSE_FILES="-f docker-compose.yml"
    APP_CONTAINER="laravel-dev"
    DB_CONTAINER="mysql-dev"
    DB_DATABASE=laravel
    DB_USERNAME="laravel_user"
    DB_PASSWORD="laravel_pass"
    APP_ENV=dev
    APP_DEBUG=true
    APP_URL="http://localhost:8000"
    ;;

  prod)
    COMPOSE_FILES="-f docker-compose.prod.yml"
    APP_CONTAINER="laravel-prod"
    DB_CONTAINER="mysql-prod"
    DB_DATABASE=laravel
    DB_USERNAME="laravel_user"
    DB_PASSWORD="laravel_pass"
    APP_ENV=production
    APP_DEBUG=false
    APP_URL="http://localhost"
    ;;

  test)
    COMPOSE_FILES="-f docker-compose.yml -f docker-compose.test.yml"
    APP_CONTAINER="laravel-test"
    DB_CONTAINER="mysql-test"
    DB_DATABASE=test_db
    DB_USERNAME="test_user"
    DB_PASSWORD="test_pass"
    APP_ENV=test
    APP_DEBUG=true
    APP_URL="http://localhost:8001"
    ;;

  *)
    echo "Entorno no válido. Usa: dev, prod o test"
    exit 1
    ;;
esac

echo "========== Configurando variables de entorno para el entorno $ENV =========="

# Crear o sobrescribir el archivo .env y ajustar permisos con sudo
sudo bash -c "cat > .env <<EOL
APP_ENV=$APP_ENV
APP_KEY=
APP_DEBUG=$APP_DEBUG
APP_URL=$APP_URL

LOG_CHANNEL=stack

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=$DB_DATABASE
DB_USERNAME=$DB_USERNAME
DB_PASSWORD=$DB_PASSWORD

BROADCAST_DRIVER=log
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120
EOL"

# Cambiar la propiedad del archivo .env al usuario que ejecuta el script
sudo chown ubuntu:ubuntu .env  # Cambia "ubuntu" por el nombre del usuario adecuado
chmod 644 .env  # Asegura que el archivo sea legible para todos, pero solo escribible para el propietario

echo "========== Eliminando contenedores y volúmenes específicos del proyecto =========="
sudo docker-compose $COMPOSE_FILES down -v
sudo docker system prune -a --volumes -f || error_exit "No se pudieron detener y eliminar los contenedores y volúmenes"

echo "========== Construyendo y levantando contenedores para el entorno $ENV =========="
sudo docker-compose $COMPOSE_FILES up --build -d || error_exit "No se pudieron construir los contenedores"

echo "========== Esperando a que la base de datos esté lista =========="
until sudo docker exec $DB_CONTAINER mysql -u $DB_USERNAME -p$DB_PASSWORD -e "SELECT 1;" > /dev/null 2>&1; do
  echo "Esperando a que MySQL esté disponible..."
  sleep 3
done
echo "Base de datos lista"

if [ "$ENV" = "prod" ]; then
  echo "========== Instalando dependencias de Composer (producción) =========="
  sudo docker exec $APP_CONTAINER composer install --no-dev --optimize-autoloader || error_exit "No se pudieron instalar las dependencias de Composer en producción"
else
  echo "========== Instalando dependencias de Composer =========="
  sudo docker exec $APP_CONTAINER composer install || error_exit "No se pudieron instalar las dependencias de Composer"
fi

echo "========== Generando clave de cifrado para Laravel =========="
sudo docker exec $APP_CONTAINER php artisan config:clear || error_exit "No se pudo limpiar la caché de configuración"
sudo docker exec $APP_CONTAINER php artisan key:generate || error_exit "No se pudo generar la clave de cifrado"

echo "========== Ejecutando migraciones y seeders =========="
sudo docker exec $APP_CONTAINER php artisan migrate:fresh --seed --force || error_exit "No se pudieron ejecutar las migraciones y seeders"

echo "========== Ejecutar comandos para limpieza de cache =========="
sudo docker exec $APP_CONTAINER php artisan cache:clear || error_exit "No se pudo limpiar la caché"
sudo docker exec $APP_CONTAINER php artisan route:clear || error_exit "No se pudo limpiar la caché de rutas"
sudo docker exec $APP_CONTAINER php artisan view:clear || error_exit "No se pudo limpiar la caché de vistas"
sudo docker exec $APP_CONTAINER php artisan config:clear || error_exit "No se pudo limpiar la caché de configuración"

echo "========== Proceso completado con éxito para el entorno $ENV - puerto $APP_URL =========="
