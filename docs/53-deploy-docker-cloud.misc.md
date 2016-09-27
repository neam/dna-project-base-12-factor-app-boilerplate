Deploy to Docker Cloud - Miscellaneous documentation
=============================================

### Installing docker-cloud cli

Official docs: https://docs.docker.com/docker-cloud/installing-cli/

Example: add the following to your .bash_profile or similar:

    alias docker-cloud='docker run -it -e DOCKERCLOUD_USER=$DOCKERCLOUD_USER -e DOCKERCLOUD_PASS=$DOCKERCLOUD_PASS -v $(PWD):/pwd -w="/pwd" -v ~/.docker:/root/.docker:ro --rm dockercloud/cli'

Then confirm that the docker-cloud cli works by running

    docker-cloud -v

And get the following response:

    docker-cloud 1.0.4

#### Running migrations for data profiles that depend on the /files volume

Note: Include only data profiles that depend on the /files volume, ie store local files within the stack. These data profiles first need their possibly modified /files-volumes copied from the old stack to s3, then copied from s3 to the new stack.

Open a shell in the PREVIOUS live deployment's phpfiles container (see "Running worker commands in an already deployed stack" below for instructions on how to do that). 

Specify all data profiles that were previously in production in this PREVIOUS stack but are meant to use the newly deployed stack, eg:

```
export DATA_PROFILES="ginger
laget
sodastream
texaslonghorn
life
zinfandel
crown
sas
cokecce
danskebank
sbs-discovery
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
export DATA_PROFILES="ginger
laget
sodastream
texaslonghorn
life
zinfandel
crown
sas
cokecce
danskebank
sbs-discovery
"
```

Then fetch user-generated files and push files to cdn:

    # Fetch user-generated files to the local /files directory + Sync to CDN
    for DATA in $DATA_PROFILES; do
        export DATA=$DATA
        bin/reset-user-generated-files.sh --force-s3-sync
        bin/upload-current-files-to-cdn.sh
    done

Run the following locally to echo the commands to run in the production stack: 

    # Reset db and run migrations
    for DATA in $DATA_PROFILES; do
      echo "export DATA=$DATA;"
      echo "bin/safe-migration-via-upload-user-data-and-reset-db.sh"
    done
    
Open a shell in the NEWLY DEPLOYED stack's phpfiles container.

Reset the log that keeps track:

    echo "" > dna/db/uploaded-user-data.log

Then run the commands echoed above, line by line, and ensure migrations etc are applied properly. 

### Troubleshooting: Checking that a newly deployed stack works - using the cli only (optional)

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

### Initiating the build directory

This creates a parallel directory next to the current directory, suffixed with "-build". It will be a clean directory where all components are installed from version control and as described by dependency management files. The practice to build in a clean directory is essential in order to avoid accidentally building project images that include uncommitted or otherwise locally modified files. 

    vendor/bin/docker-stack build-directory-init

After this, add enough proper config to `~/_PROJECT_-project/_PROJECT_-product-build/.env` to be able to run the 12-factor app from the build directory.

Lastly, make sure to be logged in on the docker registry so that pushing is possible:

    source vendor/neam/php-app-config/shell-export.sh
    docker login --username="$DOCKERCLOUD_USER" --password="$DOCKERCLOUD_PASSWORD"

