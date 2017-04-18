Managing Deployments
====================

## Stacks

### Running worker commands in an already deployed stack

#### Docker Cloud browser shell

1. Log into Docker Cloud
2. Stacks
3. Choose the relevant stack
4. Click on the "phpfiles" services
5. Click the `>_` button on the phpfiles-container's row
6. Wait for the browser shell to start
7. Write "bash" and press ENTER

Note that copying and pasting via the browser shell must be done by right-clicking and choosing Paste/Copy in the context menu.
Also note that output copied from the browser shell will include extra newlines for every linebreak visible in the output.

#### Cli

If you don't have the corresponding stack metadata (in the deployments/ directory), get it from the commonly shared SFTP server where the metadata about the deployments is stored:

    scp -r _PROJECT_@build.neamlabs.com:/home/_PROJECT_/_PROJECT_-project/_PROJECT_-product/deployments/* deployments/

Then, run:

    export DATA=example
    source deploy/prepare.sh
    deploy/diagnose.sh <path-to-deployment>
    
For instance:

    export DATA=example
    source deploy/prepare.sh
    deploy/diagnose.sh deployments/20150505174239foobarfoobarfoobar3ae0903/
    
In the output from the diagnose.sh script you will have a command similar to:

    docker-cloud exec <container-id> /bin/bash # (phpfiles-1)

Run it in order to open a shell in the deployed phpfiles container. Cd into the app directory:

    cd /app

#### Relevant commands

Examples of relevant commands:

    export DATA=example
    bin/upload-current-user-data.sh # Create a backup of the current files
    
And even: (but be careful - possibly live files and database! only do this on new deployments to install a database that was locally curated before)

    bin/reset-user-generated-files.sh --force-s3-sync
    bin/reset-db.sh --force-s3-sync
