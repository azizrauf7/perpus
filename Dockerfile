FROM php:8.2-fpm-alpine

RUN docker-php-ext-install mysqli pdo pdo_mysql && \
    apk add --no-cache nginx supervisor bash

COPY . /var/www/html/
COPY nginx.conf /etc/nginx/http.d/default.conf
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Fix permissions & ensure directories exist
RUN mkdir -p /var/www/html && \
    mkdir -p /var/run/nginx && \
    mkdir -p /var/log/supervisor && \
    chown -R www-data:www-data /var/www/html && \
    nginx -t

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]