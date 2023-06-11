FROM php:5.6.40-fpm-alpine3.8

WORKDIR /app

COPY --chmod=755 --from=mlocati/php-extension-installer:1.5.52 /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions xdebug-2.5.5 @composer-1.10.26
RUN ln -s /app/vendor/bin/phpunit /usr/local/bin/phpunit
