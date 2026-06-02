FROM php:8.2-apache

# Update and install basic dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Gunakan script install-php-extensions (jauh lebih aman & anti-gagal)
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions

# Install PHP extensions (Otomatis mendownload GPG key MS SQL & ekstensi lainnya)
RUN install-php-extensions pdo_mysql gd zip sqlsrv pdo_sqlsrv

# Enable Apache mod_rewrite (dibutuhkan untuk Laravel)
RUN a2enmod rewrite

# Ubah DocumentRoot Apache agar mengarah ke folder /public milik Laravel
ENV APACHE_DOCUMENT_ROOT="/var/www/html/public"
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Set working directory
WORKDIR /var/www/html

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application files
COPY . .

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Install composer dependencies
RUN composer install --no-interaction --no-dev --optimize-autoloader

EXPOSE 80
CMD ["apache2-foreground"]
