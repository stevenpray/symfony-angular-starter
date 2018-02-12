#!/usr/bin/env bash

APP_ENV=${APP_ENV:=dev}

if [ "$APP_ENV" = "prod" ]; then
    cd /etc/php/7.2/cli/conf.d && ln -fs ../../mods-available/20-opcache.ini .
    cd /etc/php/7.2/fpm/conf.d && ln -fs ../../mods-available/20-opcache.ini .
else
    ln -sf /dev/stdout /var/log/php/access.log
    ln -sf /dev/stderr /var/log/php/error.log
fi

cd /srv/server
mkdir -p var
chmod -R go+w var/

if [ "$APP_ENV" = "prod" ]; then
    composer install --no-suggest --optimize-autoloader --apcu-autoloader

    bin/console doctrine:cache:clear-metadata --no-interaction
    bin/console doctrine:cache:clear-query --no-interaction
    bin/console doctrine:cache:clear-result --no-interaction
else
    composer install --no-interaction --no-suggest
fi

dockerize -wait tcp://postgres:5432 -timeout 600s

if [ "$APP_ENV" != "prod" ]; then
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

if [ "$APP_ENV" = "test" ]; then
    vendor/bin/phpunit
else
    php-fpm7.2
fi
