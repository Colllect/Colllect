.DEFAULT_GOAL := help

PWD := $(dir $(abspath $(lastword $(MAKEFILE_LIST))))

# SSL
SSL_CERT := $(PWD).docker/.ssl/dev-colllect-io.cert.pem
SSL_KEY := $(PWD).docker/.ssl/dev-colllect-io.key.pem
SSL_KEYS := $(SSL_CERT) $(SSL_KEY)
OPENSSL_CNF := $(PWD).docker/.ssl/openssl.cnf

.PHONY: help
help:
	@printf "\033[33mUsage:\033[0m\n  make [target] [arg=\"val\"...]\n\n\033[33mTargets:\033[0m\n"
	@grep -E '^[-a-zA-Z0-9_\.\/]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[32m%-15s\033[0m %s\n", $$1, $$2}'

.PHONY: init
init: .env ssl-renew upd down ## Initialize project

.PHONY: ssl-renew
ssl-renew: $(SSL_KEYS)

$(SSL_KEYS): $(OPENSSL_CNF)
	openssl req -x509 -sha256 -newkey rsa:4096 -nodes -days 365 \
		-subj '/CN=dev.colllect.io' \
 		-config $(OPENSSL_CNF) \
		-keyout $(SSL_KEY) \
		-out $(SSL_CERT)

.PHONY: build
build:
	docker-compose build --parallel

.PHONY: upd
upd: build
	docker-compose up --remove-orphans -d

.PHONY: up
up: build ## Launch the app
	docker-compose up --remove-orphans

.PHONY: stop
stop: ## Stop the app
	docker-compose stop

.PHONY: down
down: ## Down the app
	docker-compose down

.PHONY: reset
reset: down ## Remove all networks, images and volumes
	@docker network prune -f
	@docker rmi -f $(docker images -qa) 2>/dev/null || true
	@docker volume rm $(docker volume ls -q) 2>/dev/null || true
	@echo Done

.env:
	cp .env.dist .env
