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

# Setup GD extension
RUN apk add --no-cache \
      freetype \
      libjpeg-turbo \
      libpng \
      freetype-dev \
      libjpeg-turbo-dev \
      libpng-dev \
    && docker-php-ext-configure gd \
      --with-freetype=/usr/include/ \
      # --with-png=/usr/include/ \ # No longer necessary as of 7.4; https://github.com/docker-library/php/pull/910#issuecomment-559383597
      --with-jpeg=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-enable gd \
    && apk del --no-cache \
      freetype-dev \
      libjpeg-turbo-dev \
      libpng-dev \
    && rm -rf /tmp/*

COPY --chown=root docker/php-prod/conf.d/ $PHP_INI_DIR/conf.d/

COPY --from=composer /usr/bin/composer /usr/bin/composer
RUN composer install --prefer-dist --no-scripts
RUN php bin/console assets:install
#db in CI required
#RUN php bin/console cache:warmup -e prod
#RUN php bin/console lint:yaml $(find config src -type f \( -iname \*.yaml -o -iname \*.yml \)) --parse-tags -e prod
CMD ["php-fpm"]
EXPOSE 9000