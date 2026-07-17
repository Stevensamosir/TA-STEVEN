#!/usr/bin/env bash
set -e

echo "=== Package discovery ==="
php /var/www/html/artisan package:discover --ansi

echo "=== Caching config ==="
php /var/www/html/artisan config:cache
php /var/www/html/artisan route:cache

echo "=== Running migrations ==="
php /var/www/html/artisan migrate --force

echo "=== Linking storage ==="
php /var/www/html/artisan storage:link || echo "storage:link sudah ada atau gagal, lanjut..."

echo "=== Starting supervisord ==="
exec supervisord -c /etc/supervisord.conf
