#!/bin/bash

#set -x;

script_path=`dirname $0`
cd $script_path/..

# fail on any error
set -o errexit

cd $PROJECT_BASEPATH
source vendor/neam/php-app-config/shell-export.sh
cd -

source $TESTS_FRAMEWORK_BASEPATH/_set-codeception-group-args.sh

CODE_COVERAGE_ARGS=" --coverage --coverage-xml --coverage-html"
# Comment to enable code coverage (xdebug must be enabled locally)
CODE_COVERAGE_ARGS=""

time codecept run unit_db_agnostic $CODECEPTION_GROUP_ARGS $CODE_COVERAGE_ARGS --debug --fail-fast $@

echo "* Done running tests"

if [ -f codeception/_log/coverage.xml ]; then
  cat codeception/_log/coverage.xml | sed 's#/app/#/Users/user/local-path-to-project/#g' > codeception/_log/coverage.phpstorm.xml
fi
