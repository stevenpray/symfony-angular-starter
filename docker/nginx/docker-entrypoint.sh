#!/usr/bin/env bash

APP_ENV=${APP_ENV:=dev}

mkdir -p /etc/nginx/sites-enabled
cd /etc/nginx/sites-enabled
ln -fs ../sites-available/${APP_ENV} .

if [ "$APP_ENV" != "prod" ]; then
    ln -fs /dev/stdout /var/log/nginx/access.log
    ln -fs /dev/stderr /var/log/nginx/error.log
fi

if [ "$APP_ENV" = "dev" ]; then
    dockerize -wait tcp://node:4200 -timeout 600s
fi

dockerize -wait tcp://php:9000 -timeout 600s

nginx
