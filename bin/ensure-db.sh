#!/usr/bin/env bash

# fail on any error
set -o errexit

# always run from project root
script_path=`dirname $0`
cd $script_path/..

# ensure DATA is set as a shell env var
if [ "$DATA" == "" ]; then

    echo "The environment variable DATA needs to be set"
    exit 1

fi

# make app config available as shell variables
php vendor/neam/php-app-config/export.php | tee /tmp/php-app-config.sh
source /tmp/php-app-config.sh

# ensure we have a root db user
#if [ "$DATABASE_ROOT_USER" == "" ]; then
#    echo "The environment variable DATABASE_ROOT_USER needs to be set"
#    exit 1
#fi

# create new db
echo "* Creating the database $DATABASE_NAME"
vendor/neam/yii-dna-deployment/util/setup-db.sh $DATABASE_HOST $DATABASE_PORT $DATABASE_NAME $DATABASE_USER $DATABASE_PASSWORD | mysql -u$DATABASE_ROOT_USER -p$DATABASE_ROOT_PASSWORD -h$DATABASE_HOST -P$DATABASE_PORT
echo "* Done!"

exit 0
