#!/bin/bash

# fail on any error
set -o errexit

# always run from project root
script_path=`dirname $0`
cd $script_path/..

# pre-propel
cd dna
echo "* Prepare propel stand-alone config"
vendor/bin/propel config:convert

# run actual command
echo "* Applying propel migrations..."
vendor/bin/propel -vvv migrate
echo "* Updating current schema dumps..."
../vendor/neam/yii-dna-pre-release-testing/shell-scripts/update-current-schema-dumps.sh

# post-propel
echo "* Reverting propel stand-alone config (so that the configuration once again can be used in the ordinary web apps)"
git checkout -- generated-conf/config.php

exit 0

#echo "* Applying propel schema..."
#vendor/bin/propel -vvv sql:insert
