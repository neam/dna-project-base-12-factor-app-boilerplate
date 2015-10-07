#!/bin/bash

# fail on any error
set -o errexit

# always run from project root
script_path=`dirname $0`
cd $script_path/..

# add default
if [ "$connectionID" == "" ]; then
    connectionID=db
fi

# uncomment to see all variables used in this script
#set -x;

# reset the reset-db log when invoking this shortcut script
echo "" > /tmp/reset-db.sh.log

# run actual command
connectionID=$connectionID vendor/neam/yii-dna-pre-release-testing/shell-scripts/reset-user-generated-files.sh $@

exit 0
