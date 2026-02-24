FROM php:8.2-fpm-alpine

RUN docker-php-ext-install mysqli pdo pdo_mysql && \
    apk add --no-cache nginx bash

COPY . /var/www/html/
COPY nginx.conf /etc/nginx/http.d/default.conf
COPY start.sh /start.sh

# Fix permissions & ensure directories exist
RUN mkdir -p /var/www/html && \
    mkdir -p /var/run/nginx && \
    chown -R www-data:www-data /var/www/html && \
    chmod +x /start.sh && \
    nginx -t

EXPOSE 80

CMD ["/start.sh"]