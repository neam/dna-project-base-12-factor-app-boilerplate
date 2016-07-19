#!/bin/bash

# fail on any error
set -o errexit

# always run from project root
script_path=`dirname $0`
cd $script_path/..

cd dna

if [ "$LOG" == "" ]; then
  export LOG=/tmp/migrate-propel.sh.log
fi

# run actual command
echo "* Applying propel migrations..." | tee -a $LOG
../bin/propel -vvv migrate >> $LOG
echo "* Updating current schema dumps..." | tee -a $LOG
# Allow failures since they can be caused by invalid views which needs to be regenerated before they can be dumped
# TODO: Find a way for mysql to dump also invalid views without complaining
set +o errexit
../vendor/neam/yii-dna-pre-release-testing/shell-scripts/update-current-schema-dumps.sh >> $LOG
set -o errexit

exit 0

#echo "* Applying propel schema..."
#../bin/propel -vvv sql:insert
