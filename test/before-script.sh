#!/usr/bin/env bash
#
set -x;
set -e;
set -o pipefail;
#
thisFile="$(readlink -f ${0})";
thisFilePath="$(dirname ${thisFile})";
#

pear channel-discover pear.phpunit.de;
pear channel-discover components.ez.no;
pear channel-discover pear.symfony-project.com;
pear install --alldeps --force phpunit/PHPUnit;

composer install --dev --no-interaction --prefer-source;

if [ "${COVERALLS}" = '1' ]; then
	composer require --dev satooshi/php-coveralls:dev-master;
fi

if [ "${PHPCS}" = '1' ]; then
	pear channel-discover pear.cakephp.org;
	pear install --alldeps cakephp/CakePHP_CodeSniffer;
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
