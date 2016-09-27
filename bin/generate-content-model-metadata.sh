#!/bin/bash

# fail on any error
set -o errexit

# always run from project root
script_path=`dirname $0`
cd $script_path/..

# debug
#set -x

# run actual commands
bin/generate-dna-content-model-metadata-helper-classes.sh
bin/generate-dna-item-traits.sh
bin/generate-dna-yii-models.sh
bin/generate-dna-propel-models.sh

echo "* Now use git (SourceTree recommended) to stage the relevant generated changes and discard the changes that overwrote customly crafted parts that is not generated. Press ENTER to continue"
read

exit 0