# Usar imagen oficial de PHP con Apache
FROM php:8.2-apache

# Instalar dependencias del sistema y extensiones de PostgreSQL para Supabase
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# Habilitar mod_rewrite de Apache para rutas limpias
RUN a2enmod rewrite

# Copiar el código del proyecto al directorio web de Apache
COPY . /var/www/html/

# Ajustar los permisos de los archivos
RUN chown -R www-data:www-data /var/www/html

# Exponer el puerto predeterminado de Apache
EXPOSE 80
