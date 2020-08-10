ARG BASE_PHP_IMAGE
FROM ${BASE_PHP_IMAGE}

ARG VERSION
ARG GIT_SHA
ARG TIMESTAMP

ENV APP_VERSION_NUMBER=${VERSION:-latest}\
    APP_VERSION_HASH=${GIT_SHA:-latest}\
    APP_VERSION_TIMESTAMP=${TIMESTAMP:-latest}\
    APP_VERSION=${VERSION:-latest}/${GIT_SHA:-latest}/${TIMESTAMP:-latest}\
    COMPOSER_HOME=/var/composer \
    MAGICK_HOME=/usr

USER root

RUN docker-php-ext-install -j$(nproc) exif \
    && docker-php-ext-configure exif --enable-exif \
    && docker-php-ext-enable exif

#Install imagemagick:
RUN apk --no-cache update \
    && apk --no-cache upgrade \
  && apk add --update freetype-dev libpng-dev libjpeg-turbo-dev libxml2-dev autoconf g++ imagemagick-dev imagemagick libtool make \
    && pecl install imagick \
    && docker-php-ext-enable imagick \
    && apk del autoconf g++ libtool make

COPY --chown=www-data . /app
COPY --chown=root docker/php-dev/conf.d/ $PHP_INI_DIR/conf.d/

COPY --from=composer /usr/bin/composer /usr/bin/composer
RUN composer install --prefer-dist --dev

RUN php bin/console cache:warmup -e dev
RUN php bin/console assets:install
RUN php bin/console lint:yaml $(find config src -type f \( -iname \*.yaml -o -iname \*.yml \)) --parse-tags -e dev
CMD ["php-fpm"]
EXPOSE 9000
