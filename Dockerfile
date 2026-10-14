FROM php:8.3-apache
RUN docker-php-ext-install pdo_mysql

# --- Point Apache's DocumentRoot at /public (DO NOT DELETE) ---
# Without these lines Apache serves the whole project root, which (a) gives 403
# at localhost:8000 and (b) exposes /src, /config and .env to the browser.
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
 && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
