FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git unzip curl libicu-dev libzip-dev libpng-dev \
    libonig-dev libxml2-dev libcurl4-openssl-dev libssl-dev \
    && docker-php-ext-install intl pdo pdo_mysql zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html