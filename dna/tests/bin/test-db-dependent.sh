#!/bin/bash

#set -x;

script_path=`dirname $0`
cd $script_path/..

# fail on any error
set -o errexit

if [[ "$DATA" != test-* ]]; then
  echo "* Prefixing the current data profile with test-, so that there is less risk that tests run against live data";
  export DATA=test-$DATA
fi

cd $PROJECT_BASEPATH
source vendor/neam/php-app-config/shell-export.sh
cd -

echo "* Running db-dependent tests for data profile $DATA";

source $TESTS_FRAMEWORK_BASEPATH/_set-codeception-group-args.sh

CODE_COVERAGE_ARGS=" --coverage --coverage-xml --coverage-html"
# Comment to enable code coverage (xdebug must be enabled locally)
CODE_COVERAGE_ARGS=""

# reset-db or not
RESET_DB=1
if [ "$1" == "--skip-reset-db" ]; then
  RESET_DB=0
fi
if [ "$1" == "--skip-db-reset" ]; then
  RESET_DB=0
fi

if [ "$RESET_DB" == 1 ]; then
  time $PROJECT_BASEPATH/bin/reset-db.sh;
  time test_console mysqldump --dumpPath=dna/tests/codeception/_data/
  mv $PROJECT_BASEPATH/dna/tests/codeception/_data/dump.sql $PROJECT_BASEPATH/dna/tests/codeception/_data/dump-db-dependent.$DATA.sql
  sed -i -e 's/\/\*!50013 DEFINER=`[^`]*`@`[^`]*` SQL SECURITY DEFINER \*\///' $PROJECT_BASEPATH/dna/tests/codeception/_data/dump-db-dependent.$DATA.sql
  echo "* Codeception is set to reload the profile $DATA between tests (codeception/_data/dump-db-dependent.$DATA.sql). Run following test rounds with the --skip-reset-db flag to skip this initialization";
fi

time codecept run unit_db_dependent $CODECEPTION_GROUP_ARGS --debug --fail-fast
#codecept run functional $CODECEPTION_GROUP_ARGS --debug --fail-fast

#if [ "$RESET_DB" == 1 ]; then $PROJECT_BASEPATH/bin/reset-db.sh; fi

echo "* Done running tests"

if [ -f codeception/_log/coverage.xml ]; then
  cat codeception/_log/coverage.xml | sed 's#/app/#/Users/user/local-path-to-project/#g' > codeception/_log/coverage.phpstorm.xml
fi
