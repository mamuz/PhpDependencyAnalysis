#!/bin/sh

composer install --no-dev
~/.composer/vendor/bin/box build -vv
mv ./download/phpda.phar ./download/phpda
mv ./download/phpda.phar.pubkey ./download/phpda.pubkey
chmod +x ./download/phpda
composer install
