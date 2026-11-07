#!/bin/bash
set -e

# Fallback default APP_KEY jika belum diset di Render Dashboard
if [ -z "$APP_KEY" ]; then
    export APP_KEY="base64:YNOHi8cYLDob3z1xoZHEWrrxuDOtjHer3fAdiGwm/Uw="
fi

# Atur port Apache sesuai variabel $PORT dari Render (default: 10000)
PORT_HTTP="${PORT:-10000}"
echo "Mengatur Apache untuk mendengarkan port: ${PORT_HTTP}"
echo "Listen ${PORT_HTTP}" > /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \*:[0-9]+>/<VirtualHost \*:${PORT_HTTP}>/g" /etc/apache2/sites-available/000-default.conf

# Pastikan file SQLite ada dan dapat ditulis jika memakai sqlite
if [ -f /var/www/html/database/database.sqlite ]; then
    chown www-data:www-data /var/www/html/database/database.sqlite || true
    chmod 664 /var/www/html/database/database.sqlite || true
fi

# Jalankan migrasi database
php artisan migrate --force || true

# Optimasi performa untuk server produksi
php artisan config:clear || true
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Jalankan server web Apache di foreground
exec apache2-foreground
