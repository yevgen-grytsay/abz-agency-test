#!/bin/bash

# We need to install dependencies only for Docker
[[ ! -e /.dockerenv ]] && exit 0

set -xe

# Install git (the php image doesn't have it) which is required by composer
apt-get update -yqq
apt-get install git wget zip default-mysql-client -yqq

apt-get install -y \
    libfreetype-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
	&& docker-php-ext-install gd


docker-php-ext-install pdo_mysql

wget https://composer.github.io/installer.sig -O - -q | tr -d '\n' > installer.sig
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('SHA384', 'composer-setup.php') === file_get_contents('installer.sig')) { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php'); unlink('installer.sig');"
php composer.phar install

cp .env.example .env
php artisan key:generate

PHP_SEC_CHECKER_NAME=local-php-security-checker_2.0.6_linux_amd64
wget "https://github.com/fabpot/local-php-security-checker/releases/download/v2.0.6/$PHP_SEC_CHECKER_NAME"
mv $PHP_SEC_CHECKER_NAME local-php-security-checker
chmod u+x ./local-php-security-checker

mysql --user=root --host="$DB_HOST" --password="$MYSQL_ROOT_PASSWORD" <<-EOSQL
    CREATE DATABASE IF NOT EXISTS testing;
    GRANT ALL PRIVILEGES ON \`testing%\`.* TO '$MYSQL_USER'@'%';
EOSQL
