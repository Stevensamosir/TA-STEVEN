FROM php:8.3-fpm-alpine

RUN apk add --no-cache nginx supervisor bash icu-dev libzip-dev postgresql-dev oniguruma-dev libpng-dev libjpeg-turbo-dev freetype-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql pgsql mbstring zip intl bcmath gd opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

RUN composer install --no-dev --working-dir=/var/www/html --no-interaction --optimize-autoloader --no-scripts

RUN mkdir -p /var/www/html/storage/framework/{sessions,views,cache} \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 80

CMD ["/entrypoint.sh"]
