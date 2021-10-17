FROM nginx:stable-alpine
WORKDIR /var/www/html
COPY docker/nginx-site.conf /etc/nginx/conf.d/default.conf
COPY docker/nginxindexfile ./public/index.php
