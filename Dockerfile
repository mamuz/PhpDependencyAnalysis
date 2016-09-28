FROM alpine:3.3

RUN apk add --no-cache bash curl git graphviz php-cli php-json php-phar php-openssl php-curl php-dom php-ctype

COPY . /phpda
WORKDIR /phpda

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
 && composer install

CMD [""]
