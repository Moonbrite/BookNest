# Utiliser l'image PHP officielle avec Apache
FROM php:8.2-apache

# Installer les dépendances système nécessaires
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install zip pdo pdo_mysql gd

# Activer mod_rewrite pour Laravel
RUN a2enmod rewrite

# Configurer le DocumentRoot d'Apache pour pointer vers le dossier public de Laravel
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# Ajouter la configuration pour le répertoire public
RUN echo '<Directory /var/www/html/public>\n\
    AllowOverride All\n\
    Require all granted\n\
    Options Indexes FollowSymLinks\n\
    </Directory>' >> /etc/apache2/sites-available/000-default.conf

# Définir le répertoire de travail
WORKDIR /var/www/html

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copier les fichiers du projet dans le conteneur
COPY . /var/www/html

# Installer les dépendances de Laravel avec Composer
RUN composer install --no-interaction --optimize-autoloader

# Copier le fichier .env.example en .env si nécessaire
RUN if [ -f .env.example ] && [ ! -f .env ]; then cp .env.example .env; fi

# Générer la clé d'application Laravel
RUN php artisan key:generate --force

# Donner les permissions nécessaires à Laravel
RUN mkdir -p storage/logs \
    && touch storage/logs/laravel.log \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

RUN composer require laravel/breeze --dev

RUN php artisan breeze:install api

RUN composer require --dev nunomaduro/larastan

RUN php artisan l5-swagger:generate

# Exposer le port 80
EXPOSE 80
