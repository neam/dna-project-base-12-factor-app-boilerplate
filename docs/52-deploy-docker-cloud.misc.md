Deploy to Docker Cloud - Miscellaneous documentation
=============================================

### Hotfix releases

Patching existing releases/stacks without creating an entirely new stack. The closest to "change files on the server" as we get.

1. Create a hotfix branch (Source Tree -> Git Flow -> Start a New Hotfix). If it already exists, switch to it. 

For the Hotfix Version, use the format `YY.MM.Rx` where `YY.MM.R` corresponds to the release being hotfixed, and `x` being a letter or reference that refers to the hotfix. Hotfixes are meant to be pushed and redeployed to the existing stack's docker images. 

Commit, cherry-pick and/or merge in the changes to be patched to the existing release. 

Push the new branch. 

2. Log in to the build server

    ssh _PROJECT_@build._PROJECT_.com # <-- Tip: Use [mosh](https://mosh.mit.edu) or screen to prevent build issues due to a flaky internet connection
    cd ~/_PROJECT_-project/_PROJECT_-product

3. Check out the hotfix branch

    git fetch
    git branch -r # to display available branches
    export HOTFIX_BRANCH='hotfix/16.03.1b'
    git checkout $HOTFIX_BRANCH
    
4. Configure the session to patch an existing stack. In this example, the release "16.03.1" is being patched for the 1st time (hotfix 16.03.1b).
    
    export COMMITSHA='3209ce7' # this is found in docker-cloud's phpfiles image name for the existing stack, for instance docker-cloud.co/_PROJECT_/_PROJECT_-web-src-php:git-commit-3209ce7
    export BRANCH_TO_DEPLOY='release/16.03.1'
    source deploy/prepare.sh

5. Pull latest changes and build images as per above. Shortcut scripts:

    ~/bin/_PROJECT_-project-pull.sh # Takes 2-3 minutes
    cd ~/_PROJECT_-project/_PROJECT_-web-build
    
Verify that the code works:

    stack/recreate.sh
    export DATA=example
    vendor/bin/docker-stack local run -e DATA=$DATA worker /bin/bash bin/ensure-and-reset-db-force-s3-sync.sh
    vendor/bin/docker-stack local url router 80 $DATA._PROJECT_.build._PROJECT_.com /api/

... then build the docker images:

    vendor/neam/yii-dna-deployment/deploy/build.sh # Takes 3-4 minutes
    
6. Log in to Docker Cloud, go to the relevant stack and redeploy the services "phpha", "phpfiles" and "web..." in the latest stack - takes 3-4 minutes.

7. Enter the docker-cloud web shell for phpfiles and apply migrations as necessary

    export DATA=example;
    bin/safe-migration-via-upload-user-data-and-reset-db.sh

8. Until cache busting is implemented, we need to login to Cloudflare, visit the domain names of the updated campaigns, choose Cache, and then Purge everything. Also, don't forget to tell every returning visitor to clear their cache...

9. When all is well, finish the hotfix branch in SourceTree, push develop and master branches and delete the remote hotfix branch.

### Build server first time setup

This section decribes how to set up at shell server to act as a build server. This may be useful if your personal workstation has a slow internet connection and thus not be able to install dependencies fast nor push large docker layers. 
If you prefer, you can use your own workstation to build the project instead. In that case, skip this section and simply open up a new terminal window locally to run the commands under "Initiating the build directory" locally instead.

First, set up a server with shell access and make sure it has access to relevant source code repositories. 

Set up a new user on a server with docker installed and connect:

    ssh _PROJECT_@build._PROJECT_.com

Then, run the following in the build server:

    cd ~
    git clone -b develop git@bitbucket.org:_PROJECT_/_PROJECT_-project.git
    cd ~/_PROJECT_-project
    git clone --recursive -b develop git@bitbucket.org:_PROJECT_/_PROJECT_-product.git _PROJECT_-product
    cd ~/_PROJECT_-project/_PROJECT_-product
    
    git clone https://github.com/neam/docker-stack ~/.docker-stack
    echo 'export PATH=$PATH:~/.docker-stack/cli' >> ~/.bash_profile
    source ~/.bash_profile

Locally:

    scp deploy/config/deploy-prepare-secrets.php _PROJECT_@build.neamlabs.com:/home/_PROJECT_/_PROJECT_-project/_PROJECT_-product/deploy/config/deploy-prepare-secrets.php

In build server:

    cd ~/_PROJECT_-project/_PROJECT_-product
    stack/recreate.se
    docker-compose run --rm -e PREFER=dist builder stack/src/install-personal-unit-deps.sh
    
Then, initiate the build directory as described below.
    
### Initiating the build directory

This creates a parallel directory next to the current directory, suffixed with "-build". It will be a clean directory where all components are installed from version control and as described by dependency management files. The practice to build in a clean directory is essential in order to avoid accidentally building project images that include uncommitted or otherwise locally modified files. 

    vendor/bin/docker-stack build-directory-init

After this, add enough proper config to `~/_PROJECT_-project/_PROJECT_-product-build/.env` to be able to run the 12-factor app from the build directory.

Lastly, make sure to be logged in on the docker registry so that pushing is possible:

    source vendor/neam/php-app-config/shell-export.sh
    docker login --username="$DOCKERCLOUD_USER" --password="$DOCKERCLOUD_PASSWORD"

### Installing docker-cloud cli

Add the following to your .bash_profile or similar:

    alias docker-cloud="docker run -it -e DOCKERCLOUD_USER=$DOCKERCLOUD_USER -e DOCKERCLOUD_PASS=$DOCKERCLOUD_PASS -v $(PWD):/pwd -w="/pwd" -v ~/.docker:/root/.docker:ro --rm dockercloud/cli"

Then confirm that the docker-cloud cli works by running

    docker-cloud -v

### Troubleshooting: Checking that a newly deployed stack works - using the cli only

Copy the stack's name (in the style <date><vhost><commitsha>) and run the following:

    deploy/diagnose.sh <stackname>

This will amongst other things list something like the following:

    # Health-checks for first-level backend (Nginx):
    export WEB_PORT=49232
    export WEB_FQDN=cfa9591a-foo.node.docker-cloud.io
    stack/_util/health-checks.sh

This gives the address to where the stack is running before it is connected to the main public domain name (we will do that later). 

To check that things work as expected (TODO - restore this):

    open http://$WEB_FQDN:$WEB_PORT/

To check that campaign manager will work as expected, add //$WEB_FQDN:$WEB_PORT to relevant Auth0-users settings and try out campaign manager features against this api endpoint.
