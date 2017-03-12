#!/bin/sh

curl -sSLo /tmp/installer.php https://getcomposer.org/installer
EXPECTED_SIGNATURE=$(wget -q -O - https://composer.github.io/installer.sig)
ACTUAL_SIGNATURE=$(php -r "echo hash_file('SHA384', '/tmp/installer.php');")

if [ "$EXPECTED_SIGNATURE" != "$ACTUAL_SIGNATURE" ]; then
    >&2 echo 'ERROR: Invalid composer installer signature'
    rm /tmp/installer.php
    exit 1
fi

php /tmp/installer.php --quiet --install-dir=/usr/local/bin --filename=composer
RESULT=$?
rm /tmp/installer.php
exit $RESULT
