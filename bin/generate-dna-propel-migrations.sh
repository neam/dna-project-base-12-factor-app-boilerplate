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
echo "* Generating propel migrations..."
vendor/bin/propel -vvv diff --table-renaming

# post-propel
echo "* Reverting propel stand-alone config (so that the configuration once again can be used in the ordinary web apps)"
git checkout -- generated-conf/config.php

exit 0
