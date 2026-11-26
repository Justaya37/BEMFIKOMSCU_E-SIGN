FROM php:8.4-cli

# Install ekstensi PHP (GD untuk cap/PDF, driver MySQL/Postgres, dll) + Node.js 22 untuk Vite
RUN apt-get update && apt-get install -y \
    git curl unzip zip \
    libpng-dev libjpeg-dev libfreetype6-dev libonig-dev libxml2-dev libzip-dev libpq-dev \
    && curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

# Install dependency PHP & compile asset Vite
RUN composer install --no-dev --optimize-autoloader --no-interaction \
    && npm install \
    && rm -f public/hot \
    && npm run build

RUN chmod -R 775 storage bootstrap/cache

ENV PHP_CLI_SERVER_WORKERS=4
EXPOSE 8080

CMD ["sh", "-c", "mkdir -p /app/storage/app/public /app/storage/app/private/assets /app/storage/app/private/certs /app/storage/app/private/drafts /app/storage/app/private/signatures /app/storage/app/private/signed && chmod -R 775 /app/storage /app/bootstrap/cache && rm -rf /app/public/storage && php artisan storage:link && php artisan esign:init-storage && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=${PORT:-8080}"]
