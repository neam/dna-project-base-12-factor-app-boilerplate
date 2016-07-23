Local Development: First-time set-up
====================================

# Notes about Cross-Platform Support

This has been confirmed to work in Mac OSX and modern Linux distributions, but Windows installation has so far been unsuccessful.

If you still want to try to install on Windows, here some notes:
* Put the project in your user directory: C:\Users\Username\
* If you are using Oracle VM VirtualBox and experiencing trouble - try with NDIS5 Bridged Driver. If you don't know what it is -> uninstall Oracle VM Virtual Box,
install it during Docker installation and choose to install with NDIS5 Bridged Driver instead of NDIS6.
* For step 2: to create link try mklink Target Link in CMD:

    mklink Docker_Toolbox_dir\docker-stack project_dir\vendor\bin\docker-stack

  If that does not work for you and docker-stack still complaining about not finding path -> just change relative to absolute path in docker-start file

# Instructions

Open up a terminal window and cd into the root directory of your 12-factor app's repository.

## Step 1 - Install app dependencies

    stack/src/install-deps.sh

## Step 2 - Install docker-stack cli

* Clone and symlink docker-stack to /usr/local/bin/:

    ln -sf $(pwd)/vendor/bin/docker-stack /usr/local/bin/docker-stack
    chmod +x /usr/local/bin/docker-stack

### Step 3 - Setup a local docker host

#### Docker Machine

If you are using Linux you can skip this step and go to step 4.

Install [Docker Toolbox](https://www.docker.com/toolbox/) and create a docker host to use for local development (switch out `virtualbox` for your vm software of choice):

    docker-machine create --driver virtualbox default

After each reboot, run:

    docker-machine start default

In each new terminal session, run (preferably by adding to ~/.bash_profile, ~/.bashrc or similar)

    eval "$(docker-machine env default)"

#### Boot2Docker

Do not use Boot2Docker, since it is officially deprecated since Docker v1.8.2.

## Step 4 - Initialize your local configuration file

Create your local configuration file for local non-versioned configuration directives:

    cp .env.dist .env
    cp .current-local-cli-data-profile.dist .current-local-cli-data-profile

Open it up an make sure that all sensitive directives are correctly entered.

## Step 5 - Initialize and run

Fire up your local docker stack:

    stack/start.sh

## Step 6 - Start a worker shell

In order to ensure cross-platform consistency, most commands in the Adoveo project are meant to run from a worker shell:

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

    Docker version 1.11.1, build 5604cbe

    docker-machine -v

    docker-machine version 0.7.0, build a650a40

    docker-cloud -v

    docker-cloud 1.0.4
