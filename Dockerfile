FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    zip \
    nodejs \
    npm \
    && docker-php-ext-install pdo pdo_mysql zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY package.json package-lock.json /var/www/html/

RUN npm install

COPY . /var/www/html

WORKDIR /var/www/html

RUN COMPOSER_ALLOW_SUPERUSER=1 composer install

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

RUN npm run build

EXPOSE 9000

CMD ["php-fpm"]
