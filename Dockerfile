FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    nginx \
    git unzip curl libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install --no-dev --optimize-autoloader

# Laravel optimización
RUN php artisan config:clear \
 && php artisan config:cache \
 && php artisan route:cache \
 && php artisan view:cache

# Copiar config de nginx
COPY nginx.conf /etc/nginx/sites-available/default

EXPOSE 10000

CMD service nginx start && php-fpm