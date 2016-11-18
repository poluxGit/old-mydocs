FROM ubuntu:16.04


MAINTAINER Polux <polux@poluxfr.org>

# Modules installation
RUN apt-get update && apt-get install -y --force-yes \
      vim \
      curl \
      apache2 \
      php7.0 \
      php7.0-cli \
      libapache2-mod-php7.0 \
      php7.0-gd \
      php7.0-json \
      php7.0-ldap \
      php7.0-mbstring \
      php7.0-mysql \
      php7.0-pgsql \
      php7.0-sqlite3 \
      php7.0-xml \
      php7.0-xsl \
      php7.0-zip \
      php7.0-soap \
      tesseract-ocr \
      tesseract-ocr-fra \
      tesseract-ocr-eng \
      poppler-utils \
      php-xdebug

USER root

# Prepare directories for logs && share
RUN mkdir -p /var/log/php/ \
  && mkdir -p /var/www/html/php-mydocs \
  && mkdir -p /var/www/html/php-myged \
  && mkdir -p /var/www/vault \
  && chmod -R 777 /var/log/php/

# PHP Composer installation
RUN cd /tmp && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# PHP 7.0 - Configuration files
COPY ./01-apache2.conf /etc/apache2/apache2.conf
COPY ./05-php.ini /etc/php/7.0/apache2/php.ini
COPY ./10-xdebug.ini /etc/php/7.0/apache2/conf.d/20-xdebug.ini
COPY ./02-000-default.conf /etc/apache2/sites-available/000-default.conf

RUN a2enmod rewrite

EXPOSE 9000 80

ENTRYPOINT ["/usr/sbin/apache2ctl", "-D", "FOREGROUND"]
