# pingmaster_scattered_secrets/Dockerfile
FROM php:8.2-apache

# Install ping utility
RUN apt-get update && apt-get install -y iputils-ping && \
    rm -rf /var/lib/apt/lists/*

# Copy clue files into a temporary location in the image
WORKDIR /app_clues
COPY flag_parts/clue1_in_tmp.txt .
COPY flag_parts/final_flag_location.txt .

# Copy the setup script and make it executable
COPY entrypoint_setup.sh /usr/local/bin/entrypoint_setup.sh
RUN chmod +x /usr/local/bin/entrypoint_setup.sh

# Copy the web application files
COPY html/ /var/www/html/

# No need to copy flag.txt to / as it's now part of the clue hunt

EXPOSE 80

# The entrypoint_setup.sh will run first, then exec the default CMD of php:apache image
ENTRYPOINT ["/usr/local/bin/entrypoint_setup.sh"]
CMD ["apache2-foreground"]
