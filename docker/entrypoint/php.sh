#!/usr/bin/env bash

APP_ENV=${APP_ENV:=dev}

cd "$SYMFONY_DIR"
mkdir -p var
chmod -R go+w var/

if [ "$APP_ENV" = "prod" ]; then
    cd /etc/php/7.2/cli/conf.d && ln -fs ../../mods-available/20-opcache.ini .
    cd /etc/php/7.2/fpm/conf.d && ln -fs ../../mods-available/20-opcache.ini .
    cd "$SYMFONY_DIR"

    composer install --no-suggest --no-plugins --optimize-autoloader --apcu-autoloader

    dockerize -wait tcp://"$MEMCACHED_HOST":"$MEMCACHED_PORT" -timeout 300s
    bin/console doctrine:cache:clear-metadata --no-interaction
    bin/console doctrine:cache:clear-query --no-interaction
    bin/console doctrine:cache:clear-result --no-interaction

    dockerize -wait tcp://"$DATABASE_HOST":"$DATABASE_PORT" -timeout 300s

else
    ln -sf /dev/stdout /var/log/php/access.log
    ln -sf /dev/stderr /var/log/php/error.log

    composer install --no-interaction --no-suggest

    dockerize -wait tcp://"$DATABASE_HOST":"$DATABASE_PORT" -timeout 300s
    bin/console doctrine:schema:create -vv --no-interaction 2> /dev/null
    if [ $? -eq 0 ]; then
        echo "Loading fixtures..."
        bin/console hautelook:fixtures:load -vv --no-interaction
    else
        bin/console doctrine:schema:validate -vv --no-interaction
        if [ $? -ne 0 ]; then
            bin/console doctrine:schema:update --force -vv --no-interaction
        fi
    fi
fi

bin/console cache:warmup

php-fpm7.2
