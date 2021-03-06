Deploy to Docker Cloud - Via new stack = General deployment routine = For new platform releases
===============================================================================================

Zero downtime production deployment routine using Docker Cloud.

## Overview

This document describes the following process:

**1. Build docker images locally**

* A. Manage [git flow](http://nvie.com/posts/a-successful-git-branching-model/) release routines in your 12-factor-app git repository
* B. Prepare deployment variables in a new terminal window
* C. Sync and prepare a clean build directory not contaminated by unpushed changes
* D. Start up a docker stack and local database to verify that the source code and deps to deploy are yielding a working application
* E. Create docker images with this source code, which is verified to work, tagging it with the current git commit sha

**2. Deploy on Docker Cloud as new stack**

* A. Push these docker images to our private docker registry
* B. Generate stack files for docker-cloud deployment that includes the relevant deployment config 
* C. Deploy the stack on docker-cloud, next to any existing stack that is used in production

**3. Decide what data profiles should use which stacks**

* A. Locking previous data profiles to previously running stacks
* B. Unlocking previously locked data profiles to use the latest wildcard stack

**4. Prepare and test the newly deployed stack together with production database contents**

* A. Run tests with a copy of the latest production database contents (TODO)

**5. Use the newly deployed stack for production URLs (Zero-downtime blue/green deployment, switching out the old stack for the new)**

* A. Run database migrations live as necessary
* B. Make the new stack take over the traffic
* C. Clear Cloudflare cache
* D. Reverting back in case of problems
* E. Post-release administration

## Installation / first-time set-up

Information on how to set-up your project and infrastructure for this deployment routine is available in `vendor/neam/yii-dna-deployment/README.md`

## Deployment routine

### Step 1 - Build docker images locally

First, make sure that everything is tested, committed and pushed. 

#### Manage [git flow](http://nvie.com/posts/a-successful-git-branching-model/) release routines in your 12-factor-app git repository

The repository that we deploy from is `_PROJECT_-product`. Open it up in SourceTree.

Create a new release in SourceTree, call it `YY.MM.R`, where YY is the current year, MM is the current month, and R is an incremental number for the current months' releases, for instance `15.11.2` if you are releasing the 2nd release in nov 2015.

Push the new branch. 

#### Prepare deployment variables in a new terminal window

Open a new shell, navigate to `_PROJECT_-product` and run:

    export DATA=%DATA%
    export COMMITSHA=""
    export BRANCH_TO_DEPLOY=""
    source deploy/prepare.sh

#### Sync and prepare a clean build directory not contaminated by unpushed changes 

    time deploy/pre-build.sh # Takes 3-10 minutes

(don't worry is you get an error message like "fatal: Cannot force update the current branch", it is not a problem in the build process)

#### Start up a docker stack and local database to verify that the source code and deps are yielding a working application

Start up a docker stack and local database:

    cd ../$(basename ${PWD/-build/})/ # Ensures that we are in the main source code directory
    stack/stop.sh
    cd ../$(basename ${PWD/-build/})-build/ # Ensures that we are in the build directory
    stack/recreate.sh
    export DATA=example # adjust to data profile you want to confirm to work, repeat for multiple profiles if necessary
    vendor/bin/docker-stack local run -e DATA=$DATA worker /bin/bash bin/ensure-and-reset-db-force-s3-sync.sh

Now we need to test the source code to be built.

The health checks are available on:

    stack/open-browser.sh /dna-health-checks.php

To check that angular frontend will work as expected, log in locally and use the "$DATA@api._PROJECT_.127.0.0.1.xip.io" (ie "example@api._PROJECT_.127.0.0.1.xip.io") data environment and try out angular frontend features against this api endpoint. (Requires that the relevant //api._PROJECT_.127.0.0.1.xip.io data environments and access are to relevant Auth0-users settings)

### Step 2 - Deploy on Docker Cloud as new stack

#### Create docker images with this source code, which is verified to work, tagging it with the current git commit sha + Push these docker images to our private docker registry

When you have verified that everything works, build and push the docker images containing the source code to deploy:

    cd ../$(basename ${PWD/-build/})-build/ # Ensures that we are in the build directory
    time deploy/build.sh # Takes 2-10 minutes

At the end of the deploy/build.sh script, the docker images are pushed to the Docker registry.  

Later (skip this for now):
In order to use the pushed images as base image for future builds, make sure to copy the resulting previously-pushed-tag specifications to the versioned directory and commit them.

    cp .stack.*.previously-pushed-tag ../_PROJECT_-product/

#### Generate stack files for docker-cloud deployment that includes the relevant deployment config

Run:

    cd ../$(basename ${PWD/-build/})/ # Ensures that we are in the main source code directory where we keep the deploy config
    export DATA=%DATA%
    export COMMITSHA=""
    export BRANCH_TO_DEPLOY=""
    source deploy/prepare.sh
    deploy/generate-config.sh

#### Deploy the stack on docker-cloud, next to any existing stack that is used in production

Follow the instructions printed by the above command under "To deploy to docker-cloud". 

This will start a new stack next to the ones already launched previously in the project history. 

Wait for the new stack to be fully running (this is seen in the Docker Cloud web interface in the Stacks section)

### Step 3 - Decide what data profiles should use which stacks

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

### Step 4 - Prepare and test the newly deployed stack together with production database contents

#### Run tests with a copy of the latest production database contents

TODO

### Step 5 - Use the newly deployed stack for production URLs (Zero-downtime blue/green deployment, switching out the old stack for the new)

#### Initiate new data environments

Open a shell locally.

Specify all data profiles that have never been deployed previously:

```
export DATA_PROFILES="example
foo-client
"
```

Run the following locally to echo the commands to run in the production stack: 

    # Reset db and run migrations
    for DATA in $DATA_PROFILES; do
      echo "export DATA=$DATA;bin/ensure-and-reset-db-force-s3-sync.sh"
    done
    
Open a shell in the NEWLY DEPLOYED stack's phpfiles container.

Then run the commands echoed above, line by line, and ensure migrations etc are applied properly. 

#### Upgrade previous data environments = Run database migrations live as necessary

This step is skipped if no changes to the existing database is necessary for the release.

There are two different workflows necessary depending on the profile depends on the /files volume, ie store local files within the stack.

##### Data profiles that do NOT depend on the /files volume

Note: Include only data profiles that do NOT depend on the /files volume, ie store local files within the stack. 

Open a shell locally.

Specify all data profiles that are to be upgraded, it meant to use the NEWLY DEPLOYED stack, eg:

```
export DATA_PROFILES="another-example-profile
another-client
"
```

Run the following locally to echo the commands to run in the production stack: 

    # Reset db and run migrations
    for DATA in $DATA_PROFILES; do
      echo "export DATA=$DATA;bin/safe-migration-via-upload-user-data-and-reset-db.sh"
    done
    
Open a shell in the NEWLY DEPLOYED stack's phpfiles container.

Reset the log that keeps track:

    echo "" > dna/db/uploaded-user-data.log

Then run the commands echoed above, line by line, and ensure migrations etc are applied properly. 

Note that the migrations may affect live traffic, so you want to make the new stack take over the traffic as soon as possible after running the database migrations. 

##### Data profiles that depend on the /files volume

See [53-deploy-docker-cloud.misc.md](53-deploy-docker-cloud.misc.md).

#### Make the new stack take over the traffic

When the new stack is verified to work as expected, you should link the new stack's web* service to the public router service in docker-cloud (and remove any previous linked web*-service for that deployment) so that it is receiving traffic to it's public domain name:

1. Open up [http://public._PROJECT__.com:1936]() (credentials - see below) and inspect the current HAProxy router state.
2. Log in to Docker Cloud, go to:
    - Stack prod-router
    - Service prod-router
    - Click "Edit"
    - Click "Links" in the menu to the right
    - (Here: Check the STATS_AUTH credentials for user/pass to public._PROJECT_.com:1936)
    - Remove previous wildcard stack web service (unless it is locked down to a data profile)
    - Link new wildcard stack web service (choose and press "+")
    - Click "Save Changes"
    Note: No docker-cloud service re-deploy is necessary when changing only a service's links.
3. Verify that the new stack is loaded in the HAProxy router by checking [http://public._PROJECT__.com:1936]() again.

#### Clear Cloudflare cache

If your stack serves content behind the Cloudflare cache and cache busting is not thoroughly implemented, you need to login to Cloudflare, visit the domain name(s) of the updated stacks, choose Cache, and then Purge everything. Also, don't forget to tell every returning visitor to clear their cache... And please enable cache busting everywhere in the project :)

The production release is then complete. 

#### Reverting back in case of problems

If something is found to be wrong with the new stack, you may want to link back the previously working stack with the production router service, or deploy a new stack with a fix.

If linking back to the previously working stack, do not forget to restore the databases to their latest backups (which where taken during deployment or - TODO - scheduled backups).

#### Post-release administration

If everything is ok, then go to source tree and finish the release using git flow + push the master and develop branches.

Also, don't forget to upload the deployment metadata to a commonly shared SFTP server so that we can all access it:

    scp -r deployments/* _PROJECT_@build._PROJECT_.com:/home/_PROJECT_/_PROJECT_-project/_PROJECT_-product/deployments/
