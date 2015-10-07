#!/bin/bash

# fail on any error
set -o errexit

# always run from project root
script_path=`dirname $0`
cd $script_path/..

# debug
set -x

# run actual commands

    #tools/code-generator/yii dna-content-model-metadata-json --configId=1 | jq '.' > dna/content-model-metadata.json
    tools/code-generator/yii gii/content-model-metadata-helper --template=yii --jsonPathAlias=@project/dna/content-model-metadata.json --overwrite=1 --interactive=0
    mv tools/code-generator/helpers/*.php dna/config/
    tools/code-generator/yii gii/content-model-metadata-model-trait --template=yii --jsonPathAlias=@project/dna/content-model-metadata.json --itemType='*' --interactive=0 --overwrite=1
    mv tools/code-generator/models/metadata/traits/*Trait.php dna/models/metadata/traits/
    bin/generate-models.sh

#echo "Now use git (SourceTree recommended) to stage the relevant generated changes and discard the changes that overwrote customly crafted parts that is not generated."
#todo wait for input

exit 0