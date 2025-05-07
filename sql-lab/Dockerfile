# broken-login-cdn/Dockerfile

FROM php:8.2-apache

# Install system dependencies required for pdo_sqlite and other common extensions
# - libsqlite3-dev: for pdo_sqlite
# - $PHPIZE_DEPS: a meta-package including build-essential, autoconf, etc., needed by pecl and docker-php-ext-install
# - git, zip, unzip are common utilities often useful
RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    $PHPIZE_DEPS \
    git \
    zip \
    unzip \
    && rm -rf /var/lib/apt/lists/* # Clean up apt cache

# Install PDO and SQLite3 PHP extensions
RUN docker-php-ext-install pdo pdo_sqlite

# --- Security: Place flag outside web root ---
RUN mkdir -p /app_secrets
COPY html/flag.txt /app_secrets/flag.txt
# Optional: set stricter permissions if needed
# RUN chown www-data:www-data /app_secrets/flag.txt && chmod 440 /app_secrets/flag.txt

# Remove default Apache index.html if it exists
RUN rm -f /var/www/html/index.html

# Copy application files into the web root
COPY html/index.php /var/www/html/
COPY html/init_db.php /var/www/html/

# Create the db directory and set permissions (Apache runs as www-data)
# PHP script (init_db.php) will create database.sqlite inside this dir.
RUN mkdir -p /var/www/html/db && chown www-data:www-data /var/www/html/db

# Run DB initialization script as www-data to ensure correct permissions for SQLite file
# This script will be executed when the container builds
USER www-data
RUN php /var/www/html/init_db.php
USER root

# Apache is started by the base image's default CMD
# Expose port 80 (Apache default)
EXPOSE 80
