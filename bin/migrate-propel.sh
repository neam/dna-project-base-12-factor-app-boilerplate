#!/bin/bash

# fail on any error
set -o errexit

# always run from project root
script_path=`dirname $0`
cd $script_path/..

cd dna

# run actual command
echo "* Applying propel migrations..."
../bin/propel -vvv migrate
echo "* Updating current schema dumps..."
../vendor/neam/yii-dna-pre-release-testing/shell-scripts/update-current-schema-dumps.sh

exit 0

#echo "* Applying propel schema..."
#../bin/propel -vvv sql:insert
