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
echo "* Reverse-engineer the database as should be according to content model metadata -> ./schema.xml"
DECORATE_PROPEL_SCHEMA=1 vendor/bin/propel -vvv reverse
cp generated-reversed-database/schema.xml .
echo "* Reverse-engineer the database as is -> generated-reversed-database/schema.xml"
DECORATE_PROPEL_SCHEMA=0 vendor/bin/propel -vvv reverse
#echo "* Pristine propel schema generated. Revert manual changes to main schema.xml before continuing. Press ENTER to continue"
#read
echo "* Generating propel schema..."
vendor/bin/propel -vvv sql:build
echo "* Pristine propel schema generated. Manually revert changes to main schema.xml before continuing. Press ENTER to continue"
read
echo "* Generating propel models..."
rm -r generated-classes/propel/models/Base/*.php || true
rm -r generated-classes/propel/models/Map/*.php || true
vendor/bin/propel model:build
echo "* Propel models generated"

# post-propel
echo "* Reverting propel stand-alone config (so that the configuration once again can be used in the ordinary web apps)"
git checkout -- generated-conf/config.php

exit 0
