#!/bin/sh
set -e

if [ "$(printf %c "$1")" = '-' ]; then
  set -- phpda analyze "$@"
elif [ "$1" = 'analyze' ]; then
  set -- phpda "$@"
else
  set -- phpda analyze "$@"
fi

exec "$@"
