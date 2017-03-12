#!/bin/sh

composer install --no-dev
~/.composer/vendor/bin/box build -vv
chmod +x ./download/phpda.phar
composer install
