FROM php:8.3-fpm

# Step 1 — Install system packages
RUN apt-get update && apt-get install -y \
    nginx \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    libzip-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libwebp-dev \
    libxrender1 \
    libfontconfig1 \
    libicu-dev \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Step 2 — Configure GD
RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg \
    --with-webp

# Step 3 — Install PHP extensions
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    intl

# Step 4 — Install soap separately (needs special handling)
RUN docker-php-ext-install soap

# Step 5 — PHP config
RUN echo "memory_limit=512M" > /usr/local/etc/php/conf.d/memory.ini

# Step 6 — Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Step 7 — Install dependencies
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --ignore-platform-reqs

# Step 8 — Copy project & generate autoloader
COPY . .
RUN composer dump-autoload --optimize

# Step 9 — Permissions
RUN chown -R www-data:www-data storage bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

# Step 10 — Config files
COPY nginx.conf /etc/nginx/sites-available/default
COPY scripts/deploy.sh /deploy.sh
RUN chmod +x /deploy.sh

EXPOSE 80
CMD ["/deploy.sh"]