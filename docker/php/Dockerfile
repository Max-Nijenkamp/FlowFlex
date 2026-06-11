FROM php:8.4-fpm-alpine

# System deps for PHP extensions
RUN apk add --no-cache \
        postgresql-dev \
        icu-dev \
        libzip-dev \
        oniguruma-dev \
        freetype-dev \
        libjpeg-turbo-dev \
        libpng-dev \
        $PHPIZE_DEPS

# Core PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_pgsql \
        pcntl \
        bcmath \
        intl \
        zip \
        gd

# Redis (PECL)
RUN pecl install redis && docker-php-ext-enable redis

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY php.ini /usr/local/etc/php/conf.d/flowflex.ini

WORKDIR /var/www

CMD ["php-fpm"]
