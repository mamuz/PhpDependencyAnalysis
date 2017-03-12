#!/bin/sh

composer install --no-dev
~/.composer/vendor/bin/box build -vv
mv phpda.phar phpda
mv phpda.phar.pubkey phpda.pubkey
sha1sum phpda > phpda.version
composer install
