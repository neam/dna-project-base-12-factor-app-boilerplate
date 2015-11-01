Local Development: Introduction
===============================

Tab-completion for all scripts related to project management: 

    bin/<tab> -> list all general project commands
    stack/<tab> -> list all stack-related project commands
    deploy/<tab> -> list all deploy-replated project commands

## The project shell

Before running any of the commands for local development expect that the first-time set-up is performed and a worker shell has been entered by running:

    stack/shell.sh

## Useful general commands outside the web container

To start a worker shell:

    stack/shell.sh

To follow the logs in the containers, run:

    stack/logs.sh

Note: There are many other shorthand wrapper scripts to many of the mostly used commands - they are found in `./bin`.
