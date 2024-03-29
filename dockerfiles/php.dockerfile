FROM composer:2 as composer_build

COPY ./app/composer.* /app/

RUN composer install -n --prefer-dist
COPY ./app /app

FROM php:8.2-fpm-alpine
# 設定ファイルを指定の場所に置く
COPY dockerfiles/php.ini /usr/local/etc/php/php.ini
COPY dockerfiles/zzz-www.conf /usr/local/etc/php-fpm.d/zzz-www.conf

# xdebugインストール
RUN apk add autoconf build-base linux-headers \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug
RUN docker-php-ext-install pdo pdo_mysql

WORKDIR /var/www/html

COPY --from=composer_build /usr/bin/composer /usr/bin/composer
COPY --chown=www-data --from=composer_build /app/ ./
