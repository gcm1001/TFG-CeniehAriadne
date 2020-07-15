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
        libzip-dev \
        libcurl4-openssl-dev && \
        docker-php-ext-install curl exif mysqli pdo pdo_mysql zip

ENV OMEKA_VERSION 2.7.1

COPY /configFiles/php.ini.modificar /usr/local/etc/php/conf.d/php.ini

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

WORKDIR /var/www/html

ADD https://github.com/omeka/Omeka/releases/download/v${OMEKA_VERSION}/omeka-${OMEKA_VERSION}.zip omeka-${OMEKA_VERSION}.zip

RUN unzip -q omeka-${OMEKA_VERSION}.zip && mv omeka-${OMEKA_VERSION}/* /var/www/html/.

RUN rm -r omeka-${OMEKA_VERSION}.zip

ADD /configFiles/.htaccess.modificar /var/www/html/.htaccess

ADD /configFiles/config.ini.modificar /var/www/html/application/config/config.ini

RUN chown -R www-data:www-data files

RUN chown www-data:www-data application/config/config.ini

RUN rm -rf /var/www/html/themes/* && rm /var/www/html/db.ini 

RUN rm -rf /var/www/html/plugins/ExhibitBuilder && rm -rf /var/www/html/plugins/Coins    

ADD /omeka/themes /var/www/html/themes

ADD /omeka/plugins /var/www/html/plugins

ADD /configFiles/db.ini /var/www/html/db.ini

ADD /configFiles/mail.ini /var/www/html/plugins/AriadnePlusTracking/mail.ini

RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN a2enmod rewrite && service apache2 restart

