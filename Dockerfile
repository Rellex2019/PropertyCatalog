FROM php:8.4-fpm

# Установка зависимостей
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    sqlite3 \
    libsqlite3-dev \
    nodejs \
    npm \
    && docker-php-ext-install pdo_sqlite mbstring exif pcntl bcmath gd

RUN echo 'upload_max_filesize = 100M' > /usr/local/etc/php/conf.d/upload-limits.ini \
    && echo 'post_max_size = 100M' >> /usr/local/etc/php/conf.d/upload-limits.ini

# Устанавливаем Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /workspace

# Копируем файлы проекта
COPY . .

# Устанавливаем права
RUN chown -R www-data:www-data /workspace \
    && chmod -R 755 /workspace/storage \
    && chmod -R 755 /workspace/bootstrap/cache

# Устанавливаем зависимости PHP
RUN composer install --no-interaction --optimize-autoloader

# Устанавливаем зависимости Node.js и собираем фронтенд
RUN npm install && npm run build || true


# Генерируем ключ
RUN php artisan key:generate || true

# Очищаем кэш
RUN php artisan config:clear \
    && php artisan route:clear \
    && php artisan cache:clear

EXPOSE 8000

# Запускаем с правильным хостом
CMD php artisan serve --host=0.0.0.0 --port=8000