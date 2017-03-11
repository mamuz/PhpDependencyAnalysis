FROM php:7.1-alpine

ARG COMPOSER_OPTS

RUN apk --no-cache add curl git openssl graphviz

COPY . /app
WORKDIR /app

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer update $COMPOSER_OPTS

CMD [""]
