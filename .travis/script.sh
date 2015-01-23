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
	~/.composer/vendor/bin/phpcs --standard=CakePHP -n src;
elif [ "${COVERALLS}" = 1 ]; then
	~/.composer/vendor/bin/phpunit --stderr --coverage-clover build/logs/clover.xml --configuration phpunit.xml;
else
	~/.composer/vendor/bin/phpunit --stderr --configuration phpunit.xml;
fi
