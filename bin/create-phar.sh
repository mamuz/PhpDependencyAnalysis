#!/bin/sh

composer install --no-dev
~/.composer/vendor/bin/box build -vv
chmod +x ./bin/phpda.phar
composer install
