Manage Users
============

## Terminology

### User Accounts

These are accounts registered by users themselves or by administrators/moderators and grants the ability to login/authenticate via the UI. 

However, if a user account does not have access to any dataset (see below), the user can not interact with any user-generated data in the application. 

### Datasets / Data profiles / Client accounts

These are isolated sets of user-generated information. 

Users and/or teams may have access to none, one or many of these. 

If the user has access to more than one, the user needs to specify what dataset to use before being able to send requests to the REST API. 

## Setting up new datasets 

Login in to Docker Cloud and open a worker shell in the deployed stack. 

We temporarily need to fetch the clean-db schema since it was not included in the latest deployment:

    export DATA=clean-db
    echo 'DATA-clean-db/ENV-%DATA%.local-motin/2016-11-03_112507/schema.sql.gz' > dna/db/migration-base/clean-db/schema.filepath
    echo 'DATA-clean-db/ENV-%DATA%.local-motin/2016-11-03_112507/data.sql.gz' > dna/db/migration-base/clean-db/data.filepath
    echo 'DATA-clean-db/ENV-%DATA%.local-motin/2016-11-03_112507/media/' > dna/db/migration-base/clean-db/media.folderpath
    vendor/neam/yii-dna-pre-release-testing/shell-scripts/fetch-user-generated-data.sh
    rm -r dna/db/migration-base/clean-db/media

Make the root database credentials available in the shell:

    export DATABASE_ROOT_USER="changeme"
    export DATABASE_ROOT_PASSWORD="changeme"

Set the DATA variable to the default subdomain it will be accessible through:

    export DATA=newprofile

Then, run the following to create the new dataset (Note: There error message from bin/ensure-db.sh is misleading and will be removed later, it just says that there was no database before, which is expected, since we are creating it now)

    bin/new-data-profile.sh $DATA
    bin/ensure-db.sh
    bin/reset-db.sh
    bin/upload-current-user-data.sh

The subdomain newprofile._PROJECT_.com will automatically become available for use with the new dataset. 

If the DATA profile should be associated with additional subdomains, you need to add the new virtual host and associated data profile to the virtual host data profile mapping environment variable `VIRTUAL_HOST_DATA_MAP`. For instance, add `customsubdomain._PROJECT_.com|foodataprofile`.

## Giving Users access to a Dataset / Data profile 

Log in to Auth0 management console, find the user account and add the data profile to the auth0-users that should have access to it, both in the endpoints and permissions sections.

Examples for giving access to the dataset "newprofile" below. 

user_metadata:

```
    {
      ... (keep other entries other than "api_endpoints" intact!) ...
      "api_endpoints": [
        {
          "slug": "newprofile@api._PROJECT_.com",
          "API_BASE_URL": "//api._PROJECT_.com/api",
          "API_VERSION": "v0",
          "DATA": "newprofile"
        }
      ],
      "default_api_endpoint_slug": "newprofile@api._PROJECT_.com"
    }
```

app_metadata:

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
