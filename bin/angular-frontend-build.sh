#!/usr/bin/env bash

# Usage: <script> <offline|"">

APPVHOST=$(docker-stack local url router 80 127.0.0.1 | sed 's|http://||')

cd ui/angular-frontend/
./build.sh $APPVHOST $@

exit 0
