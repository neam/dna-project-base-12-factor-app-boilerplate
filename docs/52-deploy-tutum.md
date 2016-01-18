Deploy to Tutum
===============

Zero downtime production deployment routine using Tutum.

## Overview

This document describes the following process:

1. Prepare a build environment
2. Manage git flow release routines in your 12-factor-app git repository
3. Check out source code and install deps in build environment
4. Start up a docker stack and local database to verify that the source code and deps are yielding a working application
5. Create docker images with this verified code, tagging it with the current git commit sha
6. Pushing these docker images to your private tutum docker registry
7. Generating stack files for tutum deployment that includes the relevant deployment config 
8. Deploying the stack on tutum, next to any existing stack that is used in production
9. Zero-downtime blue/green deployment, switching out the old stack for the new

Information on how to set-up your project for this deployment routine is available in `vendor/neam/yii-dna-deployment/README.md`

## Build server first time setup

It is recommended to build and push the docker images on a shell server. Set one up and make sure it has access to relevant source code repositories. If you prefer, you can use your own workstation as a build server. In that case, simply open up a new terminal window locally and run the below build server commands locally instead.

Set up a new user on a server with docker installed and connect:

    ssh _PROJECT_@build._PROJECT_.com

Then, run the following in the build server:

    cd ~
    git clone -b develop git@bitbucket.org:_PROJECT_/_PROJECT_-project.git
    cd ~/_PROJECT_-project
    git clone --recursive -b develop git@bitbucket.org:_PROJECT_/_PROJECT_-product.git _PROJECT_-product
    cd ~/_PROJECT_-project/_PROJECT_-product
    
    git clone https://github.com/neam/docker-stack ~/.docker-stack
    echo 'export PATH=$PATH:~/.docker-stack/cli/docker-stack' >> ~/.bash_profile
    source ~/.bash_profile

Locally:

    scp deploy/config/deploy-prepare-secrets.php _PROJECT_@build._PROJECT_.com:/home/_PROJECT_/_PROJECT_-project/_PROJECT_-product/deploy/config/deploy-prepare-secrets.php

In build server:

    cd ~/_PROJECT_-project/_PROJECT_-product
    docker ps -q | xargs docker kill # <-- use this to make sure no other container is using port 80
    stack/start.sh
    docker-compose run -e PREFER=dist builder stack/src/install-deps.sh
    vendor/bin/docker-stack build-directory-init

Finally, add enough proper config to `~/_PROJECT_-project/_PROJECT_-product-build/.env` to be able to run the 12-factor app in the build server.  

## General deployment routine

First, make sure that everything is tested, committed and pushed. 

Create a new release in SourceTree, call it `YY.MM.R`, where YY is the current year, MM is the current month, and R is an incremental number for the current months' releases, for instance `15.11.2` if you are releasing the 2nd release in nov 2015.

Push the new branch. 

Open up two new terminal windows/tabs (yes, they need to be freshly created so that no old environment variables are hanging around).

In one of them, connect to the build server:

    ssh _PROJECT_@build._PROJECT_.com # <-- Tip: Use [mosh](https://mosh.mit.edu) or screen to prevent build issues due to a flaky internet connection
    cd ~/_PROJECT_-project/_PROJECT_-product

In both terminals, run the following:

    export DATA=%DATA% # change to the deployment that you wish to deploy. for a multi-tenant deployment, use the subdomain including "%DATA%", ie "%DATA%.player" or simply "%DATA%" to first-level subdomains
    export BRANCH_TO_DEPLOY=""
    export COMMITSHA=""
    source deploy/prepare.sh

Then, on the build server:

    cd ~/_PROJECT_-project/_PROJECT_-product
    stack/src/git-pull-recursive.sh
    export BRANCH_TO_DEPLOY=""
    export COMMITSHA=""
    source deploy/prepare.sh

Now check that both terminal windows show the same value for COMMITSHA. This is critical. If not, you may be on different branches, switch branch:

    git branch -r # to display available branches
    git checkout <the-branch>
    export BRANCH_TO_DEPLOY=""
    export COMMITSHA=""
    source deploy/prepare.sh

Then, on the build server, run:

    vendor/bin/docker-stack build-directory-sync # (don't worry about "fatal: Cannot force update the current branch", it is expected)
    cd ../$(basename $(pwd))-build/

Set up a temporary deployment on the build server - Part 1:

    docker ps -q | xargs docker kill # <-- use this to make sure no other container is using port 80
    stack/start.sh

Then, on the build server, run:

    docker-compose run -e PREFER=dist builder stack/src/install-deps.sh

Set up a temporary deployment on the build server - Part 2:

    export DATA=clean-db
    echo "DATA=$DATA" >> .current-local-cli-data-profile
    vendor/bin/docker-stack local run worker /bin/bash bin/ensure-db.sh
    vendor/bin/docker-stack local run worker /bin/bash bin/reset-db.sh

Or:

    export DATA=example # change to a specific data profile that you would like to test
    echo "DATA=$DATA" >> .current-local-cli-data-profile
    vendor/bin/docker-stack local run worker /bin/bash bin/ensure-db.sh
    vendor/bin/docker-stack local run worker /bin/bash bin/reset-db.sh --force-s3-sync
    stack/src/set-writable-local.sh

Now we need to test the build. The base URL is generated by the following command:

    vendor/bin/docker-stack local url router 80 $DATA._PROJECT_.build._PROJECT_.com

The healtch checks are available on:

    vendor/bin/docker-stack local url router 80 $DATA._PROJECT_.build._PROJECT_.com /dna-health-checks.php

