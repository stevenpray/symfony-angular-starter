#!/usr/bin/env bash

APP_ENV=${APP_ENV:=dev}

cd /srv/client
yarn install --ignore-engines
npm rebuild node-sass

if [ "$APP_ENV" = "dev" ]; then
    node_modules/.bin/ng serve --env="${APP_ENV}" --host=0.0.0.0 --port=4200 --disable-host-check
fi
