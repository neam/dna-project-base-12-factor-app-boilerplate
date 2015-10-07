#!/bin/bash

set -x;

script_path=`dirname $0`
cd $script_path
# fail on any error
set -o errexit

# TODO: loop this through. every available yii-dna-api-revision should work with various schemas and various schemas should work with all unit tests

export DATA=clean-db
source $TESTS_FRAMEWORK_BASEPATH/_set-codeception-group-args.sh
connectionID=dbTest $PROJECT_BASEPATH/bin/reset-db.sh

test_console mysqldump --connectionID=dbTest --dumpPath=dna/tests/codeception/_data/
codecept run unit $CODECEPTION_GROUP_ARGS --debug --fail-fast
#codecept run functional -g data:$DATA --debug

connectionID=dbTest $PROJECT_BASEPATH/bin/reset-db.sh

export DATA=user-generated
source $TESTS_FRAMEWORK_BASEPATH/_set-codeception-group-args.sh
connectionID=dbTest $PROJECT_BASEPATH/bin/reset-db.sh

test_console mysqldump --connectionID=dbTest --dumpPath=dna/tests/codeception/_data/
codecept run unit $CODECEPTION_GROUP_ARGS --debug --fail-fast
#codecept run functional -g data:$DATA --debug

connectionID=dbTest $PROJECT_BASEPATH/bin/reset-db.sh

# TODO: loop this through. every available yii-dna-api-revision should work with various schemas and various schemas should work with every available yii-api
# the yii-apis are tested first using unit tests and then tested through integration tests of the apps that actually use the apis:
# - rest-api - all various rest contracts should work as well
# - frontends - should work with the old schema and new


