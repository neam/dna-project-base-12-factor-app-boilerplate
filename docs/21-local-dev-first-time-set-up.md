Local Development: First-time set-up
====================================

Open up a terminal window and cd into the root directory of your 12-factor app's repository.

## Step 1 - Install app dependencies

    stack/src/install-deps.sh

## Step 2 - Install docker-stack cli

* Clone and symlink docker-stack to /usr/local/bin/:

    ln -sf $(pwd)/vendor/bin/docker-stack /usr/local/bin/docker-stack
    chmod +x /usr/local/bin/docker-stack

### Step 3 - Setup a local docker host

#### Docker Machine

Install [Docker Toolbox](https://www.docker.com/toolbox/) and create a docker host to use for local development (switch out `virtualbox` for your vm software of choice):

    docker-machine create --driver virtualbox _PROJECT_

After each reboot, run:

    docker-machine start _PROJECT_
    docker-machine start default # <-- run this one instead if you migrated from boot2docker

In each new terminal session, run (preferably by adding to ~/.bash_profile, ~/.bashrc or similar)

    eval "$(docker-machine env _PROJECT_)"
    eval "$(docker-machine env default)" # <-- run this one instead if you migrated from boot2docker

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

* Follow the instructions to "Reset the database" in [Working with data](23-local-dev-working-with-data.md)
* Follow the instructions in [Pulling in changes from teammates](26-local-dev-pulling-in-changes-from-teammates.md)
* Now your yii apps should be accessible on the following urls locally and you should be able to login with admin/admin.

## Step 8 - See your app in action

* Checkout [URLs](13-overview-urls.md)

Note: To find out the ports that your stack is listening on:

    docker-compose ps

# Troubleshooting

Make sure you have at least the following versions:

    docker -v

    Docker version 1.8.2, build 0a8c2e3

    docker-machine -v

    docker-machine version 0.4.1 (e2c88d6)

    tutum -v

    tutum 0.16.21

For debugging, you can echo all available linked environment variables related to running containers:

    docker-compose run info printenv