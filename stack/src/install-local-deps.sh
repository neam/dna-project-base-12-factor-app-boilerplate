#!/bin/bash

# show commands
set -x

# fail on any error
set -o errexit

if [ "$PREFER" == "" ]; then
  PREFER=source
fi

cd ui/angular-frontend
npm install
cd -

exit 0
