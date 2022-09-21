FROM public.ecr.aws/docker/library/php:7.4-fpm-alpine

WORKDIR /var/www/html

COPY ./app/composer.json ./

COPY --from=public.ecr.aws/composer/composer:2 /usr/bin/composer /usr/bin/composer

RUN composer install --ignore-platform-reqs
# 設定ファイルを指定の場所に置く
COPY dockerfiles/php.ini /usr/local/etc/php/php.ini
COPY dockerfiles/www.conf /usr/local/etc/php-fpm.d/zzz-www.conf


COPY app .
COPY app/.env.default ./
RUN mv .env.default .env

RUN docker-php-ext-install pdo pdo_mysql

RUN chown -R www-data:www-data /var/www/html