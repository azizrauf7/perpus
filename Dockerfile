FROM php:8.2-apache

RUN docker-php-ext-install mysqli pdo pdo_mysql \
    && a2enmod rewrite \
    && a2dismod mpm_event mpm_worker \
    && a2enmod mpm_prefork

COPY . /var/www/html/

EXPOSE 80
