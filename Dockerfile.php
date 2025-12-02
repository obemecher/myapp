FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
        libzip-dev libpng-dev libjpeg-dev libwebp-dev libfreetype6-dev libonig-dev \
    && docker-php-ext-install mysqli pdo pdo_mysql \
    && pecl install redis \
    && docker-php-ext-enable redis

COPY ./app /var/www/html
