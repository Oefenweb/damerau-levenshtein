#!/usr/bin/env bash
#
# set -x;
set -e;
set -o pipefail;
#
thisFile="$(readlink -f ${0})";
thisFilePath="$(dirname ${thisFile})";
#
if [ "${CODECOVERAGE}" = '1' ]; then
  bash <(curl -sSL https://codecov.io/bash)
fi
