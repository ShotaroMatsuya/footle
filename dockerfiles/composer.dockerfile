FROM composer:2

WORKDIR /var/www/html

# we can run this without any warnings or errors even if some dependencies would be missing.
ENTRYPOINT [ "composer", "--ignore-platform-reqs" ]