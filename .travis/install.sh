#!/usr/bin/env bash
#
# set -x;
set -e;
set -o pipefail;
#
thisFile="$(readlink -f ${0})";
thisFilePath="$(dirname ${thisFile})";
#
composer install --no-ansi --no-progress --no-interaction --prefer-source;

if [ "${PHPCS}" = '1' ]; then
  composer global require --dev 'cakephp/cakephp-codesniffer=1.*';

  composer global config repositories.Oefenweb/cakephp-codesniffer vcs https://github.com/Oefenweb/cakephp-codesniffer;
  composer global require --dev 'oefenweb/cakephp-codesniffer:dev-master';
elif [ "${COVERALLS}" = '1' ]; then
  composer global require --dev 'satooshi/php-coveralls:dev-master';
else
  composer global require --dev 'phpunit/phpunit=4.*';
fi
