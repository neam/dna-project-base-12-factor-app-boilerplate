Deploy: Overview
====================

The project is designed to be deployed to the docker container platform Docker Cloud with access to a cloud database service (Amazon RDS).

Before you start, make sure that you have created and populated the local deploy configuration files for sensitive configuration directives:


    cp deploy/config/deploy-prepare-secrets.dist.php deploy/config/deploy-prepare-secrets.php
    cp deploy/config/secrets.dist.php deploy/config/secrets.php

## Summarized deploy routine

The repository that we deploy from is `personal-unit`. Open it up in SourceTree.
Create a new release in SourceTree, call it `YY.MM.R`, where YY is the current year, MM is the current month, and R is an incremental number for the current months' releases, for instance `15.11.2` if you are releasing the 2nd release in nov 2015.
Push the new branch. 

Open a new shell, navigate to `personal-unit` and run:

    deploy/build-push-and-generate-deployment-script.sh

Follow the instructions printed by the last command above under "To deploy to docker-cloud". The command looks similar to this:

    vendor/neam/yii-dna-deployment/deploy/to-docker-cloud.sh deployments/{YYYYMMDDHHMMSS}{branch-name-reference}{product-id}{commit-sha} 

This will start a new stack next to the ones already launched previously in the project history. 
Wait for the new stack to be fully running (this is seen in the Docker Cloud web interface in the Stacks section).
Run database migrations live as necessary.
Make the new stack take over the traffic.

Finish the  Git flow release routine in `personal-unit` so that the master branch matches what is live in production.

# Troubleshooting

A more in-depth version of the deploy routine is available for reference at:
 - [Docker Cloud - In-depth deployment routine](51-deploy-docker-cloud.in-depth-deployment-routine.md)

# Miscellaneous documentation

 - [Docker Cloud - Hotfix - Patching existing stack](52-deploy-docker-cloud.hotfix-patching-existing-stack.md)
 - [Docker Cloud - Miscellaneous documentation](53-deploy-docker-cloud.misc.md)
