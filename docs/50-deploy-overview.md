Deploy: Overview
====================

The project is designed to be deployed to a docker container platform (Docker Cloud) with access to a cloud database service.

Before you start, make sure that you have created a local deploy configuration file for sensitive configuration directives:


    cp deploy/config/deploy-prepare-secrets.dist.php deploy/config/deploy-prepare-secrets.php
    cp deploy/config/secrets.dist.php deploy/config/secrets.php

# Supported deployment targets

 - [Docker Cloud](52-deploy-docker-cloud.md)

