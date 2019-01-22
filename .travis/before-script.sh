#!/usr/bin/env bash
#
# set -x;
set -e;
set -o pipefail;
#
thisFile="$(readlink -f ${0})";
thisFilePath="$(dirname ${thisFile})";
#
composer self-update;
composer install --no-ansi --no-progress --no-interaction --prefer-source;

if [ "${PHPCS}" = '1' ]; then
  composer require --dev 'squizlabs/php_codesniffer=2.*';
else
  PHP_VERSION="$(php -r 'echo phpversion();')";
  if [ "${PHP_VERSION:0:3}" == '7.0' ]; then
    composer require --dev 'phpunit/phpunit=^6';
  else
    composer require --dev 'phpunit/phpunit=^7';
  fi
fi
