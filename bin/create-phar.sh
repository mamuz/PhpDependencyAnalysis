#!/bin/sh

composer install --no-dev
~/.composer/vendor/bin/box build -vv
composer install
