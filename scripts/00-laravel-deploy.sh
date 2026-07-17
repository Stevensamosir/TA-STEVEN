#!/usr/bin/env bash
set -e

# Catatan: composer install TIDAK lagi di sini -- sudah dijalankan saat BUILD
# image (lihat Dockerfile). Di sini tinggal langkah yang butuh runtime/.env.

echo "=== Package discovery ==="
# Memicu ulang discovery package Laravel yang di-skip saat build (--no-scripts).
php /var/www/html/artisan package:discover --ansi

echo "=== Caching config ==="
php /var/www/html/artisan config:cache
php /var/www/html/artisan route:cache

echo "=== Running migrations ==="
php /var/www/html/artisan migrate --force

echo "=== Linking storage ==="
php /var/www/html/artisan storage:link || echo "storage:link sudah ada atau gagal, lanjut..."

echo "=== Deploy script selesai ==="
