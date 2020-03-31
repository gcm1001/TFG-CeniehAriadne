FROM php:7.4-apache

MAINTAINER gcm1001@alu.ubu.es

RUN apt-get update && apt-get install -qq -y --no-install-recommends \
        build-essential \
        ca-certificates \
        git \
        unzip \
        imagemagick \
	    zlib1g \
        zlib1g-dev \
	    libzip-dev && \
        docker-php-ext-install curl exif mysqli pdo pdo_mysql zip

ENV OMEKA_VERSION 2.7.1

RUN echo "ServerName localhost" >> /etc/apache2.conf

WORKDIR /var/www/html

ADD https://github.com/omeka/Omeka/releases/download/v${OMEKA_VERSION}/omeka-${OMEKA_VERSION}.zip omeka-${OMEKA_VERSION}.zip

RUN unzip -q omeka-${OMEKA_VERSION}.zip && mv omeka-${OMEKA_VERSION}/* /var/www/html/.

RUN rm -r omeka-${OMEKA_VERSION}.zip

ADD omeka/.htaccess /var/www/html/.htaccess

ADD omeka/application/config/config.ini /var/www/html/application/config/config.ini

RUN chmod -R 777 files

RUN chown -R www-data:www-data files

RUN chown www-data:www-data application/config/config.ini

RUN rm -rf /var/www/html/themes/* && rm /var/www/html/db.ini

RUN rm -rf /var/www/html/plugins/ExhibitBuilder && rm -rf /var/www/html/plugins/Coins         

ADD /omeka/themes /var/www/html/themes

ADD /omeka/plugins /var/www/html/plugins

RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN a2enmod rewrite && service apache2 restart

