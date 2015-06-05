#!/usr/bin/env bash
#
# set -x;
set -e;
set -o pipefail;
#
thisFile="$(readlink -f ${0})";
thisFilePath="$(dirname ${thisFile})";
#
if [ "${PHPCS}" = '1' ]; then
  vendor/bin/phpcs --config-set \
    installed_paths "vendor/cakephp/cakephp-codesniffer,vendor/oefenweb/cakephp-codesniffer";
  vendor/bin/phpcs --standard=Oefenweb src tests;
elif [ "${COVERALLS}" = 1 ]; then
  vendor/bin/phpunit --stderr --configuration phpunit.xml --coverage-clover build/logs/clover.xml;
else
  vendor/bin/phpunit --stderr --configuration phpunit.xml;
fi
