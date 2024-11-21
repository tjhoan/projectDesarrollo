#!/bin/bash

# Función para manejar errores
error_exit() {
  echo "Error: $1"
  exit 1
}

echo "Deteniendo y eliminando contenedores existentes..."
docker-compose down -v || error_exit "No se pudieron detener los contenedores."

echo "Eliminando imágenes y volúmenes..."
docker system prune -a --volumes -f || error_exit "No se pudieron eliminar imágenes y volúmenes."

echo "Construyendo contenedores..."
docker-compose up --build -d || error_exit "No se pudieron construir los contenedores."

echo "Verificando que los contenedores se estén ejecutando..."
docker ps | grep "laravel-app" > /dev/null || error_exit "El contenedor laravel-app no está en ejecución."
docker ps | grep "laravel-db" > /dev/null || error_exit "El contenedor laravel-db no está en ejecución."

echo "Esperando a que el contenedor de la base de datos esté listo..."
until docker exec laravel-db mysqladmin ping -h "db" --silent; do
  echo "Esperando la base de datos..."
  sleep 2
done

echo "Instalando dependencias de Laravel..."
docker exec -it laravel-app composer install || error_exit "No se pudieron instalar las dependencias de Composer."
docker exec -it laravel-app npm install --legacy-peer-deps || error_exit "No se pudieron instalar las dependencias de npm."
docker exec -it laravel-app npm run dev || error_exit "No se pudo ejecutar npm run dev."

echo "Generando clave de aplicación..."
docker exec -it laravel-app php artisan key:generate || error_exit "No se pudo generar la clave de la aplicación."

echo "Ejecutando migraciones..."
docker exec -it laravel-app php artisan migrate || error_exit "No se pudieron ejecutar las migraciones."

echo "Configurando permisos..."
docker exec -it laravel-app chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache || error_exit "No se pudieron configurar los permisos."
docker exec -it laravel-app chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || error_exit "No se pudo cambiar la propiedad de los permisos."

echo "Configurando caché de configuración, rutas y vistas..."
docker exec -it laravel-app php artisan config:cache || echo "Advertencia: No se pudo configurar el caché de configuración."
docker exec -it laravel-app php artisan route:cache || echo "Advertencia: No se pudo configurar el caché de rutas."
docker exec -it laravel-app php artisan view:cache || echo "Advertencia: No se pudo configurar el caché de vistas."

echo "Reiniciando contenedores..."
docker-compose restart || error_exit "No se pudieron reiniciar los contenedores."

echo "Proceso completado con éxito."
