# Use the official PHP 8.1 Apache image
FROM php:8.1-apache

# Install required extensions
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Enable Apache modules
RUN a2enmod rewrite \
    && a2enmod headers \
    && a2enmod expires

# Set ServerName to localhost
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Create public directory
RUN mkdir -p /var/www/html/public

# Copy only necessary files to keep the image small
COPY public/ /var/www/html/public/
COPY includes/ /var/www/html/includes/
COPY config/ /var/www/html/config/

# Copy configuration files
COPY .htaccess /var/www/html/
COPY public/.htaccess /var/www/html/public/
COPY apache-config.conf /etc/apache2/conf-available/000-default.conf

# Set working directory
WORKDIR /var/www/html

# Change document root for Apache
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
