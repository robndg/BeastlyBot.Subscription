FROM pensiero/apache-php-mysql:php7.4

USER root

COPY . /var/www/html/beastlybot

WORKDIR /var/www/html/beastlybot

RUN apt-get update && apt-get install -y \
        libpng-dev \
        zlib1g-dev \
        libxml2-dev \
        libzip-dev \
        libonig-dev \
        zip \
        curl \
        unzip \
        libapache2-mod-php7.4 \
        php-gd \
        php-curl \
        php-xdebug \
        php-mbstring \
        php-xml \
        php-mysql \
        php-bcmath \
        php-json \
        php-zip \
        nano

COPY .docker/apache/beastly.conf /etc/apache2/sites-available/beastly.conf
COPY .docker/apache/beastly-ssl.conf /etc/apache2/sites-available/beastly-ssl.conf

COPY .docker/php/xdebug.ini /etc/php/8.0/mods-available/xdebug.ini
COPY .docker/php/xdebug.ini /etc/php/8.0/mods-available/xdebug.ini

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN composer install

RUN rm /etc/apache2/sites-available/000-default.conf

RUN service apache2 start

CMD php artisan serve --host=0.0.0.0 --port=8000

EXPOSE 8000
