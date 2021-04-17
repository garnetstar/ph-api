#!/bin/bash

# build image with git and compoer
docker build --target composer -t pn-api:composer -f Docker/Dockerfile .

# install dependencies by composer
docker run -it -v $(pwd)/src/:/api/src/ pn-api:composer bash -c 'cd src && composer install'

# build prod image
docker build --target prod -t pn-api:v1 -f Docker/Dockerfile .  

#run prod image
docker run -d --rm --name pn-api -p 88:80 pn-api:v1 
