#!/usr/bin/env bash

# Fix permissions on every deploy
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache
chown -R www-data:www-data /var/www/html/storage

echo "Discovering packages..."
php artisan package:discover --ansi

echo "Clearing previous cache..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo "Running migrations..."
php artisan migrate --force

# Uncomment below ONLY after confirming seeders use firstOrCreate
# echo "Running seeders..."
# php artisan db:seed --force

echo "Caching config..."
php artisan config:cache

echo "Caching routes..."
php artisan route:cache

echo "Caching views..."
php artisan view:cache

echo "Starting PHP-FPM..."
php-fpm -D

echo "Starting Nginx..."
nginx -g "daemon off;"