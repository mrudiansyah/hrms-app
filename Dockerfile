FROM php:8.2-apache

# Install dependencies
RUN apt-get update && apt-get install -y \
    gnupg2 \
    curl \
    apt-transport-https \
    git \
    unzip \
    libpng-dev \
    libxml2-dev \
    libzip-dev \
    unixodbc-dev \
    g++ \
    make \
    build-essential \
    && rm -rf /var/lib/apt/lists/*

# Add Microsoft repo for msodbcsql18 (for Debian 12)
RUN curl -fsSL https://packages.microsoft.com/keys/microsoft.asc | gpg --dearmor -o /usr/share/keyrings/microsoft-prod.gpg \
    && curl -fsSL https://packages.microsoft.com/config/debian/12/prod.list > /etc/apt/sources.list.d/mssql-release.list \
    && apt-get update \
    && ACCEPT_EULA=Y apt-get install -y msodbcsql18 mssql-tools18 unixodbc-dev

# Install PHP extensions (including SQL Server drivers)
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install gd
RUN docker-php-ext-install zip
RUN pecl install sqlsrv
RUN pecl install pdo_sqlsrv
RUN docker-php-ext-enable sqlsrv pdo_sqlsrv

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

# Install composer dependencies (Opsional, jika vendor tidak di-commit)
# RUN composer install --no-dev --optimize-autoloader

EXPOSE 80
CMD ["apache2-foreground"]
