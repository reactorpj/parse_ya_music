FROM composer:lts as composer

FROM php:8.2-cli-alpine3.21

WORKDIR /app

RUN apk add --no-cache --update \
  oniguruma-dev \
  php82-xml \
  php82-simplexml \
  php82-xmlwriter \
  php82-dev \
  && docker-php-ext-install mbstring \
  && docker-php-ext-enable mbstring \
  && rm -rf /tmp/*

RUN docker-php-ext-install pdo pdo_mysql mysqli

RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS
RUN apk add --update linux-headers
RUN pecl install xdebug
RUN apk del -f .build-deps

COPY --from=composer /usr/bin/composer /usr/bin/composer
