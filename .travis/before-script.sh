#!/usr/bin/env bash
#
# set -x;
set -e;
set -o pipefail;
#
thisFile="$(readlink -f ${0})";
thisFilePath="$(dirname ${thisFile})";
#
composer global require --dev "phpunit/phpunit=4.*"

composer install --dev --no-interaction --prefer-source;

if [ "${COVERALLS}" = '1' ]; then
	composer require --dev satooshi/php-coveralls:dev-master;
fi

if [ "${PHPCS}" = '1' ]; then
	composer global require --dev 'cakephp/cakephp-codesniffer=1.*';
	~/.composer/vendor/bin/phpcs --config-set installed_paths ~/.composer/vendor/cakephp/cakephp-codesniffer;
fi

phpenv rehash;

cat << EOF > phpunit.xml;
<phpunit>
  <testsuites>
    <testsuite name="damerau-levenshtein">
      <directory>test</directory>
    </testsuite>
  </testsuites>
</phpunit>
EOF

cat << EOF > .coveralls.yml
# for php-coveralls
src_dir: src
coverage_clover: build/logs/clover.xml
json_path: build/logs/coveralls-upload.json
EOF
