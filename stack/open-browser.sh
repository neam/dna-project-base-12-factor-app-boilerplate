#!/usr/bin/env bash

if [ "$DATA" == "" ] || [ "$DATA" == "%DATA%" ]; then
  echo "Using DATA=clean-db since no DATA env var was set. To set:"
  echo "  export DATA=example"
  echo "Then run this script again"
  DATA=clean-db
fi

URL="$(docker-stack local url router 80 $DATA.sq.local "$1")"

echo "Opening URL:"
echo $URL

open $URL

exit 0
