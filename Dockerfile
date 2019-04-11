FROM php:7.3-alpine

ENV COMPOSER_HOME /tmp
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV PHPDA_DIR /phpda
ENV PATH="${PHPDA_DIR}/bin:${PATH}"

RUN apk --no-cache add curl openssl graphviz

COPY ./LICENSE ${PHPDA_DIR}/
COPY ./bin ${PHPDA_DIR}/bin
COPY ./src ${PHPDA_DIR}/src
COPY ./composer* ${PHPDA_DIR}/
COPY ./phpda* ${PHPDA_DIR}/

RUN composer-install.sh && composer install -d ${PHPDA_DIR} --no-dev --no-scripts

WORKDIR /app
ENTRYPOINT ["/bin/sh", "/phpda/bin/docker-entrypoint.sh"]
CMD ["phpda"]
