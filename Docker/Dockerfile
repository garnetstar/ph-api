FROM php:8.0.3-apache-buster as base

RUN a2enmod rewrite

RUN \
 usermod -u 1000 www-data && \
 groupmod -g 1000 www-data && \
 usermod -d /home/www-data -s /bin/bash www-data

RUN \
a2enmod rewrite && \
apt-get update && \
apt-get install -y vim && \
docker-php-ext-install pdo_mysql && \
rm -rf \
    /tmp/* \
    /var/lib/apt/lists/* \
    /var/tmp/*

RUN mkdir /api
WORKDIR /api

FROM base as composer

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
  php composer-setup.php --install-dir=/usr/bin --filename=composer && \
  rm composer-setup.php && \
  apt-get update && \
  apt-get install -y --no-install-recommends \
  git && \
rm -rf \
    /tmp/* \
    /var/lib/apt/lists/* \
    /var/tmp/*

FROM base as base-with-apache

RUN mkdir -p ./temp/cache/doctrine
RUN chgrp www-data -R ./temp
RUN chmod g+rwx -R ./temp

RUN mkdir ./log
RUN chmod 777 ./log

# make apache runnable
COPY Docker/default.conf /etc/apache2/sites-available/pg-api.conf
RUN rm /etc/apache2/sites-enabled/*
RUN ln -s /etc/apache2/sites-available/pg-api.conf /etc/apache2/sites-enabled/pg-api.conf

FROM base-with-apache as prod

# copy and prepare source code
COPY src .

FROM base-with-apache as dev

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
  php composer-setup.php --install-dir=/usr/bin --filename=composer && \
  rm composer-setup.php && \
  apt-get update && \
  apt-get install -y --no-install-recommends \
  git && \
rm -rf \
    /tmp/* \
    /var/lib/apt/lists/* \
    /var/tmp/*
    
