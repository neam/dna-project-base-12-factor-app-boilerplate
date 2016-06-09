Local Development: Code generation
====================================

More complete documentation can be found under `tools/code-generator/README.md`

## Generating item types helper class and model files

This is the metadata about item types, attributes, their labels hints, flow steps, if they are translatable etc.

The workflow:

1. (First time only) Create a new content model metadata item in dna-project-control-panel locally
2. Import item types, attributes and relations that exists in the database schema using dna-project-control-panel locally
3. Discuss content model and collaborate by editing the content model metadata using dna-project-control-panel locally
4. Perform db schema changes (adding migrations as necessary) to reflect the content model metadata
5. Generate an up to date `dna/content-model-metadata.json`
6. Generate item types helper class and model files

### Get an up to date `dna/content-model-metadata.json` from dna-project-control-panel locally.

    cd ~/Dev/Projects/code-generation-alchemists/project/dna-project-control-panel
    docker-stack local run -e DATA=_PROJECT_ phpfiles tools/code-generator/yii dna-content-model-metadata-json --configId=_ID_,7 | jq '.' > ~/Dev/Projects/_PROJECT_-project/_PROJECT_-product/dna/content-model-metadata.json

### Generate item types helper class and model files

    cd ~/Dev/Projects/_PROJECT_-project/_PROJECT_-product/
    docker-compose run -e DATA=clean-db phpfiles bin/reset-db.sh
    docker-compose run -e DATA=clean-db phpfiles bin/generate-content-model-metadata.sh

## Generate migration that syncs schema.xml with the current clean-db database

    docker-compose run -e DATA=clean-db phpfiles bin/generate-dna-propel-migrations.sh

Remember to re-generate item types helper class and model files after generating this migration.

## Generating RESTful API

Operates on item types marked as "generate_yii_rest_api_crud".

    docker-compose run -e DATA=clean-db phpfiles bin/generate-rest-api.sh
    stack/src/install-deps.sh

Now use git (SourceTree recommended) to stage the relevant generated changes and discard the changes that overwrote customly crafted parts that is not generated.

## Generating UI

#### Generating Angular Frontend UI CRUD

Operates on item types marked as "generate_yii_workflow_ui_crud".

Requires up to date content model metadata helper class and model traits.

Updating the pristine generated files:

    docker-compose run -e DATA=clean-db phpfiles bin/generate-angular-workflow-ui-crud.sh

Move generated angularjs ui files to angular frontend:

    cp -r tools/code-generator/modules/wuingcrud/crud/* ui/angular-frontend-dna/app/crud/ && rm -r tools/code-generator/modules/wuingcrud/crud/*
    
Now use git (SourceTree recommended) to stage the relevant generated changes and discard the changes that overwrote customly crafted parts that is not generated.

Updating code-generation logic is done by adding/tweaking/enhancing providers and configure what providers is used where by modifying `ui/angular-frontend-code-generation/provider-bootstrap.php`.
