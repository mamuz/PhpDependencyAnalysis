FROM php:7.3-alpine

ENV COMPOSER_HOME /tmp
ENV COMPOSER_ALLOW_SUPERUSER 1

RUN apk --no-cache add bash curl git openssl graphviz

COPY . /app
WORKDIR /app

RUN ./bin/composer-install.sh && composer update
