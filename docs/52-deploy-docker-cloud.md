Deploy to Docker Cloud
===============

Zero downtime production deployment routine using Docker Cloud.

## Overview

This document describes the following process:

**1. Deploy on build server and Docker Cloud**

* A. Manage [git flow](http://nvie.com/posts/a-successful-git-branching-model/) release routines in your 12-factor-app git repository
* B. Check out source code and install deps in build environment
* C. Start up a docker stack and local database to verify that the source code and deps are yielding a working application
* D. Create docker images with this source code, which is verified to work, tagging it with the current git commit sha
* E. Push these docker images to our private docker registry
* F. Generate stack files for docker-cloud deployment that includes the relevant deployment config 
* G. Deploy the stack on docker-cloud, next to any existing stack that is used in production

**2. Decide what data profiles should use which stacks**

* A. Locking previous data profiles to previously running stacks
* B. Unlocking previously locked data profiles to use the latest wildcard stack
  
**3. Use the newly deployed stack for production URLs (Zero-downtime blue/green deployment, switching out the old stack for the new)**

## Installation / first-time set-up

Information on how to set-up your project for this deployment routine is available in `vendor/neam/yii-dna-deployment/README.md`

To prepare a build environment (such as seting up a new build server), follow the instructions in `52-deploy-docker-cloud.misc.md` under "Build server first time setup".

## General deployment routine = For new platform releases

### Step 1 - Deploy on build server and Docker Cloud

First, make sure that everything is tested, committed and pushed. 

#### Manage [git flow](http://nvie.com/posts/a-successful-git-branching-model/) release routines in your 12-factor-app git repository

The repository that we deploy from is `_PROJECT_-product`. Open it up in SourceTree.

Create a new release in SourceTree, call it `YY.MM.R`, where YY is the current year, MM is the current month, and R is an incremental number for the current months' releases, for instance `15.11.2` if you are releasing the 2nd release in nov 2015.

Push the new branch. 

#### Prepare local terminals

Open up two new terminal windows/tabs (yes, they need to be freshly created so that no old environment variables are hanging around).

In one of them, connect to the build server:

    ssh _PROJECT_@build._PROJECT_.com # <-- Tip: Use [mosh](https://mosh.mit.edu) or screen to prevent build issues due to a flaky internet connection
    cd ~/_PROJECT_-project/_PROJECT_-product

In both terminals, run the following:

    export DATA=%DATA% # change to the deployment that you wish to deploy. for a multi-tenant deployment, use the subdomain including "%DATA%", ie "%DATA%.player" or simply "%DATA%" to first-level subdomains

Then, locally:

    export BRANCH_TO_DEPLOY=""
    export COMMITSHA=""
    source deploy/prepare.sh

#### Check out source code and install deps in build environment

On the build server, make sure that you are on the correct branch:

    git fetch
    git branch -r # to display available branches
    
Then checkout the relevant branch, for instance:

    git checkout develop
    # or
    git checkout release/16.05.1

Then, on the build server:

    cd ~/_PROJECT_-project/_PROJECT_-product
    stack/src/git-pull-recursive.sh
    export BRANCH_TO_DEPLOY=""
    export COMMITSHA=""
    source deploy/prepare.sh

Now check that both terminal windows show the same value for COMMITSHA. This is critical since the built images and the stack configuration needs to refer to the same COMMITSHA.
If they differ, make sure that all changes are pushed and that the branches are the same locally as on the build server, then run the above again.

#### Start up a docker stack and local database to verify that the source code and deps are yielding a working application

Then, on the build server, run: (don't worry is you get an error message like "fatal: Cannot force update the current branch", it is not a problem in the build process)

    vendor/bin/docker-stack build-directory-sync
    cd ../$(basename $(pwd))-build/

Set up a temporary deployment on the build server - Part 1:

    stack/recreate.sh

Then, on the build server, run:

    docker-compose run --rm -e PREFER=dist builder stack/src/install-personal-unit-deps.sh

Set up a temporary deployment on the build server - Part 2:

    export DATA=clean-db
    echo "DATA=$DATA" >> .current-local-cli-data-profile
    vendor/bin/docker-stack local run worker /bin/bash bin/ensure-db.sh
    vendor/bin/docker-stack local run worker /bin/bash bin/reset-db.sh

Or:

    export DATA=example # change to a specific data profile that you would like to test
    echo "DATA=$DATA" >> .current-local-cli-data-profile
    vendor/bin/docker-stack local run worker /bin/bash bin/ensure-and-reset-db-force-s3-sync.sh
    stack/src/set-writable-local.sh

Now we need to test the build. The base URL is generated by the following command:

    vendor/bin/docker-stack local url router 80 $DATA._PROJECT_.build._PROJECT_.com

The healtch checks are available on:

    vendor/bin/docker-stack local url router 80 $DATA._PROJECT_.build._PROJECT_.com /dna-health-checks.php

The health checks are available on:

    vendor/bin/docker-stack local url router 80 $DATA._PROJECT_.build._PROJECT_.com /status/dna-health-checks.php

To check that angular frontend will work as expected, log in locally and use the "$DATA@build._PROJECT_.com" (ie "example@build._PROJECT_.com") data environment and try out angular frontend features against this api endpoint. (Requires that the relevant //build._PROJECT_.com data environments and access are to relevant Auth0-users settings)

#### Create docker images with this source code, which is verified to work, tagging it with the current git commit sha

When you have verified that everything works, build and push the source code to docker-cloud:
    
    vendor/neam/yii-dna-deployment/deploy/build.sh
    
In order to use the pushed images as base image for future builds, make sure to copy the resulting previously-pushed-tag specifications to the versioned directory and commit them.

    cp .stack.*.previously-pushed-tag ../_PROJECT_-product/

#### Generate stack files for docker-cloud deployment that includes the relevant deployment config

Then, locally (the other terminal window):

    deploy/generate-config.sh
    
#### Deploy the stack on docker-cloud, next to any existing stack that is used in production

Follow the instructions printed by the above command under "To deploy to docker-cloud". 

This will start a second, parallel, stack if one was already there before. 

Wait for the new stack to be fully running (this is seen in the Docker Cloud web interface in the Stacks section)

### Step 2 - Decide what data profiles should use which stacks

Strategy considerations.

TODO: Clarify difference between wildcard-deployments and locked-down deployments etc

#### Locking previous data profiles to previously running stacks

Example: `dataprofilefoo` was previously using the "latest stack" (wildcard) but is not expected to use new product features in the short term and should be locked to a previously stable stack. 

This way it can be kept online without having to be tested to be compatible for all new platform upgrades.
 
1. Go to the stack that should be locked to `dataprofilefoo` (eg 20151123134836release15113data30d7d5e)
2. Edit stack file and find the section for the web service (eg webrelease15113data30d7d5e)
3. Demote the virtual host settings from wildcard to specifically for `dataprofilefoo`:

From:
    - 'VIRTUAL_HOST=*.__PROJECT__.com'
    - 'VIRTUAL_HOST_DATA_MAP=%DATA%.__PROJECT__.com@%DATA%'
    - VIRTUAL_HOST_WEIGHT=50

To:
    - 'VIRTUAL_HOST=dataprofilefoo.__PROJECT__.com'
    - 'VIRTUAL_HOST_DATA_MAP=%DATA%.__PROJECT__.com@%DATA%'
    - VIRTUAL_HOST_WEIGHT=100

The large virtual host weight is what makes this work. When finding the suitable stack for guldfynd.ratataa.se, this stack will precede the latest (wildcard) stack.

#### Unlocking previously locked data profiles to use the latest wildcard stack

Similar to above but remove the data profile from the previous stack's VIRTUAL_HOST setting so that instead is mapped to the latest wildcard stack.

TODO: Update this section with necessary VIRTUAL_HOST and VIRTUAL_HOST_WEIGHT information.

### Step 3 - Prepare and test the newly deployed stack together with production database contents

There are two different workflows necessary depending on the profile depends on the /files bolume, ie store local files within the stack.

#### Data profiles that do NOT depend on the /files volume

Note: Include only legacy data profiles that do NOT depend on the /files volume, ie store local files within the stack. 

##### Run database migrations

Open a shell locally.

Specify all data profiles that are meant to use the NEWLY DEPLOYED stack, eg:

```
export DATA_PROFILES="example
foo-client
"
```

Run the following to echo the commands to run in the production stack: 

    # Reset db and run migrations
    for DATA in $DATA_PROFILES; do
      echo "export DATA=$DATA;"
      echo "bin/safe-migration-via-upload-user-data-and-reset-db.sh"
      echo "bin/upload-current-files-to-cdn.sh"
    done
    
Open a shell in the NEWLY DEPLOYED stack's phpfiles container.

Reset the log that keeps track:

    echo "" > dna/db/uploaded-user-data.log

Then run the commands echoed above, line by line, and ensure migrations etc are applied properly. If migrations fail, make sure to run:

    cp dna/generated-config/config.php.bak dna/generated-config/config.php

... before attempting to run commands again.

#### Data profiles that depend on the /files volume

Note: Include only legacy data profiles that depend on the /files volume, ie store local files within the stack. These data profiles first need their possibly modified /files-volumes copied from the old stack to s3, then copied from s3 to the new stack.

Open a shell in the PREVIOUS live deployment's phpfiles container (see "Running worker commands in an already deployed stack" below for instructions on how to do that). 

Specify all data profiles that were previously in production in this PREVIOUS stack but are meant to use the newly deployed stack, eg:

```
export DATA_PROFILES="example
foo-client
"
```

Then make sure that all data and files are synced up properly:

    echo "" > dna/db/uploaded-user-data.log
    for DATA in $DATA_PROFILES; do
        export DATA=$DATA
        bin/upload-current-user-data.sh # Create a backup of the current data and files
    done

Fetch the new data ref commands (copy):

    cat dna/db/uploaded-user-data.log | less

Open a shell in the NEWLY DEPLOYED deployment's phpfiles container, and run (paste) the new data ref commands.

Note: Also paste the new data ref commands locally and test-run the below locally before running it in production.

Specify all data profiles that are meant to use the NEWLY DEPLOYED stack, eg:

```
export DATA_PROFILES="example
foo-client
dataprofilefoo
zebra
"
```

Then fetch user-generated files and push files to cdn:

    # Fetch user-generated files to the local /files directory + Sync to CDN
    for DATA in $DATA_PROFILES; do
        export DATA=$DATA
        bin/reset-user-generated-files.sh --force-s3-sync
        bin/upload-current-files-to-cdn.sh
    done

When you have made sure that all data profile references are up to date (the databases will be reset to the currently activated data refs): 

    # Reset db and run migrations
    for DATA in $DATA_PROFILES; do
      echo "export DATA=$DATA;"
      echo "bin/ensure-and-reset-db-force-s3-sync.sh"
    done
    
Run the commands output above, one by one, and ensure migrations etc are applied properly.

### Step 4 - Use the newly deployed stack for production URLs (Zero-downtime blue/green deployment, switching out the old stack for the new)

When the new stack is verified to work as expected, you should link the new stack's web* service to the public router service in docker-cloud (and remove any previous linked web*-service for that deployment) so that it is receiving traffic to it's public domain name:

1. Open up [http://public._PROJECT__.com:1936]() (credentials - see below) and inspect the current HAProxy router state.
2. Log in to Docker Cloud, go to:
    - Stack router-prod
    - Service routerprod
    - Click "Edit"
    - Click "Next: environment variables"
    - (Here: Check the STATS_AUTH credentials for user/pass to public._PROJECT_.com:1936)
    - Remove previous wildcard stack web service (unless it is locked down to a data profile)
    - Link new wildcard stack web service
    - Click "Save"
Note: No docker-cloud service re-deploy is necessary when changing only a service's links.
3. Verify that the new stack is loaded in the HAProxy router by checking [http://public._PROJECT__.com:1936]() again.

If cache busting is not thoroughly implemented, you need to login to Cloudflare, visit the domain name(s) of the updated stacks, choose Cache, and then Purge everything. Also, don't forget to tell every returning visitor to clear their cache... And please enable cache busting everywhere in the project :)

The production release is then complete. 

If something is found to be wrong with the new stack, you may want to link back the previously working stack with the production router service, or deploy a new stack with a fix.

If linking back to the previously working stack, do not forget to restore the databases to their latest backups (which where taken during deployment or - TODO - scheduled backups).

If everything is ok, then go to source tree and finish the release using git flow + push the master and develop branches.

Also, don't forget to upload the deployment metadata to the build server so that we can all access it:

    scp -r deployments/* _PROJECT_@build._PROJECT_.com:/home/_PROJECT_/_PROJECT_-project/_PROJECT_-product/deployments/
