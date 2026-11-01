#!/bin/sh

echo ">> [1] atur port"
PORT="${PORT:-10000}"
sed -ri "s/^Listen .*/Listen ${PORT}/" /etc/apache2/ports.conf
sed -ri "s#<VirtualHost \*:[0-9]+>#<VirtualHost *:${PORT}>#g" /etc/apache2/sites-available/*.conf

echo ">> [2] cek driver pgsql"
php -m | grep -i pgsql || echo "pdo_pgsql TIDAK ADA"

echo ">> [3] tes koneksi database"
php -r '
try {
  new PDO("pgsql:host=".getenv("DB_HOST").";port=5432;dbname=".getenv("DB_DATABASE").";sslmode=require", getenv("DB_USERNAME"), getenv("DB_PASSWORD"));
  echo "DB OK\n";
} catch (Throwable $e) {
  echo "DB GAGAL: ".$e->getMessage()."\n";
}'

echo ">> [4] migrate"
php artisan migrate --force 2>&1 || echo "MIGRATE GAGAL"

echo ">> [5] cache"
php artisan config:cache 2>&1
php artisan route:cache 2>&1
php artisan view:cache 2>&1

chown -R www-data:www-data storage bootstrap/cache

echo ">> [6] start apache"
exec apache2-foreground