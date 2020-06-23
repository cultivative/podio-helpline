FROM php:7.2-apache

COPY /src /var/www/html/src/
COPY /vendor /var/www/html/vendor/
COPY /config/podio-settings.php /var/www/html/config/

COPY /config/alias.conf /etc/apache2/mods-available/alias.conf