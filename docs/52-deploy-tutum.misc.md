Deploy to Tutum - Miscellaneous documentation
=============================================

### Hotfix releases

Patching existing releases/stacks without creating an entirely new stack. The closest to "change files on the server" as we get.

1. Create a hotfix branch (Source Tree -> Git Flow -> Start a New Hotfix). If it already exists, switch to it. 

For the Hotfix Version, use the format `YY.MM.Rx` where `YY.MM.R` corresponds to the release being hotfixed, and `x` being a letter or reference that refers to the hotfix. Hotfixes are meant to be pushed and redeployed to the existing stack's docker images. 

Commit, cherry-pick and/or merge in the changes to be patched to the existing release. 

Push the new branch. 

2. Log in to the build server

    ssh dokku@build._PROJECT_.com # <-- Tip: Use [mosh](https://mosh.mit.edu) or screen to prevent build issues due to a flaky internet connection
    cd ~/_PROJECT_-project/_PROJECT_-web

3. Check out the hotfix branch

    git fetch
    git branch -r # to display available branches
    export HOTFIX_BRANCH='hotfix/16.03.1b'
    git checkout $HOTFIX_BRANCH
    
4. Configure the session to patch an existing stack. In this example, the release "16.03.1" is being patched for the 1st time (hotfix 16.03.1b).
    
    export COMMITSHA='3209ce7' # this is found in tutum's phpfiles image name for the existing stack, for instance tutum.co/_PROJECT_/_PROJECT_-web-src-php:git-commit-3209ce7
    export BRANCH_TO_DEPLOY='release/16.03.1'
    source deploy/prepare.sh

5. Pull latest changes and build images as per above. Shortcut scripts:

    ~/bin/_PROJECT_-project-pull.sh # Takes 2-3 minutes
    cd ~/_PROJECT_-project/_PROJECT_-web-build
    
Verify that the code works:

    stack/restart.sh
    export DATA=example
    vendor/bin/docker-stack local url router 80 $DATA._PROJECT_.build._PROJECT_.com /backend/

... then build the docker images:

    vendor/neam/yii-dna-deployment/deploy/build.sh # Takes 3-4 minutes
    
6. Log in to Tutum, go to the relevant stack and redeploy the services "phpha", "phpfiles" and "web..." in the latest stack - takes 3-4 minutes.

7. Enter the tutum web shell for phpfiles and apply migrations as necessary

    export DATA=example;cd dna;vendor/bin/propel config:convert;cd ..
    bin/safe-migration-via-upload-user-data-and-reset-db.sh

8. Until cache busting is implemented, we need to login to Cloudflare, visit the domain names of the updated campaigns, choose Cache, and then Purge everything. Also, don't forget to tell every returning visitor to clear their cache...

9. When all is well, finish the hotfix branch in SourceTree, push develop and master branches and delete the remote hotfix branch.

### Build server first time setup

It is recommended to build and push the docker images on a shell server. Set one up and make sure it has access to relevant source code repositories. If you prefer, you can use your own workstation as a build server. In that case, simply open up a new terminal window locally and run the below build server commands locally instead.

Set up a new user on a server with docker installed and connect:

    ssh dokku@build._PROJECT_.com

Then, run the following in the build server:

    cd ~
    git clone -b develop git@bitbucket.org:_PROJECT_/_PROJECT_-project.git
    cd ~/_PROJECT_-project
    git clone --recursive -b develop git@bitbucket.org:_PROJECT_/_PROJECT_-web.git _PROJECT_-web
    cd ~/_PROJECT_-project/_PROJECT_-web
    
    git clone https://github.com/neam/docker-stack ~/.docker-stack
    echo 'export PATH=$PATH:~/.docker-stack/cli/docker-stack' >> ~/.bash_profile
    source ~/.bash_profile

Locally:

    scp deploy/config/deploy-prepare-secrets.php dokku@build._PROJECT_.com:/home/dokku/_PROJECT_-project/_PROJECT_-web/deploy/config/deploy-prepare-secrets.php

In build server:

    cd ~/_PROJECT_-project/_PROJECT_-web
    docker ps -q | xargs docker kill # <-- use this to make sure no other container is using port 80
    stack/start.sh
    docker-compose run -e PREFER=dist builder stack/src/install-deps.sh
    vendor/bin/docker-stack build-directory-init

Finally, add enough proper config to `~/_PROJECT_-project/_PROJECT_-product-build/.env` to be able to run the 12-factor app in the build server.  

### Troubleshooting: Checking that a newly deployed stack works - using the cli only

Copy the stack's name (in the style <date><vhost><commitsha>) and run the following:

    deploy/diagnose.sh <stackname>

This will amongst other things list something like the following:

    # Health-checks for first-level backend (Nginx):
    export WEB_PORT=49232
    export WEB_FQDN=cfa9591a-foo.node.tutum.io
    stack/_util/health-checks.sh

This gives the address to where the stack is running before it is connected to the main public domain name (we will do that later). 

To check that things work as expected (TODO - restore this):

    open http://$WEB_FQDN:$WEB_PORT/

To check that campaign manager will work as expected, add //$WEB_FQDN:$WEB_PORT to relevant Auth0-users settings and try out campaign manager features against this api endpoint.
