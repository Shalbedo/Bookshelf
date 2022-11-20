FROM php:8.1-fpm

COPY ./app ./var/www/app

RUN apt-get update && apt-get install -y \
            zlib1g-dev \
            libzip-dev \
            libpq-dev \
            unzip \
            libicu-dev \
            libgmp-dev \
    && echo 'alias sf="php bin/console"' >> ~/.bashrc

RUN docker-php-ext-configure \
    intl

RUN docker-php-ext-install \
    intl \
    pdo pdo_pgsql pgsql zip gmp bcmath

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/app

EXPOSE 9000
