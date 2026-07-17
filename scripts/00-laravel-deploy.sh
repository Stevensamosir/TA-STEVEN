#!/usr/bin/env bash
set -e

echo "=== Cek composer ==="
if ! command -v composer &> /dev/null; then
    echo "composer tidak ditemukan, install manual..."
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
fi
composer --version

echo "=== Running composer install ==="
composer install --no-dev --working-dir=/var/www/html --no-interaction --optimize-autoloader

echo "=== Caching config ==="
php /var/www/html/artisan config:cache
php /var/www/html/artisan route:cache

echo "=== Running migrations ==="
php /var/www/html/artisan migrate --force

echo "=== Linking storage ==="
php /var/www/html/artisan storage:link || echo "storage:link sudah ada atau gagal, lanjut..."

echo "=== Deploy script selesai ==="
