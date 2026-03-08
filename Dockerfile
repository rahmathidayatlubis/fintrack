FROM php:8.3-apache

# Install dependensi sistem
RUN apt-get update && apt-get install -y \
    libpng-dev libonig-dev libxml2-dev zip unzip git curl

# Install ekstensi PHP untuk Laravel & MySQL
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Aktifkan Mod Rewrite Apache agar route Laravel jalan
RUN a2enmod rewrite

# Arahkan Document Root ke folder /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy semua file project ke dalam container
WORKDIR /var/www/html
COPY . .

# Install dependensi tanpa dev-tools untuk performa
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Atur izin folder agar Laravel bisa menulis log/cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80
CMD ["apache2-foreground"]