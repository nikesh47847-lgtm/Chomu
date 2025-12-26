FROM php:8.2-fpm-alpine

# -------------------------------
# System dependencies
# -------------------------------
RUN apk add --no-cache \
    nginx \
    curl-dev \
    libzip-dev \
    oniguruma-dev \
    libxml2-dev \
    build-base

# -------------------------------
# PHP extensions
# -------------------------------
RUN docker-php-ext-install curl

# -------------------------------
# Config files
# -------------------------------
COPY php.ini /usr/local/etc/php/php.ini
COPY nginx.conf /etc/nginx/nginx.conf

# -------------------------------
# App directory
# -------------------------------
WORKDIR /var/www/html

# -------------------------------
# COPY EVERYTHING NEEDED
# -------------------------------
# All PHP files (index.php, autog.php, autossh.php, ua.php, ua1.php, usaddress.php, genphone.php, etc.)
COPY *.php ./

# Any txt / json / data files (json.txt, payload.txt, etc.)
COPY *.txt ./
COPY *.json ./

# -------------------------------
# Permissions
# -------------------------------
RUN chown -R www-data:www-data /var/www/html

# -------------------------------
# Railway / Render HTTP port
# -------------------------------
EXPOSE 8080

# -------------------------------
# Start services
# -------------------------------
CMD sh -c "php-fpm & nginx -g 'daemon off;'"