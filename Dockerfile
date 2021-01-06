FROM php:7.4-apache

# Install PHP extensions
RUN apt-get update && apt-get install --no-install-recommends -y \
    ca-certificates \
    build-essential  \
    software-properties-common \
    cron \
    git \
    htop \
    wget \
    dos2unix \
    curl \
    libcurl4-gnutls-dev \
    sudo \
    libc-client-dev \
    libkrb5-dev \
    libmcrypt-dev \
    libssl-dev \
    libxml2-dev \
    libzip-dev \
    libjpeg-dev \
    libmagickwand-dev \
    libpng-dev \
    libgif-dev \
    libtiff-dev \
    libz-dev \
    libpq-dev \
    imagemagick \
    graphicsmagick \
    libwebp-dev \
    libjpeg62-turbo-dev \
    libxpm-dev \
    libaprutil1-dev \
    libicu-dev \
    libfreetype6-dev \
    unzip \
    nano \
    zip \
    mariadb-client \
    && apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false \
    && rm -rf /var/lib/apt/lists/* \
    && rm /etc/cron.daily/*

RUN docker-php-ext-configure imap --with-kerberos --with-imap-ssl && \
    docker-php-ext-install imap && \
    docker-php-ext-enable imap

RUN docker-php-ext-configure gd \
    && docker-php-ext-install  gd \
    && docker-php-ext-configure opcache --enable-opcache \
    && docker-php-ext-install intl mysqli curl pdo_mysql zip opcache bcmath gd \
    && docker-php-ext-enable intl mysqli curl pdo_mysql zip opcache bcmath gd

ENV COMPOSER_DEBUG_EVENTS 1
ENV MAUTIC_VERSION 3.0.2
ENV MAUTIC_SHA1 225dec8fbac05dfb77fdd7ed292a444797db215f

# Install composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php --1 --install-dir=/usr/bin --filename=composer

# By default enable cron jobs
ENV MAUTIC_RUN_CRON_JOBS true

# Setting an Default database user for Mysql
ENV MAUTIC_DB_USER root

# Setting an Default database name for Mysql
ENV MAUTIC_DB_NAME mautic

# Setting PHP properties
ENV PHP_INI_DATE_TIMEZONE='UTC' \
    PHP_MEMORY_LIMIT=512M \
    PHP_MAX_UPLOAD=512M \
    PHP_MAX_EXECUTION_TIME=300

COPY mautic.crontab /etc/cron.d/mautic
RUN chmod 644 /etc/cron.d/mautic

WORKDIR /var/www/html

RUN mkdir var
RUN mkdir var/cache
RUN mkdir var/cache/prod
RUN chmod -R 777 var

COPY composer.json composer.json
RUN composer install
RUN chown -R www-data:www-data .

#ADD "https://www.random.org/cgi-bin/randbyte?nbytes=10&format=h" skipcache

COPY --chown=www-data:www-data . .
COPY docker-entrypoint.sh /entrypoint.sh
COPY makeconfig.php /makeconfig.php
COPY makedb.php /makedb.php

RUN tail -500 /var/www/html/app/bundles/CoreBundle/Monolog/Handler/GelfyHandler.php

# the very first task at composer which executes code in your symfony project.

#RUN php bin/console mautic:plugins:reload

#RUN ls -la var/cache
#RUN ls -la var/cache/prod

RUN chmod -R 777 var
#RUN chown -R www-data:www-data .

# Enable Apache Rewrite Module
RUN a2enmod rewrite

# Apply necessary permissions
RUN ["chmod", "+x", "/entrypoint.sh"]
ENTRYPOINT ["/entrypoint.sh"]

CMD ["apache2-foreground"]

