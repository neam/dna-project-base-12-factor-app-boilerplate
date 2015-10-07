#!/bin/bash

# fail on any error
set -o errexit

# always run from project root
script_path=`dirname $0`
cd $script_path/..

# run actual command
vendor/bin/yii-dna-pre-release-testing-console migrate create $@

exit 0
