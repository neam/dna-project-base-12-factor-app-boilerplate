Deploy: Overview
====================

The project is designed to be deployed to the docker container platform Docker Cloud with access to a cloud database service (Amazon RDS).

Before you start, make sure that you have created and populated the local deploy configuration files for sensitive configuration directives:


    cp deploy/config/deploy-prepare-secrets.dist.php deploy/config/deploy-prepare-secrets.php
    cp deploy/config/secrets.dist.php deploy/config/secrets.php

# Supported deployment strategies

 - [Docker Cloud - Via new stack](51-deploy-docker-cloud.via-new-stack.md)
 - [Docker Cloud - Hotfix - Patching existing stack](52-deploy-docker-cloud.hotfix-patching-existing-stack.md)

# Miscellaneous documentation

 - [Docker Cloud - Miscellaneous documentation](53-deploy-docker-cloud.misc.md)
