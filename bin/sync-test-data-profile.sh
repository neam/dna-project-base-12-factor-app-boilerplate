#!/usr/bin/env bash

# fail on any error
set -o errexit

# always run from project root
script_path=`dirname $0`
cd $script_path/..

# ensure DATA is set as a shell env var
if [ "$DATA" == "" ]; then

    echo "The environment variable DATA needs to be set"
    exit 1

fi

#Ensure the existence of a test data profile for the current data profile:

    export PARENT_DATA=$DATA
    export TEST_DATA=test-$DATA
    rm -r dna/db/migration-base/$TEST_DATA/ || true
    bin/new-data-profile.sh $TEST_DATA
    DATA=$TEST_DATA bin/ensure-db.sh

#Copy the data of the current data profile to the test data profile:

    cp -r dna/db/migration-base/$PARENT_DATA/* dna/db/migration-base/$TEST_DATA/

#Update the test profile data to match the current database contents

  # cd to app root
  dna_path=dna

  # make app config available as shell variables
  source vendor/neam/php-app-config/shell-export.sh

  # upload user data
  vendor/neam/yii-dna-pre-release-testing/shell-scripts/upload-user-data-backup.sh --dump-only

  # Commands to run to use the dumped data and schema dumps locally
  cp $dna_path/db/data.sql dna/db/migration-base/$TEST_DATA/
  cp $dna_path/db/schema.sql dna/db/migration-base/$TEST_DATA/

#Inform the user

    echo
    echo "* Done!"
    echo
    echo "The data profile $TEST_DATA is now a copy of $PARENT_DATA"
    echo "To use:"
    echo "  export DATA=$TEST_DATA";
