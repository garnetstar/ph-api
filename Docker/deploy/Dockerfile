FROM limogin/php-7.1-apache

RUN mkdir /api

WORKDIR /api

# copy and prepare source code
COPY src/api/Docker/deploy/src .
RUN mkdir -p ./temp/cache/doctrine
RUN chgrp www-data -R ./temp
RUN chmod g+rwx -R ./temp

# make apache runnable
COPY src/api/Docker/default.conf /etc/apache2/sites-available/pg-api.conf
RUN rm /etc/apache2/sites-enabled/*
RUN ln -s /etc/apache2/sites-available/pg-api.conf /etc/apache2/sites-enabled/pg-api.conf