FROM nginx:1.23.3-alpine

WORKDIR /etc/nginx/conf.d

COPY nginx/nginx.conf .

# rename
RUN mv nginx.conf default.conf

WORKDIR /var/www/html

COPY app .
