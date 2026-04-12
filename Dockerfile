FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    nginx \
    git unzip curl libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install --no-dev --optimize-autoloader

# 🔥 PERMISOS (CRÍTICO)
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Copiar config de nginx
COPY nginx.conf /etc/nginx/sites-available/default

EXPOSE 10000

# 🔥 TODO en runtime (no en build)
CMD php artisan config:clear && \
    php artisan cache:clear && \
    php artisan route:clear && \
    php artisan view:clear && \
    php artisan config:cache && \
    service nginx start && \
    php-fpm & php artisan queue:work --tries=3 --timeout=90