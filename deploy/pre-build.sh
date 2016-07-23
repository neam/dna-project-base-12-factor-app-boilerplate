time vendor/bin/docker-stack build-directory-sync
cd ../$(basename $(pwd))-build/
time docker-compose run -e PREFER=dist builder stack/src/install-core-deps.sh
