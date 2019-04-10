#!/bin/sh
set -e

curl -sSLo /tmp/installer.php https://getcomposer.org/installer
expected_signature=$(wget -q -O - https://composer.github.io/installer.sig)
actual_signature=$(php -r "echo hash_file('SHA384', '/tmp/installer.php');")

if [ "$expected_signature" != "$actual_signature" ]; then
    echo 'Invalid composer installer signature'
    rm /tmp/installer.php
    exit 1
fi

php /tmp/installer.php --quiet --install-dir=/usr/local/bin --filename=composer
result=$?
rm /tmp/installer.php
exit ${result}