When you have verified that everything works, build and push the source code to tutum:
    
    vendor/neam/yii-dna-deployment/deploy/build.sh

Then, locally (the other terminal window):

    deploy/generate-config.sh
    
Follow the instructions printed by the command. When it is time to follow "Then, run one of the following to deploy", run the first group of commands (tutum stack create and start). 

This will start a second, parallel, stack if one was already there before. 

Wait for the new stack to be fully running.

Copy the stack's name (in the style <date><vhost><commitsha>) and run the following:

    deploy/diagnose.sh <stackname>

This will amongst other things list something like the following:

    # Health-checks for first-level backend (Nginx):
    export WEB_PORT=49232
    export WEB_FQDN=cfa9591a-foo.node.tutum.io
    stack/_util/health-checks.sh

This gives the address to where the stack is running before it is connected to the main public domain name (we will do that later). 

To check that things work as expected, add //$WEB_FQDN:$WEB_PORT to relevant Auth0-users settings in order to verify that the REST api works as expected.

TODO: Add section about locking previous data profiles to previously running stacks

TODO: Clarify difference between wildcard-deployments and locked-down deployments etc

# To migrate away from a live maintenance deployment to use the latest code

TODO: Update this section with necessary VIRTUAL_HOST and VIRTUAL_HOST_WEIGHT information.

Open a shell in the PREVIOUS live deployment's phpfiles container (see "Running worker commands in an already deployed stack" below for instructions on how to do that) and make sure that all data and files are synced up propely:

```
export DATA_PROFILES="example
foo-client
"
```

    echo "" > dna/db/uploaded-user-data.log
    for DATA in $DATA_PROFILES; do
        export DATA=$DATA
        bin/upload-current-user-data.sh # Create a backup of the current data and files
    done
    
Fetch the new data ref commands (copy):

    cat dna/db/uploaded-user-data.log

Open a shell in the RECENTLY DEPLOYED deployment's phpfiles container, and run (paste) the new data ref commands.

For every data profile that the deployment hosts, fetch user-generated files and push files to cdn:

    # Fetch user-generated files to the local /files directory + Sync to CDN
    for DATA in $DATA_PROFILES; do
        export DATA=$DATA
        bin/reset-user-generated-files.sh --force-s3-sync
        bin/upload-current-files-to-cdn.sh
    done

When you have made sure that all data profile references are up to date (the databases will be reset to the currently activated data refs): 

    # Reset db and run migrations
    for DATA in $DATA_PROFILES; do
        export DATA=$DATA
        bin/reset-db.sh --force-s3-sync
    done
    
    # OR only run migrations
    for DATA in $DATA_PROFILES; do
        export DATA=$DATA
        bin/migrate.sh 
    done

When the new stack is verified to work as expected on `http://$WEB_FQDN:$WEB_PORT/`, you should link the new stack's web* service to the public router service in tutum (and remove any previous linked web*-service for that deployment) so that it is receiving traffic to it's public domain name. No re-deploy is necessary when changing only a service's links.

If cache busting is not thoroughly implemented, you need to login to Cloudflare, visit the domain name of the updated campaign, choose Cache, and then Purge everything. Also, don't forget to tell every returning visitor to clear their cache... And please enable cache busting everywhere in the project :)

The production release is then complete. 

If something is found to be wrong with the new stack, you may want to link back the previously working stack with the production router service, or deploy a new stack with a fix.

If everything is ok, then go to source tree and finish the release using git flow + push the master and develop branches.

Also, don't forget to upload the deployment metadata to the build server so that we can all access it:

    scp -r deployments/* _PROJECT_@build._PROJECT_.com:/home/_PROJECT_/_PROJECT_-project/_PROJECT_-product/deployments/

## Running worker commands in an already deployed stack

If you don't have the corresponding stack metadata (in the deployments/ directory), get it from the build server where the metadata about the deployments is stored:
    
    scp -r _PROJECT_@build._PROJECT_.com:/home/_PROJECT_/_PROJECT_-project/_PROJECT_-product/deployments/* deployments/

Then, run:

    export DATA=example
    source deploy/prepare.sh
    deploy/diagnose.sh <path-to-deployment>
    
For instance:

    export DATA=example
    source deploy/prepare.sh
    deploy/diagnose.sh deployments/20150505174239foobarfoobarfoobar3ae0903/
    
In the output from the diagnose.sh script you will have a command similar to:

    tutum exec <container-id> /bin/bash # (phpfiles-1)

Run it in order to open a shell in the deployed phpfiles container. Cd into the app directory:

    cd /app

Then run relevant commands, for instance:

    bin/upload-current-user-data.sh # Create a backup of the current files
    
And even: (but be careful - live files and database! only do this on new deployments to install a database that was locally curated before)

    bin/reset-user-generated-files.sh --force-s3-sync
    bin/reset-db.sh --force-s3-sync

## Adding a new DATA profile to a deployed stack

Run the following worker commands in the deployed stack:

    export DATA=newprofile
    bin/create-new-data-profile.sh $DATA
    bin/ensure-db.sh
    bin/migrate.sh
    bin/reset-db.sh
    bin/upload-current-user-data.sh

Note: If the DATA profile should be associated with a subdomain different from the actual data profile, you need to add the new virtual host and associated data profile to the virtual host data profile mapping environment variable `VIRTUAL_HOST_DATA_MAP`. For instance, add `customsubdomain._PROJECT_.com|foodataprofile`
