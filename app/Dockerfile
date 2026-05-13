FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    git curl zip unzip libpq-dev libzip-dev libicu-dev libgd-dev \
    && docker-php-ext-install pdo_pgsql zip intl gd pcntl bcmath \
    && pecl install redis && docker-php-ext-enable redis \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts

COPY . .
RUN composer run-script post-autoload-dump

EXPOSE 9000
CMD ["php-fpm"]
