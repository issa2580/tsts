FROM php:7.4-apache

COPY . /var/www/html

RUN docker-php-ext-install pdo_mysql

RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

WORKDIR /var/www/html

RUN chmod -R 777 /var/www/html/

RUN a2enmod rewrite
