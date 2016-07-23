Deploy to Docker Cloud via Hotfix - Patching existing stack = Hotfix deployment routine
=======================================================================================

Patching existing releases/stacks without creating an entirely new stack. The closest to "change files on the server" as we get.

Introduces a short downtime (5-30 seconds) when stack services are restarted to acknowledge the patched images.

## Overview

This document describes the following process:

**1. Build docker images locally**

* A. Manage [git flow](http://nvie.com/posts/a-successful-git-branching-model/) hotfix routines in your 12-factor-app git repository
* B. Prepare deployment variables in a new terminal window
* C. Sync and prepare a clean build directory not contaminated by unpushed changes
* D. Start up a docker stack and local database to verify that the source code and deps to deploy are yielding a working application
* E. Create docker images with this source code, which is verified to work, tagging it with the current git commit sha

**2. Deploy on Docker Cloud within an existing stack**

* A. Push these docker images to our private docker registry, replacing the previously pushed images
* B. Re-deploy the existing stack on docker-cloud for the new images to take effekt
* C. Run database migrations live as necessary

## Installation / first-time set-up

Requires an existing stack, see [Docker Cloud - Via new stack](51-deploy-docker-cloud.via-new-stack.md).

## Deployment routine

### Step 1 - Build docker images locally

First, make sure that everything is tested, committed and pushed. 

#### Manage [git flow](http://nvie.com/posts/a-successful-git-branching-model/) hotfix routines in your 12-factor-app git repository

The repository that we deploy from is `_PROJECT_-product`. Open it up in SourceTree.

Create a hotfix branch (Source Tree -> Git Flow -> Start a New Hotfix). If it already exists, switch to it. 

For the Hotfix Version, use the format `YY.MM.Rx` where `YY.MM.R` corresponds to the release being hotfixed, and `x` being a letter or reference that refers to the hotfix. Hotfixes are meant to be pushed and redeployed to the existing stack's docker images. 

Commit, cherry-pick and/or merge in the changes to be patched to the existing release.

Push the new branch. 

#### Prepare deployment variables in a new terminal window

Open a new shell and navigate to `_PROJECT_-product`.

Configure the session to patch an existing stack. In this example, the release "16.03.1" is being patched for the 1st time (hotfix 16.03.1b).

    export COMMITSHA='3209ce7' # this is found in docker-cloud's phpfiles image name for the existing stack, for instance docker-cloud.co/_PROJECT_/_PROJECT_-web-src-php:git-commit-3209ce7
    export BRANCH_TO_DEPLOY='release/16.03.1'
    source deploy/prepare.sh

#### Sync and prepare a clean build directory not contaminated by unpushed changes 

    time deploy/pre-build.sh # Takes 3-10 minutes

(don't worry is you get an error message like "fatal: Cannot force update the current branch", it is not a problem in the build process)

#### Start up a docker stack and local database to verify that the source code and deps are yielding a working application

Same as in [51-deploy-docker-cloud.via-new-stack.md](51-deploy-docker-cloud.via-new-stack.md)

#### Create docker images with this source code, which is verified to work, tagging it with the current git commit sha

When you have verified that everything works (also in frontend etc), build images from the source code:

    time deploy/build.sh # Takes 3-4 minutes

### Step 2 - Deploy on Docker Cloud within an existing stack

#### Push these docker images to our private docker registry, replacing the previously pushed images

At the end of the output from the deploy/build.sh script, the commands to push the images are shown. Run them. 

#### Re-deploy the existing stack on docker-cloud for the new images to take effekt

Log in to Docker Cloud, go to the relevant stack and redeploy the services "phpfiles", "phpha", and "web..." in the stack - takes 3-4 minutes.

For extra safety, you may redeploy only "phpfiles" first (only used by us devs to run commands within), and when it is done, use the terminal to enter it and double-check whatever needs to be double-checked before deploying "phpha" and "web..." services. 

#### Run database migrations live as necessary

Enter the docker-cloud web shell for phpfiles and apply migrations as necessary (see [51-deploy-docker-cloud.via-new-stack.md](51-deploy-docker-cloud.via-new-stack.md))

#### Clear Cloudflare cache

Until cache busting is implemented in `ui/consumer`, we need to login to Cloudflare, visit the domain names of the updated campaigns, choose Cache, and then Purge everything. Also, don't forget to tell every returning visitor to clear their cache...

#### Post-release administration

When all is well, finish the hotfix branch in SourceTree, push develop and master branches and delete the remote hotfix branch.
