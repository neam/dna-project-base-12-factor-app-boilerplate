#!/bin/bash

# fail on any error
set -o errexit

# always run from project root
script_path=`dirname $0`
cd $script_path/..

# run actual commands

export DATA=%DATA%
export COMMITSHA=""
export BRANCH_TO_DEPLOY=""
source deploy/prepare.sh
time deploy/pre-build.sh # Takes 3-10 minutes
cd ../$(basename ${PWD/-build/})-build/ # Ensures that we are in the build directory
time deploy/build.sh # Takes 2-10 minutes
cd ../$(basename ${PWD/-build/})/ # Ensures that we are in the main source code directory where we keep the deploy config
export DATA=%DATA%
export COMMITSHA=""
export BRANCH_TO_DEPLOY=""
source deploy/prepare.sh
deploy/generate-config.sh

exit 0
