FROM php:8.0-fpm

RUN apt-get update \
  && apt-get install -y libgmp-dev git unzip libzip-dev zip libicu-dev \
  && docker-php-ext-install gmp pdo_mysql zip intl

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY docker/php-fpm/symfony.ini /etc/local/etc/php/conf.d/
COPY docker/php-fpm/symfony.pool.conf /usr/local/etc/php-fpm.conf

WORKDIR /var/www/symfony
COPY . .
RUN composer install --prefer-dist --no-progress --no-suggest -o -n

RUN mkdir -p /usr/local/var/log/ && touch /usr/local/var/log/php-fpm.log && chown -R www-data:www-data /usr/local/var/log
USER www-data
EXPOSE 9001

CMD ["php-fpm", "-F"]
