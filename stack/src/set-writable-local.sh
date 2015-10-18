#!/usr/bin/env bash

chmod -R g+rw .files/
chmod -R g+rw tmp/xdebug/
chmod -R g+rw external-apis/rest-api/app/runtime
chmod -R g+rw tools/code-generator/runtime
chmod -R g+rw tools/code-generator/web/assets

exit 0