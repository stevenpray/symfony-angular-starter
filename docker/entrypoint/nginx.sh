#!/usr/bin/env bash

APP_ENV=${APP_ENV:=dev}

mkdir -p /etc/nginx/sites-enabled
cd /etc/nginx/sites-enabled
ln -fs ../sites-available/${APP_ENV} .

if [ "$APP_ENV" != "prod" ]; then
    ln -sf /dev/stdout /var/log/nginx/access.log
    ln -sf /dev/stderr /var/log/nginx/error.log

    ln -fs ../sites-available/dev .

    dockerize -wait tcp://node:"${ANGULAR_PORT}" -timeout 600s
fi

dockerize -wait tcp://php:"${PHP_FPM_PORT}" -timeout 600s

nginx
