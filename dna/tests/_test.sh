#!/bin/bash

#set -x;

script_path=`dirname $0`
cd $script_path

# fail on any error
set -o errexit

source $TESTS_FRAMEWORK_BASEPATH/_set-codeception-group-args.sh

time codecept run unit-db-agnostic $CODECEPTION_GROUP_ARGS --debug --fail-fast

if [ ! "$1" == "--include-db-dependent" ]; then
  echo "* Skipping db-dependent tests by default. Use --include-db-dependent to include them";
  exit 0;
fi

if [[ "$DATA" != test_* ]]; then
  echo "* Skipping db-dependent tests since the data profile is not prefixed with test_, meaning that there is a risk that tests run against live data";
  exit 0;
fi

echo "* Running db-dependent tests for data profile $DATA";

# reset-db or not
RESET_DB=1
if [ "$2" == "--skip-reset-db" ]; then
  RESET_DB=0
fi

if [ "$RESET_DB" == 1 ]; then
time   $PROJECT_BASEPATH/bin/reset-db.sh;
time   test_console mysqldump --dumpPath=dna/tests/codeception/_data/
  sed -i -e 's/\/\*!50013 DEFINER=`[^`]*`@`[^`]*` SQL SECURITY DEFINER \*\///' $PROJECT_BASEPATH/dna/tests/codeception/_data/dump.sql
fi

time codecept run unit-db-dependent $CODECEPTION_GROUP_ARGS --debug --fail-fast
#codecept run functional $CODECEPTION_GROUP_ARGS --debug --fail-fast

#if [ "$RESET_DB" == 1 ]; then $PROJECT_BASEPATH/bin/reset-db.sh; fi

echo "* Done running tests"
