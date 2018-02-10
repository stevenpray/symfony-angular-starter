FROM symfonyangularstarter_debian

RUN echo "deb https://dl.yarnpkg.com/debian/ stable main" >> /etc/apt/sources.list
RUN apt-key adv --keyserver keyserver.ubuntu.com --recv-keys 46C2130DFD2497F5

RUN curl -LS https://deb.nodesource.com/setup_8.x \
    | sudo -E bash -

RUN apt-get update

RUN apt-get install --assume-yes --no-install-recommends --no-install-suggests \
    build-essential \
    nodejs \
    yarn

RUN apt-get purge --assume-yes --auto-remove \
    --option APT::AutoRemove::RecommendsImportant=false \
    --option APT::AutoRemove::SuggestsImportant=false
RUN rm -rf /var/lib/apt/lists/*
