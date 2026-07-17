FROM richarvey/nginx-php-fpm:3.1.6

COPY . .

# Install dependency PHP saat BUILD image (bukan runtime) supaya vendor/autoload.php
# sudah tersedia & startup lebih cepat. --no-scripts karena artisan belum bisa jalan
# sebelum .env siap saat build; package discovery dipicu ulang di deploy script.
RUN composer install --no-dev --working-dir=/var/www/html --no-interaction --optimize-autoloader --no-scripts

ENV SKIP_COMPOSER=1
ENV WEBROOT=/var/www/html/public
ENV PHP_ERRORS_STDERR=1
ENV RUN_SCRIPTS=1
ENV REAL_IP_HEADER=1

ENV APP_ENV=production
ENV APP_DEBUG=false
ENV LOG_CHANNEL=stderr

ENV COMPOSER_ALLOW_SUPERUSER=1

CMD ["/start.sh"]
