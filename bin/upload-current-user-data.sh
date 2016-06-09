#!/bin/bash

# fail on any error
set -o errexit

# always run from project root
script_path=`dirname $0`
cd $script_path/..

# make app config available as shell variables
source vendor/neam/php-app-config/shell-export.sh

# upload user data
vendor/neam/yii-dna-pre-release-testing/shell-scripts/upload-user-data-backup.sh $@

exit 0

