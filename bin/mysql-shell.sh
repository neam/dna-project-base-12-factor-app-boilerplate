#!/bin/bash

#
# Opens a mysql shell
#

# Uncomment to see all variables used in this sciprt
#set -x;

script_path=`dirname $0`

# fail on any error
set -o errexit

# set paths
script_path=`dirname $0`
cd $script_path/..
dna_path=$(pwd)/dna

# make app config available as shell variables
cd $dna_path/../
source vendor/neam/php-app-config/shell-export.sh
cd -

if [ "$DATA" == "" ]; then

    echo "The environment variable DATA needs to be set"
    exit 1

fi

mysql -v -A --host=$DATABASE_HOST --port=$DATABASE_PORT --user=$DATABASE_USER --password=$DATABASE_PASSWORD $DATABASE_NAME
