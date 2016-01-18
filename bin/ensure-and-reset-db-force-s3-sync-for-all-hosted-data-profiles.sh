#!/usr/bin/env bash

source .env

set -x

# Reset db and run migrations
for DATA in $HOSTED_DATA_PROFILES; do
    export DATA=$DATA
    bin/ensure-and-reset-db-force-s3-sync.sh
done

exit 0