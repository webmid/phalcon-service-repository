ARG PHP_EXTENSION_DIR=/usr/local/lib/php/extensions/no-debug-non-zts-20170718
ARG PHP_VERSION=7.2
ARG PHALCON_VERSION=3.4.4
ARG PHALCON_EXT_PATH=php7/64bits
ARG PHP_INI_DIR=/usr/local/etc/php


# Phalcon
FROM php:${PHP_VERSION}-fpm-alpine as phalcon

ARG PHALCON_VERSION
ARG PHALCON_EXT_PATH

ARG PHP_EXTENSION_DIR
ARG PHP_INI_DIR

RUN set -xe && \
    # Compile Phalcon
    curl -LO https://github.com/phalcon/cphalcon/archive/v${PHALCON_VERSION}.tar.gz && \
    tar xzf ${PWD}/v${PHALCON_VERSION}.tar.gz && \
    docker-php-ext-install -j $(getconf _NPROCESSORS_ONLN) ${PWD}/cphalcon-${PHALCON_VERSION}/build/${PHALCON_EXT_PATH} && \
    # Remove all temp files
    rm -r  ${PWD}/v${PHALCON_VERSION}.tar.gz ${PWD}/cphalcon-${PHALCON_VERSION} && \
    mkdir /files && \
    cp ${PHP_EXTENSION_DIR}/phalcon.so /files/ && \
    cp ${PHP_INI_DIR}/conf.d/docker-php-ext-phalcon.ini /files/

# Misc PHP extensions
FROM php:${PHP_VERSION}-fpm-alpine as phpextensions

ARG PHP_EXTENSION_DIR
ARG PHP_INI_DIR

RUN apk add --no-cache socat autoconf curl zlib-dev libxml2-dev  build-base gcc abuild binutils && \
    pecl install xdebug && \
    docker-php-ext-enable xdebug && \
    docker-php-ext-install zip opcache && \
    mkdir /files && \
    cp ${PHP_EXTENSION_DIR}/xdebug.so /files/ && \
    cp ${PHP_EXTENSION_DIR}/zip.so /files/ && \
    cp ${PHP_EXTENSION_DIR}/opcache.so /files/ && \
    cp ${PHP_INI_DIR}/conf.d/docker-php-ext-xdebug.ini /files/ && \
    cp ${PHP_INI_DIR}/conf.d/docker-php-ext-opcache.ini /files/ && \
    cp ${PHP_INI_DIR}/conf.d/docker-php-ext-zip.ini /files/

# Composer
FROM composer:latest as composer

ARG PHP_EXTENSION_DIR
ARG PHP_INI_DIR
ARG COMPOSER_ARGS

WORKDIR /files

COPY composer.json composer.json
COPY composer.lock composer.lock

RUN composer install --ignore-platform-reqs \
                   --no-scripts \
                   --no-interaction \
                   --prefer-dist \
                   --optimize-autoloader ${COMPOSER_ARGS}

# Merge dependencies
FROM alpine:3.7 as dependencies

ARG PHP_EXTENSION_DIR
ARG PHP_INI_DIR

WORKDIR /var/www/html

# Copy files from previous buld steps
COPY --from=phalcon /files/docker-php-ext-phalcon.ini ${PHP_INI_DIR}/conf.d/
COPY --from=phalcon /files/phalcon.so ${PHP_EXTENSION_DIR}/

COPY --from=phpextensions /files/*.ini ${PHP_INI_DIR}/conf.d/
COPY --from=phpextensions /files/*.so ${PHP_EXTENSION_DIR}/

# Set proper permissions
FROM php:${PHP_VERSION}-fpm-alpine as prep

ARG PHP_EXTENSION_DIR
ARG PHP_INI_DIR

WORKDIR /var/www/html

# Copy vender/composer files and set permissions
COPY --from=dependencies /var/www/html  /var/www/html

# copy app files
COPY . /var/www/html

# Remove docker directory
RUN rm -rf docker

# Main image
FROM php:${PHP_VERSION}-fpm-alpine as main

ARG PHP_EXTENSION_DIR
ARG PHP_INI_DIR

WORKDIR /var/www/html

# Copy files from dependencies
COPY --from=dependencies ${PHP_EXTENSION_DIR} ${PHP_EXTENSION_DIR}/
COPY --from=dependencies ${PHP_INI_DIR}/conf.d/ ${PHP_INI_DIR}/conf.d/
COPY --from=prep /var/www/html  /var/www/html

# Copy init file
COPY docker/init.sh /app/init.sh

# Set proper permissions on cache directories and bootstrap script
RUN chmod +x /app/init.sh && \
    mkfifo /tmp/stdout && chmod 777 /tmp/stdout

# Install dependencies maxmind and socat on system
RUN apk add --no-cache socat

HEALTHCHECK CMD echo 'testing' | socat TCP:127.0.0.1:9000 - || exit 1

CMD ["/app/init.sh"]

