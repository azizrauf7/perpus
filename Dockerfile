FROM php:8.2-fpm-alpine

RUN docker-php-ext-install mysqli pdo pdo_mysql
RUN apk add --no-cache nginx

COPY . /var/www/html/

RUN echo 'server {' > /etc/nginx/http.d/default.conf \
    && echo '    listen 80;' >> /etc/nginx/http.d/default.conf \
    && echo '    root /var/www/html;' >> /etc/nginx/http.d/default.conf \
    && echo '    index index.php index.html;' >> /etc/nginx/http.d/default.conf \
    && echo '    location / { try_files $uri $uri/ /index.php?$query_string; }' >> /etc/nginx/http.d/default.conf \
    && echo '    location ~ \.php$ { fastcgi_pass 127.0.0.1:9000; fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name; include fastcgi_params; }' >> /etc/nginx/http.d/default.conf \
    && echo '}' >> /etc/nginx/http.d/default.conf

RUN echo '#!/bin/sh' > /start.sh \
    && echo 'php-fpm -D' >> /start.sh \
    && echo 'nginx -g "daemon off;"' >> /start.sh \
    && chmod +x /start.sh

EXPOSE 80

CMD ["/start.sh"]