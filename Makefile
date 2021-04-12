SHELL := /bin/bash

tests: tests-back tests-e2e

tests-back: export APP_ENV=test
tests-back:
	symfony console doctrine:database:drop --force || true
	symfony console doctrine:database:create
	symfony console doctrine:migrations:migrate -n
	symfony console doctrine:fixtures:load -n
	symfony php bin/phpunit --testdox $@
.PHONY: tests tests-e2e tests-front-e2e-live

tests-e2e: export APP_ENV=test
tests-e2e:
	docker-compose up -d
	symfony console doctrine:database:drop --force || true
	symfony console doctrine:database:create
	symfony console doctrine:migrations:migrate -n
	symfony console doctrine:fixtures:load -n
	symfony server:start &
	cd front && yarn test:e2e --headless
	symfony server:stop
	docker-compose down

tests-e2e-live:
	cd front && yarn test:e2e

build:
	cd front && yarn build
