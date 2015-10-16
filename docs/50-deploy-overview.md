Deploy: Overview
====================

The project is designed to be deployed to a docker container platform (Tutum) with access to a cloud database service.

Before you start, make sure that you have created a local deploy configuration file for sensitive configuration directives:


    cp deploy/config/deploy-prepare-secrets.dist.php deploy/config/deploy-prepare-secrets.php
    cp deploy/config/secrets.dist.php deploy/config/secrets.php

# Supported deployment targets

 - [Tutum](52-deploy-tutum.md)

