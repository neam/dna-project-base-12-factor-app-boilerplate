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

Then, run a database reset:

    bin/reset-db.sh

If you have already run this once in the current DATA profile, no data will be re-fetched from S3. To force sync from S3, run:

    bin/ensure-and-reset-db-force-s3-sync.sh

## To ensure and reset to a database to the currently referenced user generated data profile:

    bin/ensure-and-reset-db-force-s3-sync.sh

## To ensure and reset to all databases to the currently referenced user generated data profile:

    bin/ensure-and-reset-db-force-s3-sync-for-all-hosted-data-profiles.sh

## To upload your current data

Enter a shell and run:

    bin/upload-current-user-data.sh

Commit your files in dna/db/migration-base/ - the three files to commit are:

		dna/db/migration-base/{project-name}/schema.filepath
		dna/db/migration-base/{project-name}/data.filepath
		dna/db/migration-base/{project-name}/media.folderpath

Push.

## To safely migrate current data

The following first creates a backup, syncs it to S3 then resets the database and applies all migrations:

    bin/safe-migration-via-upload-user-data-and-reset-db.sh

## To create a new DATA profile based on the "clean-db" data profile

Set the DATA variable to the default subdomain it will be accessible through:

    export DATA=newprofile

Then, run the following to create the new dataset (Note: There error message regarding database access is misleading and will be removed later, it just says that there was no database before, which is expected, since we are creating it now)

    bin/ensure-new-data-profile.sh $DATA

Upload the current current user-generated data to S3:

    bin/upload-current-user-data.sh

Add the new data profile to .env.dist's listing of LOCAL_OFFLINE_DATA and HOSTED_DATA_PROFILES.

Commit the references and profile-related files in dna (anything with <profileref> in it's path) and push.

Then log in to Auth0 management console, find all developer user accounts and add the data profile to their access metadata, both in the endpoints and permissions sections.

Examples for giving access to the dataset "newprofile":

```
    {
      ... (keep other entries other than "api_endpoints" intact!) ...
      "api_endpoints": [
          ... other api_endpoints ...
        {
          "slug": "newprofile@local",
          "DATA": "newprofile"
        },
        {
          "slug": "newprofile@api.adoveo.com",
          "API_BASE_URL": "//api.adoveo.com/api",
          "API_VERSION": "v0",
          "DATA": "newprofile"
        },
      ]
    }
```

## Adding a new client account (DATA profile) live for use by external users

See [61-manage-users.md](./61-manage-users.md).

## Migrations

### How are new migrations created?

    bin/create-migration.sh migration_foo

This puts the empty migration files in the dna/generated-migrations directory.

### Removing applied migrations in order to remove clutter

Run the following to take the current user-generated schema and copies it to the migration base of the clean-db schema. This makes the default schema to be identical with the user-generated version, and this routine should be done after a release (ie when migrations have been run in production) so that already production-applied migrations can be removed from the current codebase in order to minimize clutter.

    export DATA=example
    vendor/neam/yii-dna-pre-release-testing/shell-scripts/post-release-user-generated-schema-to-clean-db-schema-routine.sh
    # then, manually remove already applied migrations

A comment: Migrations are crucial when it comes to upgrading older deployments to the latest schema. If, however, there are no need of upgrading older deployments to the latest schema and code, migrations may instead add to the maintenance and development routines burden without adding value to the project. This is for instance the case during early development where there are no live deployments, or when all live deployments have run all migrations to date and there is no need to restore from old backups.

## Complete example - Sync database and files from "live" to local dev or build server (for pre-release testing)

One use of data profiles is the ability to keep a single set of "live" database data and media files in one place, and then have all developers replicate this data and media files locally for local development.

The "example" data profile is used here. Adapt to the data profile you are interested in copying. 

1. Make changes in live angular frontend

2. Log into the "live" phpfiles container (can be done by logging in to Docker Cloud, navigation to the relevant stack, then to the "phpfiles" service, then click the `>_` button on the phpfiles-container's row) and upload the current data:

    bash
    export DATA=example;
    bin/upload-current-user-data.sh

3. If using the Docker Cloud browser-based shell in Classic UI mode, select the three echo scripts at the bottom, right-click, choose "Copy", paste them into a plain-text editor and remove the extra new-lines, then copy the fixed echo scripts once again before pasting them locally.

Tip: The latest new data ref commands can also be checked in the log:

    cat dna/db/uploaded-user-data.log | less

4. If the data profile does not already exist locally, create a new DATA profile based on the "clean-db" data profile (see above)

5. Paste / run these echo scripts LOCALLY or on the build server (depending on where you are testing).

Example (do not copy paste this example, instead, use the echo scripts copied above):

    echo 'DATA-example/ENV-deployments/release_16.03.1-%DATA%/2016-03-15_154857/schema.sql.gz' > dna/db/migration-base/example/schema.filepath                      
    echo 'DATA-example/ENV-deployments/release_16.03.1-%DATA%/2016-03-15_154857/data.sql.gz' > dna/db/migration-base/example/data.filepath                          
    echo 'DATA-example/ENV-deployments/release_16.03.1-%DATA%/2016-03-15_154857/media/' > dna/db/migration-base/example/media.folderpath                            

6. Open up a shell locally (`stack/shell.sh`) and run:

    export DATA=example
    bin/ensure-and-reset-db-force-s3-sync.sh
