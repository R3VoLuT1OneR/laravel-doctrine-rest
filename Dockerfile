FROM php:7.4-cli-alpine

RUN apk add git zip unzip libzip-dev curl-dev && docker-php-ext-install zip curl

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

ENV PATH="${PATH}:/opt/project/vendor/bin"

WORKDIR /opt/project
CMD composer install
