ARG PHP_VERSION=8.1

FROM php:${PHP_VERSION}-alpine AS base
WORKDIR /app
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/
COPY composer.json .

RUN set -eux; \
    apk add --no-cache zip; \
    install-php-extensions sockets zip; \
    composer install;


FROM base AS test

RUN install-php-extensions pcov

CMD ["vendor/bin/phpunit"]
