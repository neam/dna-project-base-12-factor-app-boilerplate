#!/usr/bin/env bash

source .env

set -x

# fail on any error
set -o errexit

# always run from project root
script_path=`dirname $0`
cd $script_path/..

# Reset db and run migrations
for DATA in $HOSTED_DATA_PROFILES; do
    export DATA=$DATA
    bin/ensure-and-reset-db-force-s3-sync.sh
done

exit 0