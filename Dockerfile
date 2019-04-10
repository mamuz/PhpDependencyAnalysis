FROM php:7.3-alpine

ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_DISABLE_XDEBUG_WARN 1

RUN apk --no-cache add bash curl git openssl graphviz

COPY ./bin /app/bin
COPY ./src /app/src
COPY ./composer* /app/
COPY ./phpda* /app/

RUN cd /app && ./bin/composer-install.sh && composer install --no-dev --no-scripts

WORKDIR /tmp/src
ENTRYPOINT ["/app/bin/docker-entrypoint.sh"]
