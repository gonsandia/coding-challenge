FROM php:8.0-cli
RUN apt update
RUN apt install git -y
RUN pecl install xdebug-3.0.4 \
    && docker-php-ext-enable xdebug

COPY ./.docker/php/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY ./ /var/www

WORKDIR /var/www

RUN composer dump-autoload

WORKDIR /var/www/src/ui

CMD [ "php", "Cli.php" ]

