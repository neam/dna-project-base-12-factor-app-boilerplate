#!/bin/bash

# fail on any error
set -o errexit

# always run from project root
script_path=`dirname $0`
cd $script_path/..

# run actual commands
tools/code-generator/yii gii/content-model-metadata-helper --template=yii --jsonPathAlias=@project/dna/content-model-metadata.json --overwrite=1 --interactive=0
mv tools/code-generator/helpers/*.php dna/config/

exit 0