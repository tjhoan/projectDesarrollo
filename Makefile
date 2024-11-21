up:
	docker-compose up --build -d

down:
	docker-compose down -v

build:
	docker-compose up --build -d

volumes:
	docker system prune -a --volumes -f

restart:
	docker-compose down -v && docker-compose up --build  -d

init:
	docker-compose down -v && docker system prune -a --volumes -f && docker-compose up --build -d

install:
	docker exec -it laravel-app composer install
	docker exec -it laravel-app npm install --legacy-peer-deps
	docker exec -it laravel-app npm run dev
	docker exec -it laravel-app php artisan key:generate
	docker exec -it laravel-app chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
	docker exec -it laravel-app chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
