Docker-based Stack - DNA Project Base (Based on Debian PHP/Nginx)
===================================

## Features

* Follows Docker's one-process-per-container philosophy by having separate containers for Nginx and PHP-FPM
* Uses a Debian-derived PHP image and the official Nginx Docker image
* Includes boilerplate configuration with Docker-specific enhancements
* Includes both a PHP "ha" service which is not supposed to use any data volumes and thus can be scaled elastically, as well as a PHP "files" container which uses a /files volume to stored user-uploaded files 
* The PHP "ha" service is frontend by a HAProxy service that distributes the load to the PHP "ha" service containers
* The PHP service can use either PHP-FPM with Opcache (default) or HHVM
* Compatible with deployment routines explained in [https://github.com/neam/yii-dna-deployment](https://github.com/neam/yii-dna-deployment)

## Default configuration

A frontend nginx location on / is configured to use the "ha" PHP service. A backend nginx location on /backend/ is configured to use the "files" PHP service. 

The frontend can be scaled horizontally and is thus served by different containers on each request, while the backend is always served by the same container. 

A Redis service is included in the docker stack and configured as the PHP session handler for all containers.

## Installation

Clone/download a copy of this repository and copy the boilerplate files to your 12-factor app base dir.

    cd my-app
    docker-stack install debian-php-nginx
    docker-stack install debian-php-nginx.dna-project-base

## Usage

To try this stack out-of-the-box after installing it, create the index php files expected by the default configuration:

    mkdir -p frontend/www
    mkdir -p backend/www
    echo '<?php phpinfo();' > frontend/www/index.php
    echo '<?php phpinfo();' > backend/www/index.php

Also, you need the local files directory and the DATA environment variable to indicate where local files are stored:

    mkdir -p .files/foo/media
    echo 'DATA=foo' >> .env

Fire up the stack locally:

    docker-compose up -d

Visit the below returned urls in your browser:

    docker-stack local url
    docker-stack local url /backend/

> Hint: On OSX, you can open the url directly from a terminal session:
>
>    stack/open-browser.sh
>    stack/open-browser.sh /backend/

To scale the PHP "ha" service:

    docker-compose scale phpha=3

## Deployment

For thorough instructions and helpers scripts:
 
    composer install neam/yii-dna-deployment

Then, follow instructions under `vendor/neam/yii-dna-deployment/README.md`

## Local Extras

The local stack include optional containers that emulate upstream services outside of your control. Simply comment them out if you do not need them.

### Explanation of the extra local containers

* `mailcatcher` - Runs a mailcatcher SMTP server
* `localdb` - Runs a local MySQL server

### Corresponding service in production

* `mailcatcher` - An SMTP service such as Gmail, Amazon Simple Mail Service, Foo etc
* `localdb` - A cloud database service such as Amazon RDS, Rackspace CloudDB etc

### Using memcache

TODO: Add instructions

### Using localdb

In order to connect to your local database from your work station, use the following credentials:

    User: admin
    Password: local-mysql-pass

Connect to the host and port given by:

    docker-stack local db

## Customization

### Configuration

Optionally, generate your project php/nginx base configuration based on composer.json metadata: [Follow these instructions](../../generators/server-config-generator/README.md)

Customize your php/nginx configuration by changing the files in your project's `stack/nginx/` and `stack/php/` directories.

To use PHP-FPM instead of HHVM, comment the "hhvm"-related lines almost at the bottom of your project's `stack/php/run.sh`, and uncomment the "php-fpm" line.

Also, because HHVM seems to alter the SCRIPT_NAME and DOCUMENT_ROOT params from nginx, add the following to your index.php file:

    // HHVM SCRIPT_NAME difference vs php-fpm workaround
    if (defined('HHVM_VERSION')) {
        $_SERVER['DOCUMENT_ROOT'] = $_SERVER['NGINX_DOCUMENT_ROOT'];
        $_SERVER['SCRIPT_NAME'] = $_SERVER['NGINX_SCRIPT_NAME'];
        $_SERVER['PHP_SELF'] = $_SERVER['NGINX_SCRIPT_NAME'];
    }

And the following to your location block config in nginx:

    # for hhvm
    fastcgi_keep_conn on;
    fastcgi_param NGINX_SCRIPT_NAME $fastcgi_script_name;
    fastcgi_param NGINX_DOCUMENT_ROOT $document_root;