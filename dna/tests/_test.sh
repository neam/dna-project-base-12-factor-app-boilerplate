#!/bin/bash

set -x;

script_path=`dirname $0`
cd $script_path

# fail on any error
set -o errexit

# reset-db or not
RESET_DB=1
if [ "$1" == "--skip-reset-db" ]; then
  RESET_DB=0
fi

# TODO: loop this through. every available yii-dna-api-revision should work with various schemas and various schemas should work with all unit tests

source $TESTS_FRAMEWORK_BASEPATH/_set-codeception-group-args.sh
# TODO: Restore support for test-databases, then re-enable usable of dbTest
#connectionID=dbTest
if [ "$RESET_DB" == 1 ]; then $PROJECT_BASEPATH/bin/reset-db.sh; fi

test_console mysqldump --dumpPath=dna/tests/codeception/_data/
codecept run unit $CODECEPTION_GROUP_ARGS --debug --fail-fast
#codecept run functional -g data:$DATA --debug

# TODO: Restore support for test-databases, then re-enable usable of dbTest
#connectionID=dbTest
if [ "$RESET_DB" == 1 ]; then $PROJECT_BASEPATH/bin/reset-db.sh; fi

# TODO: loop this through. every available yii-dna-api-revision should work with various schemas and various schemas should work with every available yii-api
# the yii-apis are tested first using unit tests and then tested through integration tests of the apps that actually use the apis:
# - rest-api - all various rest contracts should work as well
# - frontends - should work with the old schema and new
