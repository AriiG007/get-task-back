FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# cambiar el directorio de trabajo a /opt/data
WORKDIR /opt/data

# copiar el proyecto a /opt/data
COPY . /opt/data

# copiar y ejecutar el script de inicialización
COPY docker/init.sh /var/www/docker/init.sh

RUN chmod +x /var/www/docker/init.sh \
    && /var/www/docker/init.sh

# ccrear enlace simbólico de /opt/data como /var/www/html (para compatibilidad con nginx)
RUN rm -rf /var/www/html && ln -s /opt/data /var/www/html


RUN php artisan storage:link || true


EXPOSE 9000


CMD ["php-fpm"]
