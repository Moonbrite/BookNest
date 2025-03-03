# Utiliser l'image PHP officielle avec Apache
FROM php:8.2-apache

# Activer mod_rewrite pour Laravel
RUN a2enmod rewrite

# Installer les extensions PHP nécessaires
RUN docker-php-ext-install pdo pdo_mysql

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier les fichiers du projet dans le conteneur
COPY . /var/www/html

# Modifier la configuration Apache pour AllowOverride All
RUN sed -i 's|AllowOverride None|AllowOverride All|' /etc/apache2/apache2.conf

# Vérifier et créer le fichier .htaccess dans public/
RUN touch /var/www/html/public/.htaccess && \
    echo '<IfModule mod_rewrite.c>\n\
    RewriteEngine On\n\
    RewriteCond %{REQUEST_FILENAME} !-f\n\
    RewriteCond %{REQUEST_FILENAME} !-d\n\
    RewriteRule ^ index.php [L]\n\
</IfModule>' > /var/www/html/public/.htaccess

# Donner les permissions nécessaires à Laravel
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Exposer le port 80
EXPOSE 80
