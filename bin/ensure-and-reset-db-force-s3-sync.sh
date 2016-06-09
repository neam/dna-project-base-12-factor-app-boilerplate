#!/usr/bin/env bash

# fail on any error
set -o errexit

bin/ensure-db.sh
bin/reset-db.sh --force-s3-sync

exit 0
