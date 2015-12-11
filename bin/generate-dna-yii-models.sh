#!/bin/bash

# fail on any error
set -o errexit

# always run from project root
script_path=`dirname $0`
cd $script_path/..

# run actual command
echo "* Generating DNA models..."
tools/code-generator/yii dna-model-batch
tools/code-generator/vendor/neam/gii2-dna-project-base-model-generators/yii1_model/copy-models.sh dna/models
echo "* DNA models generated"

exit 0
