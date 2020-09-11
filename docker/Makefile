build-push-develop: build-dev-all push-dev-all
build-push-production: build-prod-all push-prod-all
build-dev-all: build-nginx build-dev-php
build-prod-all: build-nginx build-prod-php

push-prod-all: push-prod
push-dev-all: push-dev

build-dev-php:
	$(call about,Build develop PHP image)
	($(PULL_IF_EXIST) == true && docker pull $(NAMESPACE)/api-php-dev:$(GIT_SHA)) || \
	docker build --secret id=ssh,src="$(SSH_KEY_FILE)" \
		-t $(NAMESPACE)/api-php-dev:$(GIT_SHA) \
		--build-arg BASE_PHP_IMAGE=$(BASE_PHP_IMAGE) \
		--build-arg TIMESTAMP=$(TIMESTAMP) \
		--build-arg VERSION=$(VERSION) \
		--build-arg GIT_SHA=$(GIT_SHA) \
		-f docker/php-dev/Dockerfile \
		docker/../
	docker tag $(NAMESPACE)/api-php-dev:$(GIT_SHA) $(NAMESPACE)/api-php-dev:$(VERSION)
	docker tag $(NAMESPACE)/api-php-dev:$(GIT_SHA) $(NAMESPACE)/api-php-dev:latest

build-nginx:
	$(call about,Build NGINX image)
	($(PULL_IF_EXIST) == true && docker pull $(NAMESPACE)/nginx:$(GIT_SHA)) || \
	docker build \
		-t $(NAMESPACE)/nginx:$(GIT_SHA) \
		--build-arg PROD_PHP_IMAGE=$(NAMESPACE)/api-php:$(VERSION) \
		-f docker/nginx/Dockerfile \
		docker/../
	docker tag $(NAMESPACE)/nginx:$(GIT_SHA) $(NAMESPACE)/nginx:$(VERSION)
	docker tag $(NAMESPACE)/nginx:$(GIT_SHA) $(NAMESPACE)/nginx:latest

push-nginx:
	docker push $(NAMESPACE)/nginx:$(VERSION)
	docker push $(NAMESPACE)/nginx:$(GIT_SHA)

push-prod: push-nginx ## Push production images
	$(call about,Pushing production images)
	docker push $(NAMESPACE)/api-php:$(VERSION)
	docker push $(NAMESPACE)/api-php:$(GIT_SHA)

push-dev: push-nginx ## Push develop images
	$(call about,Pushing dev images)
	docker push $(NAMESPACE)/api-php-dev:$(VERSION)
	docker push $(NAMESPACE)/api-php-dev:$(GIT_SHA)
