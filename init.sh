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

# Seleccionar el archivo docker-compose y nombres de contenedores según el entorno
case $ENV in
  dev)
    COMPOSE_FILES="-f docker-compose.yml"
    APP_CONTAINER="laravel-dev"
    DB_CONTAINER="mysql-dev"
    ;;
  prod)
    COMPOSE_FILES="-f docker-compose.yml -f docker-compose.prod.yml"
    APP_CONTAINER="laravel-prod"
    DB_CONTAINER="mysql-prod"
    ;;
  test)
    COMPOSE_FILES="-f docker-compose.yml -f docker-compose.test.yml"
    APP_CONTAINER="laravel-test"
    DB_CONTAINER="mysql-test"
    ;;
  *)
    echo "Entorno no válido. Usa: dev, prod o test"
    exit 1
    ;;
esac

echo "========== Deteniendo y eliminando contenedores existentes =========="
docker-compose $COMPOSE_FILES down -v || error_exit "No se pudieron detener los contenedores."

echo "========== Construyendo y levantando contenedores para el entorno $ENV =========="
docker-compose $COMPOSE_FILES up --build -d || error_exit "No se pudieron construir los contenedores."

echo "========== Verificando que los contenedores estén corriendo =========="
docker ps | grep "$APP_CONTAINER" > /dev/null || error_exit "El contenedor $APP_CONTAINER no está corriendo."
docker ps | grep "$DB_CONTAINER" > /dev/null || error_exit "El contenedor $DB_CONTAINER no está corriendo."

echo "========== Esperando a que la base de datos esté lista =========="
until docker exec $DB_CONTAINER mysql -u laravel_user -plaravel_pass -e "SELECT 1;" > /dev/null 2>&1; do
  echo "Esperando a que MySQL esté disponible..."
  sleep 5
done
echo "Base de datos lista."

if [ "$ENV" != "prod" ]; then
  echo "========== Ejecutando migraciones y seeders =========="
  docker exec $APP_CONTAINER php artisan migrate:fresh --seed || error_exit "No se pudieron ejecutar las migraciones y seeders."
fi

echo "========== Proceso completado con éxito para el entorno $ENV =========="
