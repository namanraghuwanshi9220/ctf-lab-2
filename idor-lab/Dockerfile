FROM php:8.2-apache

# Optional: Install any needed PHP extensions (none strictly needed for this basic example)
# RUN docker-php-ext-install some_extension

# Copy application files
COPY html/ /var/www/html/

# Ensure web server can write if needed (not for this example, but good practice if sessions/uploads)
# RUN chown -R www-data:www-data /var/www/html/user_albums 
# The above line is only if users could upload. For this read-only example, it's fine.

# Expose port 80
EXPOSE 80
