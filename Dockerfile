# PHP 8.3 with Laravel 11 optimized image
FROM php:8.3-fpm

# Install system dependencies (without nodejs/npm - will use node container)
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# InstallPHP extensions required by Laravel 11
RUN docker-php-ext-install pdo_pgsql pgsql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy PHP configuration
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/zz-custom.conf

# Set working directory
WORKDIR /var/www

# Expose port 9000 for PHP-FPM
EXPOSE 9000

CMD ["php-fpm"]
