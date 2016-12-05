#!/usr/bin/env bash

# fail on any error
set -o errexit

# debug
#set -x

# always run from project root
script_path=`dirname $0`
cd $script_path/..

# ensure DATA is set as a shell env var
if [ "$DATA" == "" ]; then

    echo "The environment variable DATA needs to be set"
    exit 1

fi

# make app config available as shell variables
source vendor/neam/php-app-config/shell-export.sh

# ensure we have a root db user
if [ "$DATABASE_ROOT_USER" == "" ]; then
    echo "The environment variable DATABASE_ROOT_USER needs to be set for a new database to be created"
#    exit 1
fi

# create new db
echo "* Creating the database $DATABASE_NAME"
echo "SELECT 'Access to database is properly set-up'" | mysql -u$DATABASE_USER -p$DATABASE_PASSWORD -h$DATABASE_HOST -P$DATABASE_PORT $DATABASE_NAME && echo "Database already exists" || echo "Database access will now be setup" && vendor/neam/yii-dna-deployment/util/setup-db.sh $DATABASE_HOST $DATABASE_PORT $DATABASE_NAME $DATABASE_USER $DATABASE_PASSWORD | mysql -u$DATABASE_ROOT_USER -p$DATABASE_ROOT_PASSWORD -h$DATABASE_HOST -P$DATABASE_PORT
echo "* Done!"
echo "* To verify that the database exists and access is granted, run the following and ensure no error message is returned:"
echo ""
echo "    echo SELECT 1 | mysql -u$DATABASE_USER -p$DATABASE_PASSWORD -h$DATABASE_HOST -P$DATABASE_PORT $DATABASE_NAME"
echo ""

exit 0
