FROM php:5.5

WORKDIR /workspace

RUN echo "deb http://archive.debian.org/debian jessie main contrib non-free" > /etc/apt/sources.list && \
    echo "deb http://archive.debian.org/debian-security jessie/updates main contrib non-free" >> /etc/apt/sources.list && \
    apt-get update && apt-get install -y --force-yes ca-certificates && \
    update-ca-certificates

RUN apt-get install -y --force-yes git libxml2-utils

ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN install-php-extensions @composer-1 xdebug

RUN ln -s /workspaces/vendor/bin/phpunit /usr/local/bin/phpunit

CMD ["sleep", "infinity"]
