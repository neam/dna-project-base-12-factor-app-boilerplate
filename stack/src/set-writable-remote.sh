#!/usr/bin/env bash

chmod -R 777 /files/$DATA/media
chmod -R 777 external-apis/rest-api/app/runtime
chmod -R 777 tools/code-generator/runtime
chmod -R 777 tools/code-generator/web/assets

exit 0