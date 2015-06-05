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
  composer global require --dev 'cakephp/cakephp-codesniffer=1.*';

  composer global config repositories.Oefenweb/cakephp-codesniffer vcs https://github.com/Oefenweb/cakephp-codesniffer;
  composer global require --dev 'oefenweb/cakephp-codesniffer:dev-master';
else
  composer global require --dev 'phpunit/phpunit=4.*';
  if [ "${COVERALLS}" = '1' ]; then
    composer global require --dev 'satooshi/php-coveralls:dev-master';

    printf '# for php-coveralls\nsrc_dir: src\ncoverage_clover: build/logs/clover.xml\njson_path: build/logs/coveralls-upload.json\n' > .coveralls.yml;
		ls -lha .;
		cat .coveralls.yml;
  fi
fi
