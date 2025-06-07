#!/bin/bash

echo "Iniciando script init.sh"

# Validar y generar directorio de logs docker
if [ -d "/opt/logs/docker" ]; then
    rm -f /opt/logs/docker/*
else
    mkdir -p /opt/logs/docker
fi

LOGFILE="/opt/logs/docker/docker_run.log"
echo "=====================================" >> "$LOGFILE"
echo "Starting process" >> "$LOGFILE"


echo "Verify if vendor folder exists, if so we remove it" >> "$LOGFILE"
[ -d vendor ] && rm -Rf vendor


echo "Running composer" >> "$LOGFILE"
composer install --no-interaction --prefer-dist --optimize-autoloader >> "$LOGFILE" 2>&1


# Ejecutar migraciones
echo "Running migrations" >> "$LOGFILE"
php artisan migrate --force >> "$LOGFILE" 2>&1

# Ejecutar seeders
echo "Running seeders" >> "$LOGFILE"
php artisan db:seed --force >> "$LOGFILE" 2>&1

# Crear directorios del framework
echo "Creating framework folders" >> "$LOGFILE"
mkdir -p /opt/data/storage/framework/{sessions,views,cache}


echo "Finished process"  >> "$LOGFILE"

echo "=====================================" >> "$LOGFILE"


