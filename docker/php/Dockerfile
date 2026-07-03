FROM php:8.4-fpm-alpine

# Extensions the platform needs: pgsql, redis (phpredis), queues (pcntl),
# money/intl formatting, images for media-library, zip for excel imports.
ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN install-php-extensions \
    pdo_pgsql \
    redis \
    pcntl \
    intl \
    bcmath \
    gd \
    zip \
    exif \
    opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY php.ini /usr/local/etc/php/conf.d/flowflex.ini

WORKDIR /var/www/html
