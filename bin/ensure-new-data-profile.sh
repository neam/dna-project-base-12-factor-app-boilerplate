#!/bin/bash

# fail on any error
set -o errexit

# always run from project root
script_path=`dirname $0`
cd $script_path/..

# DATA arg
export DATA="$1"
if [ "$DATA" == "" ]; then
  echo "DATA arg missing"
  exit 1;
fi

# uncomment to see all variables used in this script
#set -x;

# run actual commands
bin/new-data-profile.sh $DATA
bin/ensure-db.sh
bin/reset-db.sh
bin/upload-current-user-data.sh

exit 0
