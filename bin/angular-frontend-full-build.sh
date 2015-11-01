#!/usr/bin/env bash

# Usage: <script> <offline|"">

APPVHOST=$(docker-stack local url router 80 192.168.99.100 | sed 's|http://||')

cd ui/angular-frontend/
./full-build.sh $APPVHOST $@

exit 0
