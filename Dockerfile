# syntax=docker/dockerfile:1

# Stage 1: install Composer dependencies
FROM composer:lts AS deps

WORKDIR /app

RUN --mount=type=bind,source=composer.json,target=composer.json \
    --mount=type=bind,source=composer.lock,target=composer.lock \
    --mount=type=cache,target=/tmp/cache \
    composer install --no-dev --no-interaction

# Stage 2: final image with PHP + Apache
FROM php:8.3-apache AS final

# Install PHP extensions required by Laravel
RUN apt-get update && apt-get install -y \
    libzip-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring zip bcmath \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite (needed for Laravel routes)
RUN a2enmod rewrite

# Point Apache document root to Laravel's public/ folder
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -i 's|/var/www/html|${APACHE_DOCUMENT_ROOT}|g' /etc/apache2/sites-available/000-default.conf \
    && sed -i 's|/var/www/html|${APACHE_DOCUMENT_ROOT}|g' /etc/apache2/apache2.conf

# Use production PHP config
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Copy vendor from deps stage
COPY --from=deps /app/vendor/ /var/www/html/vendor

# Copy application files
COPY ./ /var/www/html

# Give Apache write access to Laravel's storage and cache folders
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

USER www-data
