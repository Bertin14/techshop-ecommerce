FROM ubuntu:22.04

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update && apt-get install -y \
    apache2 \
    php8.1 \
    php8.1-mysql \
    libapache2-mod-php8.1 \
    && apt-get clean

RUN a2enmod php8.1 rewrite

COPY . /var/www/html/
RUN rm -f /var/www/html/index.html

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 80

CMD ["apache2ctl", "-D", "FOREGROUND"]