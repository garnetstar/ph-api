#!/bin/bash

# build image with git and compoer
docker build --target composer -t pn-api:composer -f Docker/Dockerfile .

# install dependencies by composer
docker run -it -v $(pwd)/src/:/api/src/ pn-api:composer bash -c 'cd src && composer install'

# build prod image
docker build --target prod -t pn-api:v1 -f Docker/Dockerfile .  

# build dev image
docker build --target dev -t pn-api:dev -f Docker/Dockerfile . 

# network 
docker network create pnet  

# database
docker run  --network pnet -p 3306:3306 --rm -v $(pwd)/db/:/db/ -d --name db -e MYSQL_ROOT_PASSWORD=xxx -e MYSQL_DATABASE=pn mariadb:10.2.26 
docker exec -it db bash -c "mysql -u root -pxxx < /db/init.sql"  

#run prod image
docker run --network pnet \
-e DB_PASSWORD=xxx \
-e DB_USER=root \
-e DB_HOST=db \
-e DB_NAME=pn \
-e GOOGLE_CLIENT_ID=192740429578-s8t31esln4b8ab64os5afg11imb93l35.apps.googleusercontent.com \
-d --rm --name pn-api -p 88:80 \
pn-api:v1 

#run dev image
docker run \
--network pnet \
-e DB_PASSWORD=xxx \
-e DB_USER=root \
-e DB_HOST=db \
-e DB_NAME=pn \
-e GOOGLE_CLIENT_ID=192740429578-s8t31esln4b8ab64os5afg11imb93l35.apps.googleusercontent.com \
-v $(pwd)/src:/api \
--rm -d --name pn-api -p 88:80 \
pn-api:dev

