FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libicu-dev libonig-dev libpng-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql intl zip gd xml mbstring

# Opcache (optional, built-in)
RUN docker-php-ext-install opcache

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# PHP configuration overrides
COPY docker/php/conf.d/*.ini /usr/local/etc/php/conf.d/

WORKDIR /var/www/html

# Configure compiled Blade view path
ENV VIEW_COMPILED_PATH=/tmp/laravel-views

# Ensure view cache directory exists and is writable
RUN mkdir -p /tmp/laravel-views \
    && chown -R www-data:www-data /tmp/laravel-views \
    && chmod -R 775 /tmp/laravel-views

# Entrypoint to prepare permissions and start php-fpm
COPY docker/php/entrypoint.sh /usr/local/bin/app-entrypoint
RUN chmod +x /usr/local/bin/app-entrypoint
ENTRYPOINT ["app-entrypoint"]
