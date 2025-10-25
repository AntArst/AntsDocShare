FROM php:8.4.14-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    zip \
    unzip

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files
COPY composer.json composer.lock* ./

# Install PHP dependencies
RUN composer install --no-scripts --no-autoloader --no-dev || true

# Copy application files
COPY . .

# Generate autoloader
RUN composer dump-autoload --optimize

# Create storage directories with proper permissions
RUN mkdir -p storage/assets storage/packages storage/temp \
    && chmod -R 775 storage \
    && chown -R www-data:www-data storage

# Expose port 9000 for PHP-FPM
EXPOSE 9000

CMD ["php-fpm"]

