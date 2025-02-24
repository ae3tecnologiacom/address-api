#!/usr/bin/env bash
source $(dirname "$0")/_vars.sh
options=(`docker container ls --filter "label=php" --format "{{.Names}}"`)

echo -e $RED_COLOR"Bower Install..."$RESET_COLOR
docker compose -f ./docker-compose.yml exec php-fpm bower install -f

echo -e $RED_COLOR"Yarn Install..."$RESET_COLOR
docker compose -f ./docker-compose.yml exec php-fpm yarn install --non-interactive