#!/bin/bash
set -e

# Pastikan file SQLite ada dan dapat ditulis oleh Apache
if [ ! -f /var/www/html/database/database.sqlite ]; then
    touch /var/www/html/database/database.sqlite
fi
chown www-data:www-data /var/www/html/database/database.sqlite
chmod 664 /var/www/html/database/database.sqlite

# Jalankan migrasi database
php artisan migrate --force

# Optimasi performa untuk server produksi
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Jalankan server web Apache di foreground
exec apache2-foreground
