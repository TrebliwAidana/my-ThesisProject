FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    nginx git curl zip unzip \
    libpng-dev libonig-dev \
    libxml2-dev libpq-dev \
    && docker-php-ext-install \
    pdo pdo_pgsql pdo_mysql \
    mbstring exif pcntl bcmath gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

RUN composer install --no-dev --optimize-autoloader

RUN chown -R www-data:www-data storage bootstrap/cache

COPY nginx.conf /etc/nginx/sites-available/default
COPY scripts/deploy.sh /deploy.sh
RUN chmod +x /deploy.sh

EXPOSE 80
CMD ["/deploy.sh"]