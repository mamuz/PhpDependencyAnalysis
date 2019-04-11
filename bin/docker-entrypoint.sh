#!/bin/sh
set -e

if [ "$(printf %c "$1")" = '-' ]; then
  set -- phpda analyze "$@"
elif [ "$1" = 'phpda' ]; then
  set -- "$@"
elif [ "$1" = 'analyze' ]; then
  set -- phpda "$@"
fi

exec "$@"
