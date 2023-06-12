FROM php:5.6.40-apache-stretch

WORKDIR /app

COPY --chmod=744 files/sources.list /etc/apt/sources.list
COPY --chmod=744 files/rest.conf /etc/apache2/conf-available/rest.conf
RUN a2enmod rewrite && ln -s /etc/apache2/conf-available/rest.conf /etc/apache2/conf-enabled/rest.conf

EXPOSE 80/tcp
