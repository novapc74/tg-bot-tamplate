init: create_network docker-down docker-pull docker-build docker-up
prod: create_network docker-down prod-docker-pull docker-prod-up
update_certificates: certbot reload-nginx

create_network:
	@if [ -z "$$(docker network ls --filter name=tg-bot-network -q)" ]; then \
		docker network create tg-bot-network; \
	else \
		echo "Docker network tg-bot-network already exists, skipping creation."; \
	fi

create_shared_network:
	docker network create --driver bridge shared-network

docker-down:
	docker compose --env-file ./project/.env.local down --remove-orphans

docker-pull:
	docker compose --env-file ./project/.env.local pull

prod-docker-pull:
	docker compose -f docker-compose-prod.yml --env-file ./project/.env.local pull

docker-build:
	docker compose --env-file ./project/.env.local build --pull

prod-docker-build:
	docker compose -f docker-compose-prod.yml --env-file ./project/.env.local build --pull

docker-up:
	docker compose --env-file ./project/.env.local up -d

docker-prod-up:
	docker compose -f docker-compose-prod.yml --env-file ./project/.env.local up -d

php-cli-dev:
	docker compose --env-file ./project/.env.local run --rm php-cli bash

php-cli-prod:
	docker compose -f /var/www/tg-bot/docker-compose-prod.yml --env-file ./project/.env.local run --rm php-cli bash

dev-update:
	docker compose --env-file ./project/.env.local exec php-cli bash
	composer install
	bin/console d:m:m --no-inreraction

yarn-watch:
	docker compose --env-file ./project/.env.local run --rm node-cli yarn watch

yarn-dev:
	docker compose --env-file ./project/.env.local run --rm node-cli yarn encore dev

yarn-install:
	docker compose --env-file ./project/.env.local run --rm node-cli yarn install --force

yarn-add:
	docker compose --env-file ./project/.env.local run --rm node-cli yarn add ...

image-docker-build:
	docker --log-level=debug build --pull --file=docker/prod/nginx/Dockerfile --tag=ghcr.io/novapc74/repository/tg_nginx:master .
	docker --log-level=debug build --pull --file=docker/prod/php-cli/Dockerfile --tag=ghcr.io/novapc74/repository/tg_php-cli:master .
	docker --log-level=debug build --pull --file=docker/prod/php-fpm/Dockerfile --tag=ghcr.io/novapc74/repository/tg_php-fpm:master .
	docker --log-level=debug build --pull --file=docker/node-cli/Dockerfile --tag=ghcr.io/novapc74/repository/tg_node-cli:master .

github-login:
	docker login ghcr.io -u novapc74 -p GIT_USER_PASS # https://github.com/settings/tokens Token (classic)

image-docker-push:
	docker push ghcr.io/novapc74/repository/tg_nginx:master
	docker push ghcr.io/novapc74/repository/tg_php-cli:master
	docker push ghcr.io/novapc74/repository/tg_php-fpm:master
	docker push ghcr.io/novapc74/repository/tg_node-cli:master

# /etc/cron.d/certbot
certbot:
	certbot renew --quiet --config-dir /var/www/tg-bot/conf

reload-nginx:
	docker compose -f /var/www/tg-bot/docker-compose-prod.yml --env-file /var/www/tg-bot/project/.env.local exec nginx nginx -s stop

yarn-install-prod:
	cd project
	docker compose -f docker-compose-prod.yml --env-file ./project/.env.local run --rm node-cli yarn install

yarn-build:
	cd project
	docker compose -f docker-compose-prod.yml --env-file ./project/.env.local run --rm node-cli yarn build

yarn-add:
	cd project
	docker compose -f docker-compose-prod.yml --env-file ./project/.env.local run --rm node-cli yarn add vite @hotwired/stimulus