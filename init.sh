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
ACTION=${2:-""} # Por defecto no elimina volúmenes

# Validar el segundo argumento si existe
if [[ "$ACTION" != "" && "$ACTION" != "-b" ]]; then
  echo "Acción no válida. Usa -b para borrar contenedores y volúmenes, o no uses ningún argumento para solo detener los contenedores."
  exit 1
fi

# Seleccionar el archivo docker-compose y nombres de contenedores según el entorno
case $ENV in
  dev)
    COMPOSE_FILES="-f docker-compose.yml"
    APP_CONTAINER="laravel-dev"
    DB_CONTAINER="mysql-dev"
    VOLUME="db_data_dev"
    ;;
  prod)
    COMPOSE_FILES="-f docker-compose.yml -f docker-compose.prod.yml"
    APP_CONTAINER="laravel-prod"
    DB_CONTAINER="mysql-prod"
    VOLUME="db_data_prod"
    ;;
  test)
    COMPOSE_FILES="-f docker-compose.yml -f docker-compose.test.yml"
    APP_CONTAINER="laravel-test"
    DB_CONTAINER="mysql-test"
    VOLUME="db_data_test"
    ;;
  *)
    echo "Entorno no válido. Usa: dev, prod o test"
    exit 1
    ;;
esac

# Manejar la acción según el segundo argumento
echo "========== Deteniendo contenedores existentes =========="
if [ "$ACTION" == "-b" ]; then
  echo "Eliminando contenedores, volúmenes y datos persistentes..."
  docker-compose $COMPOSE_FILES down -v || error_exit "No se pudieron detener y eliminar los contenedores."
  
  # Eliminar volúmenes no utilizados, en caso de que queden residuos
  docker volume rm $VOLUME > /dev/null 2>&1 || echo "El volumen $VOLUME ya ha sido eliminado o no existe."
else
  echo "Deteniendo contenedores sin eliminar volúmenes..."
  docker-compose $COMPOSE_FILES down || error_exit "No se pudieron detener los contenedores."
fi

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
