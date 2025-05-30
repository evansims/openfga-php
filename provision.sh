#!/bin/sh

# Environment provisioning script for OpenAI Codex.

add-apt-repository -y ppa:ondrej/php
apt update
apt install -yq php8.3-cli php8.3-common php8.3-mbstring php8.3-xml php8.3-curl php8.3-gmp php8.3-readline php8.3-phar php8.3-bcmath php8.3-intl php8.3-xdebug
curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php
php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer
composer install
