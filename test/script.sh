#!/usr/bin/env bash
#
set -x;
set -e;
set -o pipefail;
#
thisFile="$(readlink -f ${0})";
thisFilePath="$(dirname ${thisFile})";
#

if [ "${PHPCS}" = '1' ]; then
	phpcs --standard=CakePHP -n .;
elif [ "${COVERALLS}" = 1 ]; then
	phpunit --stderr --coverage-clover build/logs/clover.xml --configuration phpunit.xml;
else
	phpunit --stderr --configuration phpunit.xml;
fi
