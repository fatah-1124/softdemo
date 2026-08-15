# Stage 1: build Vue
FROM node:20 AS frontend
WORKDIR /app
COPY frontend/ .
RUN npm install && npm run build

# Stage 2: PHP + Apache
FROM php:8.2-apache
RUN docker-php-ext-install pdo_mysql mysqli
RUN a2enmod rewrite
COPY . /var/www/html
COPY --from=frontend /app/dist /var/www/html/public/assets
WORKDIR /var/www/html
RUN chmod -R 755 writable/
EXPOSE 80
CMD sed -i "s/80/$PORT/g" /etc/apache2/ports.conf /etc/apache2/sites-enabled/000-default.conf && apache2-foreground