#!/bin/bash

# We need to install dependencies only for Docker
[[ ! -e /.dockerenv ]] && exit 0

set -xe

apk update && apk add --no-cache fcgi git libzip-dev zip icu-dev

docker-php-ext-install mysqli pdo pdo_mysql pcntl intl zip

php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === 'dac665fdc30fdd8ec78b38b9800061b4150413ff2e3b6f88543c636f7cd84f6db9189d43a81e5503cda447da73c7e5b6') { echo 'Installer verified'.PHP_EOL; } else { echo 'Installer corrupt'.PHP_EOL; unlink('composer-setup.php'); exit(1); }"
php composer-setup.php
php -r "unlink('composer-setup.php');"
mv composer.phar /usr/local/bin/composer

apk --update --no-cache add autoconf g++ make linux-headers  && \
  cd /tmp && \
  git clone https://github.com/xdebug/xdebug.git && \
  cd xdebug && \
  git checkout 3.4.2 && \
  phpize && \
  ./configure --enable-xdebug && \
  make && \
  make install && \
  rm -rf /tmp/xdebug && \
  docker-php-ext-enable xdebug
