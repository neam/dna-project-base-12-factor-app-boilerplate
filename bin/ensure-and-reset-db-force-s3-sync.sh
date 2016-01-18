#!/usr/bin/env bash

bin/ensure-db.sh
bin/reset-db.sh --force-s3-sync

exit 0
