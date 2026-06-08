FROM php:8.2-fpm-alpine

# Installer les dépendances système
RUN apk add --no-cache \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev

# Configurer et installer les extensions PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install \
        pdo \
        pdo_mysql \
        mysqli \
        gd

# Définir le répertoire de travail
WORKDIR /var/www/html

# Définir les permissions appropriées
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# Copy custom PHP-FPM configuration (enables environment variables)
COPY www.conf /usr/local/etc/php-fpm.d/www.conf

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD php -r "if (@fsockopen('localhost', 9000)) { exit(0); } exit(1);" || exit 1