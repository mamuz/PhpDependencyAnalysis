#!/bin/sh

composer install --no-dev
~/.composer/vendor/bin/box build -vv
mv phpda.phar phpda
mv phpda.phar.pubkey phpda.pubkey
composer install
