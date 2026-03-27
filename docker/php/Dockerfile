# syntax=docker/dockerfile:1

FROM php:8.4-fpm AS php_base

WORKDIR /app

RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    libicu-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    default-libmysqlclient-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    intl \
    pdo_mysql \
    zip \
    opcache \
    gd \
    && pecl install apcu xdebug \
    && docker-php-ext-enable apcu \
    && rm -rf /var/lib/apt/lists/*

#RUN docker-php-ext-enable pdo_mysql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1

COPY docker/php/conf.d/10-app.ini $PHP_INI_DIR/conf.d/

COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# Development
FROM php_base AS php_dev

COPY docker/php/conf.d/20-xdebug.ini $PHP_INI_DIR/conf.d/
RUN docker-php-ext-enable xdebug

# Production
FROM php_base AS php_prod

# Disable Xdebug in production
COPY docker/php/conf.d/20-opcache.ini $PHP_INI_DIR/conf.d/
