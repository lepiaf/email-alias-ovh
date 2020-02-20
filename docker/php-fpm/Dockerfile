FROM php:7.4-fpm

RUN apt-get update && apt-get install -y libgmp-dev git && docker-php-ext-install gmp
RUN echo "$(curl -sS https://composer.github.io/installer.sig) -" > composer-setup.php.sig \
        && curl -sS https://getcomposer.org/installer | tee composer-setup.php | sha384sum -c composer-setup.php.sig \
        && php composer-setup.php && rm composer-setup.php* \
        && chmod +x composer.phar && mv composer.phar /usr/bin/composer

COPY symfony.ini /etc/local/etc/php/conf.d/
COPY symfony.pool.conf /usr/local/etc/php-fpm.conf

CMD ["php-fpm", "-F", "-R"]

WORKDIR /var/www/symfony
EXPOSE 9001
