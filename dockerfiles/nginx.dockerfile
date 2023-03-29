FROM nginx:1-perl

WORKDIR /etc/nginx/conf.d

COPY nginx/nginx.conf .

# rename
RUN mv nginx.conf default.conf

WORKDIR /var/www/html

COPY app .
