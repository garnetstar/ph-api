FROM limogin/php-7.1-apache

#RUN apt-get update && \
#  apt-get install -y \
#    mc

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
  php composer-setup.php --install-dir=/usr/bin --filename=composer && \
  rm composer-setup.php

RUN mkdir /api

WORKDIR /api
