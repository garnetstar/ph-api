FROM php:7.2-apache

RUN a2enmod rewrite

RUN apt-get update && \
  apt-get install -y \
    mc

RUN pecl install xdebug && docker-php-ext-enable xdebug
RUN docker-php-ext-install pdo_mysql

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
  php composer-setup.php --install-dir=/usr/bin --filename=composer && \
  rm composer-setup.php

RUN mkdir /api

WORKDIR /api
