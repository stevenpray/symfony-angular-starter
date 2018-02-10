FROM symfonyangularstarter_debian

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV WKHTMLTOPDF_VERSION=0.12.4

RUN echo "deb https://packages.sury.org/php/ stretch main" >> /etc/apt/sources.list
RUN apt-key adv --keyserver keyserver.ubuntu.com --recv-keys AC0E47584A7A714D

RUN apt-get update

RUN apt-get install --assume-yes --no-install-recommends --no-install-suggests \
    php-apcu \
    php-geoip \
    php-memcached \
    php-msgpack \
    php-xdebug \
    php-yaml \
    php7.2-bcmath \
    php7.2-bz2 \
    php7.2-cli \
    php7.2-common \
    php7.2-curl \
    php7.2-fpm \
    php7.2-gd \
    php7.2-intl \
    php7.2-json \
    php7.2-mbstring \
    php7.2-opcache \
    php7.2-pgsql \
    php7.2-readline \
    php7.2-soap \
    php7.2-xml \
    php7.2-zip

RUN apt-get install --assume-yes --no-install-recommends --no-install-suggests \
    libfontconfig \
    libxext-dev \
    libxrender1

RUN curl -LS https://github.com/wkhtmltopdf/wkhtmltopdf/releases/download/${WKHTMLTOPDF_VERSION}/wkhtmltox-${WKHTMLTOPDF_VERSION}_linux-generic-amd64.tar.xz \
    | tar xJv -C /usr/local --strip-components=1

RUN mkdir -p /var/log/php

RUN curl -LS https://getcomposer.org/installer \
    | php -- --install-dir=/usr/local/bin --filename=composer

RUN apt-get purge --assume-yes --auto-remove \
    --option APT::AutoRemove::RecommendsImportant=false \
    --option APT::AutoRemove::SuggestsImportant=false
RUN rm -rf /var/lib/apt/lists/*
