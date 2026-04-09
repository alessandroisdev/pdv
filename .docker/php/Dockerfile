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

# Install smbclient for Windows Printer Support
RUN apk add --no-cache samba-client

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configure PHP uploads
RUN echo "upload_max_filesize=128M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size=128M" >> /usr/local/etc/php/conf.d/uploads.ini

WORKDIR /var/www/html
