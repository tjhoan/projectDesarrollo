up:
	docker-compose up -d

down:
	docker-compose down -v

build:
	docker-compose up --build

init:
	docker-compose down -v && docker system prune -a --volumes -f && docker-compose up --build

install:
	docker exec -it laravel-app composer install
	docker exec -it laravel-app npm install --legacy-peer-deps
	docker exec -it laravel-app npm run dev
