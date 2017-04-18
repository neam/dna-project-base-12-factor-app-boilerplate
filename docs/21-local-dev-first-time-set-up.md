Local Development: First-time set-up
====================================

# Instructions

Open up a terminal window and cd into the root directory of your 12-factor app's repository (`_PRODUCT_-product`).

## Step 1 - Install app dependencies

    stack/src/install-deps.sh

## Step 2 - Install docker-stack cli

* Clone and symlink docker-stack to /usr/local/bin/:

    ln -sf $(pwd)/vendor/bin/docker-stack /usr/local/bin/docker-stack
    chmod +x /usr/local/bin/docker-stack

### Step 3 - Setup a local docker host

#### Docker

Download and install Docker from https://www.docker.com/products/docker

You know that Docker is installed when you can open up a new terminal window, run `docker ps` and get the following response:

    CONTAINER ID        IMAGE               COMMAND             CREATED             STATUS              PORTS               NAMES

## Step 4 - Initialize your local configuration file

Create your local configuration file for local non-versioned configuration directives:

    cp .env.dist .env
    cp .current-local-cli-data-profile.dist .current-local-cli-data-profile

Open it up an make sure that all sensitive directives are correctly entered. (Get a team-mate to help out with this first time)

## Step 5 - Initialize and run

Fire up your local docker stack:

    stack/start.sh

The first time this is run, Docker will download the project's server software. Takes about 10 minutes on a decent internet connection. 

## Step 6 - Start a worker shell

In order to ensure cross-platform consistency, most commands in the project are meant to run from a worker shell:

    stack/shell.sh

## Step 7 - Initialize the database with a relevant dataset

* Follow relevant instructions in [Working with data](23-local-dev-working-with-data.md) at least down to and including "To reset to a clean database". In order to be able to work with user-generated data, follow the instructions to to and including "To ensure and reset to all databases to the currently referenced user generated data profile".
* Follow relevant instructions in [Pulling in changes from teammates](26-local-dev-pulling-in-changes-from-teammates.md)
* Now your product should be up and running locally.

## Step 8 - See your app in action

Checkout [URLs](13-overview-urls.md)

Note: To find out the ports that your stack is listening on:

    docker-compose ps

# Troubleshooting

Make sure you have at least the following versions:

    docker -v

    Docker version 17.03.1-ce-rc1, build 3476dbf

    docker-compose -v 
    
    docker-compose version 1.11.2, build dfed245
    
# Notes about Cross-Platform Support

This has been confirmed to work in Mac OSX and modern Linux distributions, but Windows support is still lacking. 

If you still want to try to install on Windows, here some notes from a developer that tried in July 2016 and reported back the following (using Docker Toolbox for Windows):
* Put the project in your user directory: C:\Users\Username\
* If you are using Oracle VM VirtualBox and experiencing trouble - try with NDIS5 Bridged Driver. If you don't know what it is -> uninstall Oracle VM Virtual Box,
install it during Docker installation and choose to install with NDIS5 Bridged Driver instead of NDIS6.
* For step 2: to create link try mklink Target Link in CMD:

    mklink Docker_Toolbox_dir\docker-stack project_dir\vendor\bin\docker-stack

  If that does not work for you and docker-stack still complaining about not finding path -> just change relative to absolute path in docker-start file
