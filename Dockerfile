FROM php:8.2-fpm-alpine

RUN docker-php-ext-install mysqli pdo pdo_mysql && \
    apk add --no-cache nginx bash

COPY . /var/www/html/
COPY start.sh /start.sh

# Create nginx config
RUN mkdir -p /etc/nginx/http.d && \
    echo 'server {' > /etc/nginx/http.d/default.conf && \
    echo '    listen 80;' >> /etc/nginx/http.d/default.conf && \
    echo '    root /var/www/html;' >> /etc/nginx/http.d/default.conf && \
    echo '    index index.php index.html;' >> /etc/nginx/http.d/default.conf && \
    echo '    location / { try_files $uri $uri/ /index.php?$query_string; }' >> /etc/nginx/http.d/default.conf && \
    echo '    location ~ \.php$ { fastcgi_pass 127.0.0.1:9000; fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name; include fastcgi_params; }' >> /etc/nginx/http.d/default.conf && \
    echo '}' >> /etc/nginx/http.d/default.conf

# Fix permissions
RUN chown -R www-data:www-data /var/www/html && \
    chmod +x /start.sh

EXPOSE 80

CMD ["/start.sh"]