#!/usr/bin/env bash

# fail on any error
set -o errexit

docker-machine start _PROJECT_
eval "$(docker-machine env _PROJECT_)"
stack/start.sh
bin/angular-frontend-develop.sh $@

exit 0
