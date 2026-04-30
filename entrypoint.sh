#!/bin/bash
# Le dice al sistema que este archivo es un script de bash

set -e
# Si cualquier comando falla, el script para inmediatamente (no sigue adelante)

php artisan key:generate --force
# Genera la APP_KEY de Laravel. Sin esta clave Laravel no arranca

php artisan migrate:fresh --seed --force
# Borra todas las tablas, las vuelve a crear y mete los datos de prueba (seeders)

php artisan passport:keys --force
# Genera las claves OAuth que necesita Laravel Passport para los tokens

apache2-foreground
# Arranca Apache en primer plano. "foreground" es importante:
# si Apache corriera en background el contenedor se apagaría pensando que no hay nada que hacer
