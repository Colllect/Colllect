.PHONY: init
init: .env upd install back/var/oauth2/public.key init-db create-default-client down

.PHONY: upd
upd:
	docker-compose up -d

.PHONY: up
up:
	docker-compose up --build

.PHONY: down
down:
	docker-compose down

.env:
	cp .env.dist .env

.PHONY: install
install:
	docker-compose exec php composer install
	cd front && npm install

.PHONY: init-db
init-db: install
	docker-compose exec php bin/console doctrine:schema:create; true

.PHONY: create-default-client
create-default-client: init-db
	docker-compose exec php bin/console trikoder:oauth2:create-client default "" --scope superadmin --grant-type client_credentials; true

back/var/oauth2/private.key:
	docker-compose exec php mkdir -p var/oauth2
	docker-compose exec php openssl genrsa -out var/oauth2/private.key 2048

back/var/oauth2/public.key: back/var/oauth2/private.key
	docker-compose exec php openssl rsa -in var/oauth2/private.key -pubout -out var/oauth2/public.key
