FROM debian:stretch-slim

ENV DEBIAN_FRONTEND noninteractive
ENV DOCKERIZE_VERSION "v0.6.0"
ENV SHELL /bin/bash

RUN apt-get update

RUN apt-get dist-upgrade --assume-yes

RUN apt-get install --assume-yes --no-install-recommends --no-install-suggests \
    apt-transport-https \
    apt-utils \
    ca-certificates \
    curl \
    dirmngr \
    git \
    gnupg2 \
    lsb-release \
    sudo \
    xz-utils

RUN apt-get purge --assume-yes --auto-remove \
    --option APT::AutoRemove::RecommendsImportant=false \
    --option APT::AutoRemove::SuggestsImportant=false
RUN rm -rf /var/lib/apt/lists/*

RUN curl -LS https://github.com/jwilder/dockerize/releases/download/${DOCKERIZE_VERSION}/dockerize-linux-amd64-${DOCKERIZE_VERSION}.tar.gz \
    | tar xzv -C /usr/local/bin

RUN curl -LS https://gist.githubusercontent.com/zyedidia/d4acfcc6acf2d0d75e79004fa5feaf24/raw/a43e603e62205e1074775d756ef98c3fc77f6f8d/install_micro.sh \
    | bash -s linux64 /usr/local/bin
