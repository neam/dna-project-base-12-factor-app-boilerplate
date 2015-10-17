Local Development: URLs
====================================

First, set the DATA env var to specify which url you want to open:


    export DATA=example

If you do not set anything, the clean-db DATA profile will be used, meaning that there will be no campaigns in the database.

Healthchecks:


    stack/open-browser.sh /
    stack/open-browser.sh /dna-health-checks.php

REST API:


    stack/open-browser.sh /api/
