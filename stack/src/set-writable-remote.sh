#!/usr/bin/env bash

chmod -R 777 /local-tmp-files
if [ -d "/files" ]; then
  chmod -R 777 /files
fi
if [ -d "tools/code-generator/" ]; then
  chmod -R 777 tools/code-generator/runtime
  chmod -R 777 tools/code-generator/web/assets
fi

exit 0