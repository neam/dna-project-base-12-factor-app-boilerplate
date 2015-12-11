#!/bin/bash

# fail on any error
set -o errexit

# always run from project root
script_path=`dirname $0`
cd $script_path/..

# run actual commands
tools/code-generator/yii gii/content-model-metadata-model-trait --template=yii --jsonPathAlias=@project/dna/content-model-metadata.json --itemType='*' --interactive=0 --overwrite=1
mv tools/code-generator/models/metadata/traits/*Trait.php dna/models/metadata/traits/

exit 0