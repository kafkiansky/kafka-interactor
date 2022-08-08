build:
	docker-compose build
up:
	docker-compose up -d
down:
	docker-compose down --remove-orphans
php:
	docker-compose exec php bash
logs:
	docker-compose logs -f php
