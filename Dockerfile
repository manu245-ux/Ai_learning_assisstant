FROM php:8.2-apache

# Extensions used by the application:
# PDO/MySQL for the database, cURL for Gemini, mbstring/fileinfo/zlib for
# validation and PDF text extraction.
RUN docker-php-ext-install pdo pdo_mysql curl mbstring     && a2enmod rewrite headers     && sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

COPY . /var/www/html/

RUN mkdir -p /var/www/html/uploads     && chown -R www-data:www-data /var/www/html     && chmod -R 755 /var/www/html/uploads

# Railway/other PaaS platforms may inject PORT. Apache normally listens on 80.
RUN printf '%s\n'     '#!/bin/sh'     'set -eu'     'PORT="${PORT:-80}"'     'sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf'     'sed -i "s/:80>/:${PORT}>/" /etc/apache2/sites-enabled/000-default.conf'     'exec apache2-foreground'     > /usr/local/bin/start-apache.sh     && chmod +x /usr/local/bin/start-apache.sh

EXPOSE 80
CMD ["/usr/local/bin/start-apache.sh"]
