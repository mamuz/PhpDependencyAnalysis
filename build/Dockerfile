FROM php:7.3-alpine

ARG COMPOSER_OPTS

ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_DISABLE_XDEBUG_WARN 1

RUN apk --no-cache add curl git openssl graphviz

COPY . /app
WORKDIR /app

RUN ./build/composer.sh && composer update $COMPOSER_OPTS
