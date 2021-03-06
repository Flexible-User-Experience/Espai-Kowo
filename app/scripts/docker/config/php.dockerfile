FROM php:7.2-fpm

WORKDIR /var/www/html

COPY ./ .

RUN docker-php-ext-install pdo pdo_mysql

RUN chown -R www-data:www-data /var/www/html
