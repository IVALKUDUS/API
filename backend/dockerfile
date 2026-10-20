# 1. gunakan image dasar PHP versi CLI
FROM php:8.4-cli

# 2. install depedensi sistem dasar dan extensi PHP
RUN apt-get update&& apt-get install -y libzip-dev zip unzip && docker-php-ext-install pdo pdo_mysql zip

# 3, install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. tetapkan direktori kerja utama di dalam container
WORKDIR /var/www