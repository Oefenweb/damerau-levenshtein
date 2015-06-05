#!/usr/bin/env bash
#
# set -x;
set -e;
set -o pipefail;
#
thisFile="$(readlink -f ${0})";
thisFilePath="$(dirname ${thisFile})";
#
if [ "${COVERALLS}" = '1' ]; then
  ~/.composer/vendor/bin/coveralls -c .coveralls.yml -v;
fi
