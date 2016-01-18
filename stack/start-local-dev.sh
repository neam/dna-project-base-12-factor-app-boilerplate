#!/usr/bin/env bash

# fail on any error
set -o errexit

status=$(docker-machine status default)
if [ "$status" != "Running" ]; then
  docker-machine start default
fi
eval "$(docker-machine env default)"
stack/start.sh
bin/angular-frontend-develop.sh $@

exit 0
