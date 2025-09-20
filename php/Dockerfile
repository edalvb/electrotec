FROM php:8.1-apache

# instalar cliente mysql y extensiones necesarias (pdo_mysql, mysqli, zip)
RUN apt-get update \
  && apt-get install -y --no-install-recommends default-mysql-client libzip-dev zip unzip \
  && docker-php-ext-configure zip \
  && docker-php-ext-install pdo_mysql mysqli zip \
  && a2enmod rewrite \
  && apt-get clean \
  && rm -rf /var/lib/apt/lists/*

# Instalar Composer (desde imagen oficial)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]
