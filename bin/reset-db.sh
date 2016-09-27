#!/bin/bash

# fail on any error
set -o errexit
set -o pipefail

# always run from project root
script_path=`dirname $0`
cd $script_path/..

# uncomment to see all variables used in this script
#set -x;

# reset the reset-db log when invoking this shortcut script
echo "" > /tmp/reset-db.sh.log

# run actual command
vendor/neam/yii-dna-pre-release-testing/shell-scripts/reset-db.sh $@

exit 0
