FROM php:8.4-cli

WORKDIR /var/www


RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    zip \
    curl \
    netcat-openbsd


RUN docker-php-ext-install pdo_mysql


COPY --from=composer:2 /usr/bin/composer /usr/bin/composer


COPY docker/wait-for-mysql.sh /wait-for-mysql.sh
RUN chmod +x /wait-for-mysql.sh


COPY src /var/www


RUN composer install


RUN chmod -R 777 storage bootstrap/cache

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]