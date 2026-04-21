FROM php:8.2-fpm

# Extensions PHP nécessaires pour MySQL
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Forcer UTF-8 dans PHP
RUN echo "default_charset = UTF-8" >> /usr/local/etc/php/php.ini && \
    echo "mbstring.internal_encoding = UTF-8" >> /usr/local/etc/php/php.ini

WORKDIR /var/www/html