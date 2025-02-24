include .env

define-environment:
	@echo "Using ${DOCKER_COMPOSE_FILE} file"

SERVICE_NAMES := $(shell grep -v '^#' .env | sed '/^[[:space:]]*$$/d' | grep '^DOCKER_SERVICE_' | cut -d '=' -f 2- | tr '\n' ' ')
build: ## Executa o build de todos os containers listados no docker-compose
	make define-environment
	@docker compose -f ${DOCKER_COMPOSE_FILE} build $(SERVICE_NAMES) --no-cache


up: ## Executa o start de todos os containers listados no docker-compose.yml e definidos no .env
	@docker compose -f ${DOCKER_COMPOSE_FILE} up -d $(SERVICE_NAMES)

down: ## Encerra todos os containers listados no docker-compose
	make define-environment
	@docker compose -f ${DOCKER_COMPOSE_FILE} down

key-generate: ## Executa o composer install
	make define-environment
	docker compose -f ${DOCKER_COMPOSE_FILE} exec php-fpm php artisan key:generate

refresh:
	make composer-dump
	make clear

lib ?=
composer-require: ## Instala uma nova lib utilizando o composer
ifdef lib
	@docker compose -f ${DOCKER_COMPOSE_FILE} exec php-fpm composer require $(lib)
else
	echo "Informe o nome da lib a ser instalada"
endif

composer-install: ## Executa o composer install
	make define-environment
	@docker compose -f ${DOCKER_COMPOSE_FILE} exec php-fpm composer install --no-interaction

composer-update: ## Executa o composer update
	make define-environment
	@docker compose -f ${DOCKER_COMPOSE_FILE} exec php-fpm composer update --no-interaction

composer-dump: ## Executa o composer dump
	make define-environment
	@docker compose -f ${DOCKER_COMPOSE_FILE} exec php-fpm composer dump --no-interaction

node-install: ## Executa bower e yarn install
	make define-environment
	@docker compose -f ${DOCKER_COMPOSE_FILE} exec php-fpm npm install --non-interactive

delete-node_modules: ## Remove a pasta node_modules
	make define-environment
	docker compose -f ${DOCKER_COMPOSE_FILE} exec php-fpm rm -Rf node_modules

delete-vendor: ## Remove a pasta node_modules
	make define-environment
	docker compose -f ${DOCKER_COMPOSE_FILE} exec php-fpm rm -Rf vendor

env ?=
node-run: ## Roda o comando npm run
ifdef env
	echo "Rodando yarn run no modo $(env)"
	@docker compose -f ${DOCKER_COMPOSE_FILE} exec php-fpm npm run $(env)
else
	echo "Rodando yarn run no modo prod"
	@docker compose -f ${DOCKER_COMPOSE_FILE} exec php-fpm npm run prod
endif

migrate: ## Executa o comando php artisan migrate --force
	make define-environment
	@docker compose -f ${DOCKER_COMPOSE_FILE} exec php-fpm php artisan migrate --force

migrate-fresh: ## Executa o comando php artisan migrate --force
	make define-environment
	@docker compose -f ${DOCKER_COMPOSE_FILE} exec php-fpm php artisan migrate:fresh

clear: ## Executa o comando php artisan migrate --force
	make define-environment
	@docker compose -f ${DOCKER_COMPOSE_FILE} exec php-fpm php artisan optimize:clear

db-seed: ## Executa o comando php artisan db:seed
	make define-environment
	@docker compose -f ${DOCKER_COMPOSE_FILE} exec php-fpm php artisan db:seed

## Exemplo: make assign-password password=12345678
assign-password:
	make define-environment
	@docker compose -f ${DOCKER_COMPOSE_FILE} exec php-fpm php artisan password:assign --password=$(password)

## Exemplo: make recreate-database
recreate-database: ## Restaura um backup do banco de dados
	make define-environment
	@docker compose -f ${DOCKER_COMPOSE_FILE} exec pgsql psql -U "${DB_USERNAME}" -d "postgres" -c "SELECT pg_terminate_backend(pg_stat_activity.pid) FROM pg_stat_activity WHERE pg_stat_activity.datname = '${DB_DATABASE}';"
	@docker compose -f ${DOCKER_COMPOSE_FILE} exec pgsql psql -U "${DB_USERNAME}" -d "postgres" -c "DROP DATABASE IF EXISTS ${DB_DATABASE};"
	@docker compose -f ${DOCKER_COMPOSE_FILE} exec pgsql psql -U "${DB_USERNAME}" -d "postgres" -c "CREATE DATABASE ${DB_DATABASE};"

## Exemplo: make recreate-testing-database
recreate-testing-database: ## Restaura um backup do banco de dados
	make define-environment
	@docker compose -f ${DOCKER_COMPOSE_FILE} exec pgsql psql -U "${DB_USERNAME}" -c "DROP DATABASE IF EXISTS ${DB_DATABASE}_testing;"
	@docker compose -f ${DOCKER_COMPOSE_FILE} exec pgsql psql -U "${DB_USERNAME}" -c "CREATE DATABASE ${DB_DATABASE}_testing;"

## Exemplo: make restore filename=siagres.sql
restore: ## Restaura um backup do banco de dados
	make define-environment
	@docker compose -f ${DOCKER_COMPOSE_FILE} exec pgsql psql -U "${DB_USERNAME}" -d "${DB_DATABASE}" -c "CREATE EXTENSION IF NOT EXISTS postgis;"
	@docker compose -f ${DOCKER_COMPOSE_FILE} exec pgsql psql -U "${DB_USERNAME}" -d "${DB_DATABASE}" -f "/home/backups/$(filename)"

in: ## Lista todos os containers levantados para o usuário escolher um e entrar
	@bash .docker/scripts/in.sh

group ?=
filter ?=

certbot-run: ## Solicita a primeira instalação do certificado digital utilizando certbot
	@docker compose -f ${DOCKER_COMPOSE_FILE} exec nginx certbot run --nginx --agree-tos --no-eff-email -m ${CERTBOT_EMAIL_ADDRESS} -d ${CERTBOT_DOMAIN}

certbot-renew: ## Solicita a renovação do certificado digital utilizando certbot
	@docker compose -f ${DOCKER_COMPOSE_FILE} exec nginx certbot renew

passport-install: ## Faz install do passport
	make define-environment
	docker compose -f ${DOCKER_COMPOSE_FILE} exec php-fpm php artisan passport:install

openapi: ## Faz o build do arquivo do swagger
	make define-environment
	echo "Atualizando a documentação do swagger.."
	docker compose -f ${DOCKER_COMPOSE_FILE} exec php-fpm php artisan openapi:build

restart: ## Restarta todos os containers em execução
	make define-environment
	make down
	make up
	make refresh

test:
	make define-environment
	@docker compose -f ${DOCKER_COMPOSE_FILE} exec php-fpm php artisan test

install: ## Instala a aplicação executando todos os passos necessários
	echo "SIABES install"
	make build
	make up
	make delete-node_modules
	make delete-vendor
	make composer-install
	make key-generate
	echo "Install finished!"
