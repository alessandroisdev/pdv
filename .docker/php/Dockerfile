FROM php:8.4-fpm-alpine

# Install dependencies
RUN apk add --no-cache \
    linux-headers \
    git \
    nano \
    zip \
    unzip \
    libzip-dev \
    libpng-dev \
    sqlite-dev \
    oniguruma-dev \
    nodejs \
    npm \
    libxml2-dev

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring pcntl bcmath gd zip mysqli pdo_sqlite soap

# Install Redis extension
RUN apk add --no-cache pcre-dev $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
