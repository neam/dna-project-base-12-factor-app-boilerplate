#!/usr/bin/env bash

# shared with the host
chmod -R g+rw .files/
mkdir -p tmp/xdebug/
chmod -R g+rw tmp/xdebug/
chmod -R g+rw tools/code-generator/runtime
chmod -R g+rw tools/code-generator/web/assets

exit 0