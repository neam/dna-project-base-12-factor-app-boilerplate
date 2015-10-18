#!/usr/bin/env bash

# fail on any error
set -o errexit

docker-machine start default
eval "$(docker-machine env default)"
stack/start.sh
bin/angular-frontend-develop.sh $@

exit 0
