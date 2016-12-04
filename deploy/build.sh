#!/bin/bash

# fail on any error
set -o errexit

# always run from project root
script_path=`dirname $0`
cd $script_path/..

# run actual command
vendor/neam/yii-dna-deployment/deploy/build.sh $@

exit 0
