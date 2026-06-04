FROM php:8.2-apache

# Enable mysqli and pdo_mysql
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copy project files to Apache web root
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html

# Give proper permissions
RUN chown -R www-data:www-data /var/www/html