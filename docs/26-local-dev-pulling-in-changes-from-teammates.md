Local Development: Pulling in changes from teammates
=====================================================

Pull the latest changes recursively (if you did this in SourceTree already then you can skip this step):

    stack/src/git-pull-recursive.sh

After pulling the latest changes or restoring to a previous commit, compare your `.env` against `.env.dist` and merge in relevant new configuration options/changes from the latter.

Then, run the following to update your local environment's dependencies:

    stack/src/install-deps.sh

If updates to docker-compose.yml has been made since you last started the docker stack:

    stack/start.sh

If updates to the data profiles have been made and you have no unsaved data locally:

    stack/shell.sh
    export DATA=example
    bin/reset-db.sh --force-s3-sync
