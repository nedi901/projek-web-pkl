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

# Konfigurasi Apache DocumentRoot dan Permission untuk Laravel
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN printf '<VirtualHost *:10000>\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
        Options -Indexes +FollowSymLinks\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>\n' > /etc/apache2/sites-available/000-default.conf

RUN printf 'DirectoryIndex index.php index.html\n\
<Directory /var/www/html/public>\n\
    Options -Indexes +FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>\n' >> /etc/apache2/apache2.conf

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

# Atur permission seluruh folder kerja dan folder storage/cache
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 10000

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
