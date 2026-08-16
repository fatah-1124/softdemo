FROM php:8.2-apache

# Install library sistem yang dibutuhkan ext-gd
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Configure & install gd + extension lain
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo_mysql mysqli

# Install Composer (ambil binary-nya dari image resmi composer)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN a2enmod rewrite
COPY . /var/www/html
WORKDIR /var/www/html

# Install dependency PHP
RUN composer install --optimize-autoloader --no-dev --no-interaction

COPY --from=frontend /app/dist /var/www/html/public/assets
RUN chmod -R 755 writable/
EXPOSE 80
CMD sed -i "s/80/$PORT/g" /etc/apache2/ports.conf /etc/apache2/sites-enabled/000-default.conf && apache2-foreground

