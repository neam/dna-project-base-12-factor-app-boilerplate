Local Development: Working with data
====================================

Note: All commands below are supposed to be run inside a worker shell:

    stack/shell.sh

As part of setting up the development environment, create your local cli data profile configuration file:

    cp .current-local-cli-data-profile.dist .current-local-cli-data-profile

## Introduction

A local database is configured as part of the dna project base docker stack. Different databases are connected to depending on the current data profile (which is basically a collection of a database, associated media files and backups).  

Copies of the different databases (and associated media files) are backed up and stored on S3, making it easy for developers to aquire a copy of the data of a specific client. Simply set the DATA env var and run reset-db.sh to reset the database of that client to the contents referenced in the `dna/db/migration-base/$DATA/*.*path` files.

To avoid having to set the DATA env var in the worker shell again and again, the DATA env var is read from the local cli data profile configuration file `.current-local-cli-data-profile`.

The database name used is the equivalent of "db_$DATA", so the clean-db DATA profile uses the `db_clean_db` database and so on.

First time a DATA profile is used locally, it's database needs to be created. Run the following:

    bin/ensure-db.sh
    
This will set up the database for the DATA profile.

## To reset to a clean database

To reset the clean database, first make sure that "DATA: clean-db" is the only uncommented data profile in `.current-local-cli-data-profile.yml`, then run:

    bin/reset-db.sh

Note: to reset to anything other than DATA=clean-db, the below instructions needs to be followed first, since you need access to S3 where the data is stored.

## To reset to a database with user generated data:

Make sure to set the `USER_DATA_BACKUP_UPLOADERS_*` config vars in your `secrets.php` file.

Uncomment/set the DATA variable in your `.current-cli-data-profile` to the data environment you want to reset to.

Then, run a normal database reset:

    bin/reset-db.sh

If you have already run this once in the current DATA profile, no data will be re-fetched from S3. To force sync from S3, run:

    bin/reset-db.sh --force-s3-sync

Note: If you have trouble with internet connectivity from inside the shell, run the following, then open a new shell.

    docker-machine ssh default 'echo nameserver 8.8.8.8 > /etc/resolv.conf'

Note: If you get a random 403 permission error for no good reason (for instance `ERROR: S3 error: 403 (RequestTimeTooSkewed)`), it could be because the virtual machine clock and your laptop's clock have gone out of sync (this can happen), which S3 gets picky about. To fix, run:

    docker-machine ssh default 'sudo ntpclient -s -h pool.ntp.org'

## To upload your current data

Enter a shell and run:

    bin/upload-current-user-data.sh

Commit your files in dna/db/migration-base/ - the three files to commit are:

		dna/db/migration-base/{project-name}/schema.filepath
		dna/db/migration-base/{project-name}/data.filepath
		dna/db/migration-base/{project-name}/media.folderpath

Push.

## To create a new DATA profile based on the current data profile

Create a new data profile using the helper script, then upload the current current user-generated data to S3, commit the references and profile-related files in dna (anything with <profileref> in it's path) and push.

    export DATA=clean-db
    bin/new-data-profile.sh <profileref>
    bin/upload-current-user-data.sh
    # then run the three commands to update the data refs
    # commit and push

## Migrations

### How are new migrations created?

    bin/migrate.sh create migration_foo

This puts the empty migration files in the common migrations dir. If you need a migration only for clean-db or only for user-generated you'll need to move it.

### Removing applied migrations in order to remove clutter

Run the following to take the current user-generated schema and copies it to the migration base of the clean-db schema. This makes the default schema to be identical with the user-generated version, and this routine should be done after a release (ie when migrations have been run in production) so that already production-applied migrations can be removed from the current codebase in order to minimize clutter.

    export DATA=example
    vendor/neam/yii-dna-pre-release-testing/shell-scripts/post-release-user-generated-schema-to-clean-db-schema-routine.sh
    # then, manually remove already applied migrations

A comment: Migrations are crucial when it comes to upgrading older deployments to the latest schema. If, however, there are no need of upgrading older deployments to the latest schema and code, migrations may instead add to the maintenance and development routines burden without adding value to the project. This is for instance the case during early development where there are no live deployments, or when all live deployments have run all migrations to date and there is no need to restore from old backups.

## Complete example - Sync database and files from "live" to local dev

One use of data profiles is the ability to keep a single set of "live" database data and media files in one place, and then have all developers replicate this data and media files locally for local development.

1. Make changes in backend and upload files "live"

2. Log into the "live" phpfiles container and upload the current data:

    bin/upload-current-user-data.sh

3. Run the echo scripts at the bottom LOCALLY.

4. Open up a shell locally and run:

    bin/reset-db.sh --force-s3-sync

