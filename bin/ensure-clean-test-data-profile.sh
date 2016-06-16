#!/usr/bin/env bash

#Ensure the existence of a test data profile for the current data profile:

    export PARENT_DATA=$DATA
    export TEST_DATA=test-$DATA
    rm -r dna/db/migration-base/$TEST_DATA/ || true
    bin/new-data-profile.sh $TEST_DATA
    DATA=$TEST_DATA bin/ensure-db.sh

#Copy the data of the current data profile to the test data profile:

    cp -r dna/db/migration-base/$PARENT_DATA/* dna/db/migration-base/$TEST_DATA/

#Inform the user

    echo
    echo "* Done!"
    echo
    echo "The data profile $TEST_DATA is now a copy of $PARENT_DATA"
    echo "To use:"
    echo "  export DATA=$TEST_DATA";
