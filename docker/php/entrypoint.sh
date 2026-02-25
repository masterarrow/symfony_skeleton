#!/bin/sh
set -e

if [ ! -f /app/vendor/autoload.php ]; then
    echo "Vendor directory not found. Running composer install..."
    if [ "$APP_ENV" = "prod" ]; then
        composer install --no-dev --optimize-autoloader --no-interaction
    else
        composer install --optimize-autoloader --no-interaction
    fi
else
    echo "Vendor directory already exists. Skipping composer install."
fi

exec php-fpm
