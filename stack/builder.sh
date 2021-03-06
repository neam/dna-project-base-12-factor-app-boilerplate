#!/bin/bash

# fail on any error
set -o errexit

# always run from project root
script_path=`dirname $0`
cd $script_path/..

# run actual command
echo "Starting /bin/bash in a new builder container. Press ENTER to see the prompt (some sort of bug...)"
docker-stack local run builder /bin/bash

exit 0
