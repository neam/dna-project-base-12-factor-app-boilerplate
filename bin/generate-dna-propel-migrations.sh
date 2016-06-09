#!/bin/bash

# fail on any error
set -o errexit

# always run from project root
script_path=`dirname $0`
cd $script_path/..

cd dna

# run actual command
echo "* Generating propel migrations..."
../bin/propel -vvv diff --table-renaming

exit 0
