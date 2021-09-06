.PHONY: help
help:
	@printf "\033[33mUsage:\033[0m\n  make [target] [arg=\"val\"...]\n\n\033[33mTargets:\033[0m\n"
	@grep -E '^[-a-zA-Z0-9_\.\/]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[32m%-15s\033[0m %s\n", $$1, $$2}'

.PHONY: init
init: .env upd install back/var/oauth2/public.key init-db create-default-client down ## Initialize project dependencies, database, keys, etc.

.PHONY: build
build:
	docker-compose build --parallel

.PHONY: upd
upd: build
	docker-compose up -d

.PHONY: up
up: build ## Launch the app
	docker-compose up

.PHONY: down
down: ## Stop the app
	docker-compose down

.env:
	cp .env.dist .env

.PHONY: install
install: ## Install back and front dependencies
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
