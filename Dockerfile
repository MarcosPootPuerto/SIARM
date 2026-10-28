# Usar imagen oficial de PHP 8.2 con Apache
FROM php:8.2-apache

# Instalar dependencias del sistema y extensiones de PostgreSQL para Supabase
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql \
    && rm -rf /var/lib/apt/lists/*

# Configurar Apache para interpretar archivos .php y usar index.php por defecto
RUN echo "DirectoryIndex index.php index.html" > /etc/apache2/conf-available/docker-php.conf \
    && a2enconf docker-php

# Habilitar mod_rewrite de Apache
RUN a2enmod rewrite

# Copiar el código del proyecto al directorio web de Apache
COPY . /var/www/html/

# Ajustar permisos para el servidor web
RUN chown -R www-data:www-data /var/www/html

# Exponer el puerto 80
EXPOSE 80
