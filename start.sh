#!/bin/sh
set -e

echo "Starting PHP-FPM..."
php-fpm -D
sleep 2

echo "Starting Nginx..."
nginx -g "daemon off;"