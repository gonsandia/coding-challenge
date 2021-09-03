FROM php:8.0-cli
RUN apt update
RUN apt install git -y
RUN pecl install xdebug-3.0.4 \
    && docker-php-ext-enable xdebug

COPY ./.docker/php/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www

ENTRYPOINT ["/var/www/.docker/entrypoint.sh"]

