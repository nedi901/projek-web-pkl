FROM php:8.3-apache

# Install dependencies sistem & ekstensi PHP (termasuk libzip-dev & libpq-dev)
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libpq-dev \
    libsqlite3-dev \
    nodejs \
    npm \
    && docker-php-ext-install pdo pdo_sqlite pdo_pgsql pgsql mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Aktifkan mod_rewrite Apache
RUN a2enmod rewrite

# Arahkan Apache DocumentRoot ke folder public Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Atur Apache agar mendengarkan port yang diberikan Render ($PORT, default 10000)
RUN sed -ri -e 's!Listen 80!Listen ${PORT:-10000}!g' /etc/apache2/ports.conf \
    && sed -ri -e 's!<VirtualHost \*:80>!<VirtualHost *:${PORT:-10000}>!g' /etc/apache2/sites-available/*.conf

# Pasang Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Konfigurasi Composer untuk mencegah 504 Gateway Timeout dari GitHub API di cloud runner
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_MAX_PARALLEL_HTTP=4
ENV COMPOSER_PROCESS_TIMEOUT=1200

WORKDIR /var/www/html

# Salin file dependensi terlebih dahulu agar di-cache oleh Docker layer
COPY composer.json composer.lock ./
COPY package.json package-lock.json ./

# Download dependensi Composer dengan retry mechanism jika GitHub mengalami 504
RUN (composer install --no-dev --no-scripts --no-autoloader --no-interaction --prefer-dist || \
     (sleep 5 && composer install --no-dev --no-scripts --no-autoloader --no-interaction --prefer-dist))

# Install dependensi NPM
RUN npm ci || npm install

# Salin seluruh sisa source code proyek
COPY . /var/www/html

# Bangun autoload Composer dan aset frontend Vite
RUN composer dump-autoload --optimize --no-dev \
    && npm run build

# Atur permission folder storage, cache, dan database
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 10000

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
