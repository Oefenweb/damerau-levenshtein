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
  ~/.composer/vendor/bin/phpcs --config-set \
    installed_paths "${HOME}/.composer/vendor/cakephp/cakephp-codesniffer,${HOME}/.composer/vendor/oefenweb/cakephp-codesniffer";
  ~/.composer/vendor/bin/phpcs --standard=Oefenweb src tests;
elif [ "${COVERALLS}" = '1' ]; then
  ~/.composer/vendor/bin/phpunit --stderr --configuration phpunit.xml --coverage-clover build/logs/clover.xml;
else
  ~/.composer/vendor/bin/phpunit --stderr --configuration phpunit.xml;
fi
