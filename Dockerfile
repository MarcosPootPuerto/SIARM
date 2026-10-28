FROM php:8.2-apache

# Instalar extensiones de PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql \
    && rm -rf /var/lib/apt/lists/*

# Habilitar mod_rewrite, headers y deflate para compresión y caché
RUN a2enmod rewrite headers deflate

# Habilitar OPcache para acelerar la ejecución de código PHP
RUN docker-php-ext-install opcache

COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
