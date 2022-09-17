FROM php:7.4-fpm-alpine

# xdebugインストール
RUN apk add autoconf build-base \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug

WORKDIR /var/www/html

COPY ./app/composer.json ./

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN composer install --ignore-platform-reqs
# 設定ファイルを指定の場所に置く
COPY dockerfiles/php.ini /usr/local/etc/php/php.ini
COPY dockerfiles/www.conf /usr/local/etc/php-fpm.d/zzz-www.conf


COPY app .

RUN docker-php-ext-install pdo pdo_mysql

RUN chown -R www-data:www-data /var/www/html