#!/usr/bin/env bash

# Usage: <script> <offline|"">

APPVHOST=$(docker-stack local url | sed 's|http://||')

cd ui/angular-frontend/
./develop.sh $APPVHOST $@

exit 0
