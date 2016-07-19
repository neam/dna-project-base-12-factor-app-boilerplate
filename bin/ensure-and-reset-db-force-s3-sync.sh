#!/usr/bin/env bash

# fail on any error
set -o errexit

# always run from project root
script_path=`dirname $0`
cd $script_path/..

bin/ensure-db.sh
bin/reset-db.sh --force-s3-sync

exit 0
