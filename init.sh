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

# Dependiendo del argumento, se selecciona el archivo de docker-compose y los contenedores
case $ENV in
  dev)
    COMPOSE_FILES="-f docker-compose.yml"
    APP_CONTAINER="laravel-dev"
    DB_CONTAINER="mysql-dev"
    DB_USER="laravel_user"
    DB_PASS="laravel_pass"
    ;;
  prod)
    COMPOSE_FILES="-f docker-compose.yml -f docker-compose.prod.yml"
    APP_CONTAINER="laravel-prod"
    DB_CONTAINER="mysql-prod"
    DB_USER="laravel_user"
    DB_PASS="laravel_pass"
    ;;
  test)
    COMPOSE_FILES="-f docker-compose.yml -f docker-compose.test.yml"
    APP_CONTAINER="laravel-test"
    DB_CONTAINER="mysql-test"
    DB_USER="test_user"
    DB_PASS="test_pass"
    ;;
  *)
    echo "Entorno no válido. Usa: dev, prod o test"
    exit 1
    ;;
esac

echo "========== Configurando variables de entorno para el entorno test =========="
if [ "$ENV" == "test" ]; then
  echo "APP_ENV=testing" > .env
  echo "APP_DEBUG=true" >> .env
  echo "DB_CONNECTION=mysql" >> .env
  echo "DB_HOST=db" >> .env
  echo "DB_PORT=3306" >> .env
  echo "DB_DATABASE=test_db" >> .env
  echo "DB_USERNAME=test_user" >> .env
  echo "DB_PASSWORD=test_pass" >> .env
fi

echo "========== Deteniendo y eliminando contenedores =========="
docker-compose $COMPOSE_FILES down -v || error_exit "No se pudieron detener los contenedores"

echo "========== Construyendo y levantando contenedores para el entorno $ENV =========="
docker-compose $COMPOSE_FILES up --build -d || error_exit "No se pudieron construir los contenedores"

echo "========== Esperando a que la base de datos esté lista =========="
until docker exec $DB_CONTAINER mysql -u $DB_USER -p$DB_PASS -e "SELECT 1;" > /dev/null 2>&1; do
  echo "Esperando a que MySQL esté disponible..."
  sleep 3
done
echo "Base de datos lista"

echo "========== Generando clave de cifrado para Laravel =========="
docker exec $APP_CONTAINER sh -c "grep APP_KEY /app/.env" > /dev/null 2>&1 || \
docker exec $APP_CONTAINER php artisan key:generate || \
error_exit "No se pudo generar la clave de cifrado"

if [ "$ENV" != "prod" ]; then
  echo "========== Ejecutando migraciones y seeders =========="
  docker exec $APP_CONTAINER php artisan migrate:fresh --seed || \
  error_exit "No se pudieron ejecutar las migraciones y seeders"
fi

echo "========== Proceso completado con éxito para el entorno $ENV =========="
