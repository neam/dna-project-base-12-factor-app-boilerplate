#!/usr/bin/env bash

# Usage: <script> <offline|"">

APPVHOST=$(docker-stack local url router 80 docker._PROJECT_.local | sed 's|http://||')

cd ui/angular-frontend/
./develop.sh $APPVHOST $@

exit 0
