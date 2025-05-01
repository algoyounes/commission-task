FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    unzip \
    libicu-dev \
    fish \
    && docker-php-ext-install intl \
    && docker-php-ext-install bcmath \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/dev

EXPOSE 9000

CMD ["php-fpm"]
