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

# propel-related
cd dna
vendor/bin/propel config:convert
vendor/bin/propel reverse
cp generated-reversed-database/schema.xml .
echo "* Pristine propel schema generated. Manually revert changes to main shema.xml before continuing. Press ENTER to continue"
read
echo "* Generating propel models..."
rm -r generated-classes/propel/models/Base/*.php || true
rm -r generated-classes/propel/models/Map/*.php || true
vendor/bin/propel model:build
echo "* Propel models generated"

exit 0
