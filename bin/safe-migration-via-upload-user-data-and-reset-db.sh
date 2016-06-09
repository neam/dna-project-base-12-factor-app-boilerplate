#!/bin/bash

# fail on any error
set -o errexit

# always run from project root
script_path=`dirname $0`
cd $script_path/..

# run actual command
bin/upload-current-user-data.sh
bin/ensure-and-reset-db-force-s3-sync.sh

exit 0
