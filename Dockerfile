# Image de base : PHP avec serveur Apache
FROM php:8.2-apache

# 1. Installation des extensions nécessaires pour MySQL et GD (pour vos images/photos)
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql gd

# 2. Activation du module de réécriture (nécessaire pour votre .htaccess)
RUN a2enmod rewrite

# 3. Copie du code source dans le dossier du serveur web
COPY . /var/www/html/

