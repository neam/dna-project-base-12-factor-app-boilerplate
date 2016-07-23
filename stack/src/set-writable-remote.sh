#!/usr/bin/env bash

if [ -d "/files/$DATA/media" ]; then
  chmod -R 777 /files/$DATA/media
fi
if [ -d "tools/code-generator/" ]; then
  chmod -R 777 tools/code-generator/runtime
  chmod -R 777 tools/code-generator/web/assets
fi

exit 0