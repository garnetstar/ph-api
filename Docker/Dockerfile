FROM garnetstar/pg-api-template:php7.2

RUN mkdir /api

WORKDIR /api

# copy and prepare source code
COPY src .
RUN mkdir -p ./temp/cache/doctrine
RUN chgrp www-data -R ./temp
RUN chmod g+rwx -R ./temp

RUN mkdir ./log
RUN chmod g+rwx ./log

# make apache runnable
COPY Docker/default.conf /etc/apache2/sites-available/pg-api.conf
RUN rm /etc/apache2/sites-enabled/*
RUN ln -s /etc/apache2/sites-available/pg-api.conf /etc/apache2/sites-enabled/pg-api.conf