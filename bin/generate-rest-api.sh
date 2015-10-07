#!/bin/bash

# fail on any error
set -o errexit

# always run from project root
script_path=`dirname $0`
cd $script_path/..

# run actual command
tools/code-generator/yii dna-rest-api-batch
cp -r tools/code-generator/modules/yiirestapi/controllers/* external-apis/rest-api/app/modules/v0/controllers/
rm -r tools/code-generator/modules/yiirestapi/controllers/*
tools/code-generator/vendor/neam/gii2-restful-api-generators/yii1_rest_model/copy-models.sh external-apis/rest-api/app/modules/v0/models

exit 0