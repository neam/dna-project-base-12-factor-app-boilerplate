#!/usr/bin/env bash

docker-machine start _PROJECT_
eval "$(docker-machine env _PROJECT_)"
stack/start.sh
bin/angular-frontend-develop.sh $@

exit 0
