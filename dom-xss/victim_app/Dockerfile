FROM php:8.2-apache

# Copy application files
COPY public/ /var/www/html/
COPY php.ini /usr/local/etc/php/conf.d/custom-php.ini

# Ensure correct permissions (Apache usually runs as www-data)
RUN chown -R www-data:www-data /var/www/html && \
    a2enmod rewrite

# Expose port 80
EXPOSE 80
