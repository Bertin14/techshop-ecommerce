FROM php:8.2-apache

# Disable conflicting MPM modules and enable the right one
RUN a2dismod mpm_event mpm_worker 2>/dev/null || true \
    && a2enmod mpm_prefork

# Enable mysqli and pdo_mysql
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable Apache rewrite module
RUN a2enmod rewrite

# Copy project files to Apache web root
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html

# Give proper permissions
RUN chown -R www-data:www-data /var/www/html