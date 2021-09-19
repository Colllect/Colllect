.PHONY: install
install: var/oauth2/private.key vendor assets

var/oauth2/private.key:
	mkdir -p var/oauth2
	openssl genrsa -out var/oauth2/private.key 2048

var/oauth2/public.key: var/oauth2/private.key
	openssl rsa -in var/oauth2/private.key -pubout -out var/oauth2/public.key

vendor: composer.json $(wildcard composer.lock)
	composer install
	# Create a file to force update the folder date
	@touch vendor/__make-colllect-back-update-date

.PHONY: assets
assets:
	bin/console assets:install

.PHONY: init-db
init-db:
	bin/console doctrine:database:create --if-not-exists
	# TODO: create all migrations needed
	# bin/console doctrine:migrations:migrate
	bin/console doctrine:schema:update --force

	# Create default OAuth client if not exists
	bin/console trikoder:oauth2:list-clients | grep -q default || bin/console trikoder:oauth2:create-client default "" --scope superadmin --grant-type client_credentials
