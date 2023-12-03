ARG PHP_TAG=7.3-cli-alpine3.14
FROM php:${PHP_TAG} as base

# Install composer
#
ENV COMPOSER_HOME /tmp
ENV COMPOSER_ALLOW_SUPERUSER 1
RUN set -eux; \
  composerVersion='2.6.5'; \
  installerUrl='https://raw.githubusercontent.com/composer/getcomposer.org/70527179915d55b3811bebaec55926afd331091b/web/installer'; \
  fetchDeps=' \
    curl \
  '; \
  \
  apk add --no-cache \
    ${fetchDeps} \
    git \
    libzip-dev \
    unzip \
  ; \
  \
  docker-php-ext-install zip; \
  \
  curl --insecure -sSLf "${installerUrl}" -o /usr/local/bin/composer-installer.php; \
  sha256sum /usr/local/bin/composer-installer.php; \
  echo '203196aedb1a3b0f563363796bbf6f647a4f8c2419bc1dfc5aa45adc1725025d  /usr/local/bin/composer-installer.php' \
    | sha256sum -cws; \
  \
  { \
    echo '#! /usr/bin/env php'; \
    cat /usr/local/bin/composer-installer.php; \
  } > /usr/local/bin/composer-installer; \
  rm /usr/local/bin/composer-installer.php; \
  chmod +x /usr/local/bin/composer-installer; \
  \
  composer-installer \
    --disable-tls \
    --version="${composerVersion}" \
    --filename=composer \
    --install-dir=/usr/local/bin \
  ; \
  \
  sha256sum /usr/local/bin/composer; \
  echo '9a18e1a3aadbcb94c1bafd6c4a98ff931f4b43a456ef48575130466e19f05dd6  /usr/local/bin/composer' \
    | sha256sum -cws; \
  \
  composer --version; \
  \
  apk del ${fetchDeps}; \
  :;

# Install graphviz
#
RUN apk --no-cache add graphviz

# Configure source files location
ENV PHPDA_DIR /phpda
ENV PATH="${PHPDA_DIR}/bin:${PATH}"

FROM base as dev

FROM base as prod

COPY ./LICENSE ${PHPDA_DIR}/
COPY ./bin/phpda ${PHPDA_DIR}/bin
COPY ./bin/phpda.php ${PHPDA_DIR}/bin
COPY ./bin/docker-entrypoint.sh ${PHPDA_DIR}/bin
COPY ./src ${PHPDA_DIR}/src
COPY ./composer* ${PHPDA_DIR}/
COPY ./phpda* ${PHPDA_DIR}/

RUN composer update -d ${PHPDA_DIR} --no-dev --no-scripts

WORKDIR /app
ENTRYPOINT ["/bin/sh", "/phpda/bin/docker-entrypoint.sh"]
CMD ["phpda"]
