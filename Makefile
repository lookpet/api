NAMESPACE		=	641201314091.dkr.ecr.eu-central-1.amazonaws.com
APP_ROOT		=	/app
WORKING_DIR		=	$(CURDIR)
PULL_IF_EXIST  ?= 	false
AWS_REGION = eu-central-1

BASE_PHP_IMAGE  =   641201314091.dkr.ecr.eu-central-1.amazonaws.com/php-base:cb5191475d444cb21e7b45a321c6edf61ad1ccbf

# BUILD CONFIG
TARGET_DIR                     ?= target

# PHPUNIT CONFIG
COVERAGE_XML_PATH       		= $(TARGET_DIR)/logs/coverage/xml/
CLOVER_XML_PATH                 = $(TARGET_DIR)/logs/coverage/clover.xml
CLOVER_HTML_PATH                = $(TARGET_DIR)/logs/coverage/html/
JUNIT_LOCATION_PATH             = $(TARGET_DIR)/logs/junit
JUNIT_FILE                     ?= junit.xml
JUNIT_FILE_PATH                 = $(JUNIT_LOCATION_PATH)/$(JUNIT_FILE)


# Use modern docker features
SSH_KEY_FILE   ?= $(HOME)/.ssh/id_rsa
export DOCKER_BUILDKIT := 1

export VERSION := $(shell git describe --all --dirty --always | sed -E 's/[a-z]+\///;s/\//-/')
export GIT_SHA := $(shell git rev-parse HEAD)
export TIMESTAMP := $(shell date +"%Y%m%d%H%M%S")

# Default docker-compose settings for testing (see: https://docs.docker.com/compose/reference/envvars/)
export COMPOSE_FILE ?= docker-compose.ci.yml
COMPOSE_SUFFIX ?= $(GIT_SHA)
export COMPOSE_PROJECT_NAME ?= lookpet_$(COMPOSE_SUFFIX)

export PROJECT_VERSION=$(GIT_SHA)

define compose
	docker-compose $1
endef

include docker/Makefile
include docker/Makefile.ci

build: build-prod-all
build-dev: build-dev-all

validate-composer-file:
	bin/composer validate
install-prod: validate-composer-file
	bin/composer install --no-scripts --prefer-dist --no-dev --optimize-autoloader --classmap-authoritative
install-dev: validate-composer-file
	bin/composer install --no-scripts --prefer-dist

run-composer:
	docker run --rm \
		--interactive \
        -v $(abspath $(WORKING_DIR)):/app \
        -v $(abspath $(HOME)/.ssh):/.ssh \
        -v $(abspath $(HOME)/.composer):/composer \
        --entrypoint "sh" \
        $(BASE_PHP_IMAGE) \
		-c "cp -R /.ssh ~ && chmod -R 0600 ~/.ssh/* && cd /app/ && COMPOSER_MEMORY_LIMIT=-1 /usr/bin/composer $(COMPOSER_FLAGS)"

check-cs: PHP_CS_COMMAND=php-cs-fixer fix --dry-run --format=junit --diff
fix-cs: PHP_CS_COMMAND=php-cs-fixer fix
ROOT_DIR = /app/build/
EXCLUDE_DIRS =

check-cs fix-cs:
	@docker run --rm \
		-v $(WORKING_DIR):$(ROOT_DIR) \
		-e "EXCLUDE_DIRS=$(EXCLUDE_DIRS)" \
		-e "ROOT_DIR=$(ROOT_DIR)" \
		--entrypoint="sh" \
		641201314091.dkr.ecr.eu-central-1.amazonaws.com/php-cs-fixer:latest \
		-c "$(PHP_CS_COMMAND) --config=/app/.php_cs $(PHP_CS_EXTERNAL_FLAGS)"

clean:
	rm -rf var/cache/{test,prod,dev}/
diff-db:
	bin/console doctrine:migrations:diff && bin/console doctrine:migrations:migrate -n
pull:
	git pull
migrate:
	bin/console doctrine:migrations:migrate -n

fixtures:
	$(call compose,exec php bin/console doctrine:fixtures:load --no-interactions)


aws-v2-login:
	aws --region ${AWS_REGION} ecr get-login-password \
        | docker login \
            --password-stdin \
            --username AWS \
            "${NAMESPACE}"

aws-v1-login:
	$$(aws ecr get-login --no-include-email --region "${AWS_REGION}")

clear:
	docker-compose down --remove-orphans --volumes

fix: fix-cs