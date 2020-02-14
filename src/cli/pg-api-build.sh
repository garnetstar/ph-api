#!/usr/bin/env bash
# Bash3 Boilerplate. Copyright (c) 2014, kvz.io
# https://kvz.io/blog/2013/11/21/bash-best-practices/

set -o errexit
set -o pipefail
set -o nounset

export IMAGE_NAME=pg-api-build:php7.2
docker build -f ../../Docker/Dockerfile-build -t garnetstar/$IMAGE_NAME .
docker login -u garnetstar docker.io
docker push garnetstar/$IMAGE_NAME

