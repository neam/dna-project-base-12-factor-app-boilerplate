#!/bin/bash

# show commands
set -x

# fail on any error
set -o errexit

if [ "$PREFER" == "" ]; then
  PREFER=source
fi

# install products' deps if available
DIRECTORY=ui/angular-frontend
if [ -d "$DIRECTORY" ]; then
  cd $DIRECTORY
  npm install
  cd -
fi

exit 0
