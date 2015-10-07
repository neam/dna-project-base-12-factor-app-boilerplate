#!/bin/bash

# fail on any error
set -o errexit

# always run from project root
script_path=`dirname $0`
cd $script_path/..

# run actual command
export CODE_GENERATOR_BOOTSTRAP_INCLUDE_ALIAS=@project/ui/angular-frontend-dna/provider-bootstrap.php
tools/code-generator/yii dna-angular-workflow-ui-batch

exit 0