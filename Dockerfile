FROM php:8.2-cli

WORKDIR /app

# Install dependencies
RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev zip libpq-dev \
    && docker-php-ext-install zip pdo pdo_mysql pdo_pgsql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy project
COPY . .

# Install Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# Generate app key (safe)
RUN php artisan key:generate

# Cache config (important for production)
RUN php artisan config:cache

# Expose port
EXPOSE 10000

# Run migrations + start server
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=10000