ARG BASE_PHP_IMAGE
FROM ${BASE_PHP_IMAGE}

ARG VERSION
ARG GIT_SHA
ARG TIMESTAMP

COPY --chown=www-data . /app

ENV APP_ENV=prod

ENV APP_DEBUG=1 \
    APP_VERSION_NUMBER=${VERSION:-latest}\
    APP_VERSION_HASH=${GIT_SHA:-latest}\
    APP_VERSION_TIMESTAMP=${TIMESTAMP:-latest}\
    APP_VERSION=${VERSION:-latest}/${GIT_SHA:-latest}/${TIMESTAMP:-latest}\
    COMPOSER_HOME=/var/composer

COPY --chown=root docker/api-php-prod/conf.d/ $PHP_INI_DIR/conf.d/

COPY --from=composer /usr/bin/composer /usr/bin/composer
RUN composer install --prefer-dist --no-scripts
#db in CI required
#RUN php bin/console cache:warmup -e prod
#RUN php bin/console lint:yaml $(find config src -type f \( -iname \*.yaml -o -iname \*.yml \)) --parse-tags -e prod
CMD ["php-fpm"]
EXPOSE 9000