FROM php:8.2-apache

# Install library sistem yang dibutuhkan
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libicu-dev \
    libzip-dev \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Pastikan hanya mpm_prefork yang aktif (dibutuhkan mod_php)
RUN a2dismod mpm_event mpm_worker 2>/dev/null; a2enmod mpm_prefork

# Configure & install extension
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo_mysql mysqli intl zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN a2enmod rewrite
COPY . /var/www/html
WORKDIR /var/www/html
RUN composer install --optimize-autoloader --no-dev --no-interaction
RUN chown -R www-data:www-data writable/ && chmod -R 775 writable/
EXPOSE 80

CMD ["bash", "-c", "\
    a2dismod mpm_event mpm_worker || true; \
    rm -f /etc/apache2/mods-enabled/mpm_event.* /etc/apache2/mods-enabled/mpm_worker.* || true; \
    a2enmod mpm_prefork; \
    sed -i \"s/80/$PORT/g\" /etc/apache2/ports.conf /etc/apache2/sites-enabled/000-default.conf; \
    apache2-foreground \
"]