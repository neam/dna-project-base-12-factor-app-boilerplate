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

Note: If you have trouble with internet connectivity from inside the shell, run the following, then open a new shell.

    docker-machine ssh default 'echo nameserver 8.8.8.8 > /etc/resolv.conf'

Note: If you get a random 403 permission error for no good reason (for instance `ERROR: S3 error: 403 (RequestTimeTooSkewed)`), it could be because the virtual machine clock and your laptop's clock have gone out of sync (this can happen), which S3 gets picky about. To fix, run:

    docker-machine ssh default 'sudo ntpclient -s -h pool.ntp.org'

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

## To create a new DATA profile based on the current data profile

Create a new data profile locally using the helper script, then upload the current current user-generated data to S3, commit the references and profile-related files in dna (anything with <profileref> in it's path) and push.

    export DATA=newprofile
    bin/new-data-profile.sh $DATA
    # run the three commands output by the above command
    bin/ensure-db.sh
    bin/reset-db.sh
    bin/upload-current-user-data.sh
    # add the new data profile to .env.dist's listing of LOCAL_OFFLINE_DATA and HOSTED_DATA_PROFILES
    # commit the new files and push

## Adding a new DATA profile to a deployed stack

This is done to make a new data profile available in an already deployed stack, which was deployed before the data profile was created and committed locally.

Run the following worker commands in the deployed stack:

    export DATABASE_ROOT_USER="changeme"
    export DATABASE_ROOT_PASSWORD="changeme"
    export DATA=newprofile
    bin/new-data-profile.sh $DATA
    bin/ensure-db.sh
    bin/reset-db.sh
    bin/upload-current-user-data.sh

Don't forget follow the instructions under "To create a new DATA profile based on the current data profile" above in order to keep track of the data profile in git. 

Also, you'll need to add the data profile to the auth0-users that should have access to it, both in the endpoints and permissions sections:

```
    {
      "slug": "newprofile@local",
      "DATA": "newprofile"
    },
    {
      "slug": "newprofile@api._PROJECT_.com",
      "API_BASE_URL": "//api._PROJECT_.com/api",
      "API_VERSION": "v0",
      "DATA": "newprofile"
    },
```

```
{
  "r0": {
    "permissions": {
      ... (the other data profiles) ...
      "newprofile": {
        "superuser": 1,
        "groups": []
      }
    }
  }
}
```

Note: If the DATA profile should be associated with a subdomain different from the actual data profile, you need to add the new virtual host and associated data profile to the virtual host data profile mapping environment variable `VIRTUAL_HOST_DATA_MAP`. For instance, add `customsubdomain.adoveo.com|foodataprofile`

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

4. Paste / run these echo scripts LOCALLY or on the build server (depending on where you are testing).

Example (do not copy paste this example, instead, use the echo scripts copied above):

    echo 'DATA-example/ENV-deployments/release_16.03.1-%DATA%/2016-03-15_154857/schema.sql.gz' > dna/db/migration-base/example/schema.filepath                      
    echo 'DATA-example/ENV-deployments/release_16.03.1-%DATA%/2016-03-15_154857/data.sql.gz' > dna/db/migration-base/example/data.filepath                          
    echo 'DATA-example/ENV-deployments/release_16.03.1-%DATA%/2016-03-15_154857/media/' > dna/db/migration-base/example/media.folderpath                            

5. Open up a shell locally (`stack/shell.sh`) and run:

    export DATA=example
    bin/ensure-and-reset-db-force-s3-sync.sh
