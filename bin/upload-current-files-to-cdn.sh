#!/usr/bin/env bash

# fail on any error
set -o errexit

# always run from project root
script_path=`dirname $0`
cd $script_path/..

# upload current media to public files
vendor/neam/yii-dna-deployment/util/upload-current-media-as-public-files.sh

# make app config available as shell variables
source vendor/neam/php-app-config/shell-export.sh

# upload scripts-folder to public files
s3cmd -v --acl-public --config=/tmp/.public-files.s3cfg --recursive sync ui/consumer/www/scripts/ ${PUBLIC_FILES_S3_BUCKET}${PUBLIC_FILES_S3_PATH}scripts/

exit 0
