# syntax=docker/dockerfile:1

# ─── STAGE 1: instalar dependencias de Composer ───────────────────────────────
# Usamos la imagen oficial de Composer para instalar paquetes.
# Este stage se descarta al final — solo nos llevamos la carpeta vendor/
FROM composer:lts AS deps

# Carpeta de trabajo dentro del contenedor (como hacer cd /app)
WORKDIR /app

# Instala las dependencias de PHP definidas en composer.json
# --mount=bind: lee composer.json y composer.lock sin copiarlos dentro del contenedor
# --mount=cache: guarda los paquetes descargados en caché para no volver a descargarlos
# --no-scripts: no ejecuta scripts post-instalación (artisan no existe en este stage)
RUN --mount=type=bind,source=composer.json,target=composer.json \
    --mount=type=bind,source=composer.lock,target=composer.lock \
    --mount=type=cache,target=/tmp/cache \
    composer install --no-interaction --no-scripts


# ─── STAGE 2: imagen final con PHP + Apache ───────────────────────────────────
# Empezamos desde cero con PHP 8.3 + Apache. Lo del stage anterior queda descartado
# excepto la carpeta vendor/ que copiamos más abajo con COPY --from=deps
FROM php:8.3-apache AS final

# Instala las extensiones de PHP que necesita Laravel
# pdo_mysql: para conectar con MySQL
# mbstring, zip, bcmath: requeridas por Laravel y sus dependencias
RUN apt-get update && apt-get install -y \
    libzip-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring zip bcmath \
    && rm -rf /var/lib/apt/lists/*
# rm -rf /var/lib/apt/lists/* limpia la caché de apt para que la imagen pese menos

# Activa mod_rewrite de Apache — necesario para que funcionen las rutas de Laravel
RUN a2enmod rewrite

# Le dice a Apache que sirva desde public/ en vez de la raíz del proyecto
# Laravel solo expone public/index.php al exterior, el resto es privado
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -i 's|/var/www/html|${APACHE_DOCUMENT_ROOT}|g' /etc/apache2/sites-available/000-default.conf \
    && sed -i 's|/var/www/html|${APACHE_DOCUMENT_ROOT}|g' /etc/apache2/apache2.conf

# Usa la configuración de PHP para producción (más estricta y segura)
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Copia la carpeta vendor/ del stage anterior (deps)
# --from=deps significa "coge esto del stage llamado deps, no de mi carpeta local"
COPY --from=deps /app/vendor/ /var/www/html/vendor

# Copia todo el código de la app dentro del contenedor
COPY ./ /var/www/html

# Crea el .env a partir del .env.example y da permisos de escritura
RUN cp /var/www/html/.env.example /var/www/html/.env \
    && chown www-data:www-data /var/www/html/.env \
    && chmod 664 /var/www/html/.env

# Da permisos de escritura a Apache sobre las carpetas que Laravel necesita escribir
# storage/: logs, caché de vistas, archivos subidos
# bootstrap/cache/: caché de configuración
RUN chown -R www-data:www-data /var/www/html

# Copia el script de arranque y le da permisos de ejecución
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# A partir de aquí todo se ejecuta como www-data (usuario de Apache), no como root
# Buena práctica de seguridad: nunca correr la app como root
USER www-data

# Cuando el contenedor arranque, ejecuta el entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]
