FROM php:8.2-cli

WORKDIR /app

# Install system dependencies + Node.js + MySQL driver
RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev zip \
    nodejs npm \
    && docker-php-ext-install zip pdo pdo_mysql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy project
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Build frontend (optional but safe if you use Vite)
RUN npm install && npm run build || true

# Fix permissions
RUN chmod -R 777 storage bootstrap/cache

# Expose Railway port
EXPOSE 8000

# Start app (migrate + serve)
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT