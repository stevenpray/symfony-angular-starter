FROM symfonyangularstarter_debian

RUN echo "deb https://nginx.org/packages/mainline/debian/ stretch nginx" >> /etc/apt/sources.list
RUN apt-key adv --keyserver keyserver.ubuntu.com --recv-keys ABF5BD827BD9BF62

RUN apt-get update

RUN apt-get install --assume-yes --no-install-recommends --no-install-suggests \
    nginx

RUN apt-get purge --assume-yes --auto-remove \
    --option APT::AutoRemove::RecommendsImportant=false \
    --option APT::AutoRemove::SuggestsImportant=false
RUN rm -rf /var/lib/apt/lists/*

RUN mkdir -p /var/log/nginx
