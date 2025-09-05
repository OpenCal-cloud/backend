# -------------
# generic php base image
# -------------
FROM php:8.4-fpm-alpine AS generic

ARG OPENCAL_VERSION=
ENV OPENCAL_VERSION=${OPENCAL_VERSION}

RUN apk update && apk --no-cache add fcgi=~2.4.6 git=~2.49

RUN apk update && apk --no-cache add icu-dev=~76.1 openssl=~3.5 acl=~2.3
RUN docker-php-ext-install mysqli pdo pdo_mysql posix pcntl intl

RUN apk update && apk --no-cache add libzip-dev=~1.11 zip=~3.0 \
    && docker-php-ext-install zip

RUN apk update && apk --no-cache add nginx=~1.28

COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/conf.d /etc/nginx/conf.d/

#RUN docker-php-ext-enable apcu
COPY --from=composer:2.8.11 /usr/bin/composer /usr/local/bin/composer

VOLUME /var/run/php

COPY docker/php/zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf

COPY docker/php/php-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

COPY docker/php/docker-healthcheck.sh /usr/local/bin/docker-healthcheck
RUN chmod +x /usr/local/bin/docker-healthcheck

RUN apk update && apk --no-cache add supervisor=~4.2

COPY docker/supervisord.conf /etc/supervisor/supervisord.conf

COPY docker/crontab /etc/cron/crontab
RUN crontab /etc/cron/crontab

EXPOSE 8080
ENTRYPOINT ["docker-entrypoint"]
CMD ["supervisord", "-c", "/etc/supervisor/supervisord.conf"]
HEALTHCHECK --timeout=3s CMD curl --silent --fail http://127.0.0.1:8080/fpm-ping || exit 1

FROM generic AS base

#ARG CI_JOB_TOKEN=
ARG COMPOSER_AUTH=

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_AUTH=${COMPOSER_AUTH}

WORKDIR /srv/app

# prevent the reinstallation of vendors at every changes in the source code
COPY composer.json composer.lock symfony.lock ./

# copy only specifically what we need
COPY bin bin/
COPY config config/
COPY public public/
COPY src src/
COPY migrations migrations/

RUN mkdir -p files && \
    mkdir -p var/cache var/log

VOLUME /srv/app/var

# ---------
# prod build
# ---------
FROM base AS build_prod

WORKDIR /srv/app

RUN composer install --prefer-dist --no-dev --no-scripts --no-progress -v && \
    composer clear-cache

COPY .env ./

ENV APP_ENV=prod

RUN composer dump-autoload --classmap-authoritative --no-dev && \
    chmod +x bin/console && \
    sync && \
    rm -rf src/DataFixtures && \
    php bin/console assets:install && \
    echo "memory_limit = -1" >> "$PHP_INI_DIR/conf.d/memory_limit_php.ini" && \
    echo "upload_max_filesize = 200 M" >> "$PHP_INI_DIR/conf.d/memory_limit_php.ini"

# ---------
# dev build
# ---------
FROM base AS build_dev

RUN apk update && apk --no-cache add bash=~5.2

COPY tests tests/
COPY phpunit.dist.xml phpstan.neon ./
COPY .env .env.test ./

RUN composer install --prefer-dist --no-scripts --no-progress && \
    composer clear-cache

RUN composer dump-autoload && \
    composer run-script post-install-cmd || \
    chmod +x bin/console && \
    sync

RUN php bin/console assets:install

# --------------
# php prod image
# --------------
FROM base AS php

WORKDIR /srv/app

RUN ln -sf "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Modify memory limit
RUN echo 'memory_limit = -1' >> "$PHP_INI_DIR/conf.d/memory_limit_php.ini" && \
    echo 'upload_max_filesize = 200 M' >> "$PHP_INI_DIR/conf.d/memory_limit_php.ini"

COPY --from=build_prod /srv/app /srv/app
RUN chown -R www-data var

# -------------
# php dev image
# -------------
FROM base AS php_dev

WORKDIR /srv/app

RUN apk update && apk --no-cache add dpkg-dev=~1.22 dpkg=~1.22 file=~5.46 g++=~14.2 gcc=~14.2 make=~4.4 pkgconf=~2.4 re2c=~4.2 linux-headers=~6.14 autoconf=~2.72 \
    && pecl install xdebug-3.4.5 \
    && docker-php-ext-enable xdebug

ARG COMPOSER_AUTH=

COPY phpunit.dist.xml phpstan.neon ./
COPY --from=build_dev /srv/app /srv/app

RUN chown -R www-data var && \
    ln -sf "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

# Modify memory limit
RUN echo 'memory_limit = -1' >> "$PHP_INI_DIR/conf.d/memory_limit_php.ini" && \
    echo 'upload_max_filesize = 200 M' >> "$PHP_INI_DIR/conf.d/memory_limit_php.ini"

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_AUTH=${COMPOSER_AUTH}
