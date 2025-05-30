FROM php:8.2-fpm


RUN apt-get update && apt-get install -y \
    git unzip curl libicu-dev libzip-dev libpng-dev \
    libonig-dev libxml2-dev libcurl4-openssl-dev libssl-dev \
    && docker-php-ext-install intl pdo pdo_mysql zip


COPY --from=composer:2 /usr/bin/composer /usr/bin/composer


WORKDIR /var/www/html


COPY . .


RUN composer install --no-dev --optimize-autoloader


RUN php bin/console asset-map:compile


RUN php bin/console cache:clear --env=prod && php bin/console cache:warmup --env=prod


RUN echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.interned_strings_buffer=8" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.max_accelerated_files=10000" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.validate_timestamps=0" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.revalidate_freq=0" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini