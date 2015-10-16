# no shebang since this file is included through the source command/macro

# Environment variables used (see https://github.com/drone/drone/issues/122#issuecomment-35978045):
# DRONE_BRANCH     # the branch currently being built
# DRONE_COMMIT     # the commit sha currently being built
# DRONE_PR         # the pull request number being built
# DRONE_BUILD_DIR  # the location of your code and working directory

# not used but kept in case we want to use it shortly
# DRONE_COMMIT_SHORT=`git rev-parse --verify --short=7 $DRONE_COMMIT`

export CI=1
export USER_GENERATED_DATA_S3_BUCKET="s3://_PROJECT_-product-user-data-backups"
export PUBLIC_FILES_S3_BUCKET="s3://static._PROJECT_.com"
export PUBLIC_FILES_S3_HOST="static._PROJECT_.com"
export WEB_CONFIG_ENVIRONMENT=production
export BRAND_HOME_URL=www._PROJECT_.com

# Use the development tutum deployment for all deployments except for demo, release and master deployments
if [ "$GRANULARITY" == "project-branch-commit-specific" ] || ([[ "$DRONE_BRANCH" != release* ]] && [[ "$DRONE_BRANCH" != hotfix* ]] && [[ "$DRONE_BRANCH" != live* ]] && [ "$DRONE_BRANCH" != "master" ]); then
    export DEPLOY_STABILITY_TAG=dev
    export TOPLEVEL_DOMAIN=_PROJECT_dev.com
    export DEFAULT_COVERAGE=minimal
    export VIRTUAL_HOST_DATA_MAP="%DATA%._PROJECT_dev.com@%DATA%,%DATA%.player._PROJECT_dev.com@%DATA%"
    export MULTI_TENANT_VIRTUAL_HOST='*._PROJECT_dev.com, *.product._PROJECT_dev.com'
else
    # Use demo tutum deployment for demo deployments, otherwise use production tutum
    if [[ "$DRONE_BRANCH" == demo* ]]; then
        export DEPLOY_STABILITY_TAG=demo
        export TOPLEVEL_DOMAIN=_PROJECT_demo.com
        export DEFAULT_COVERAGE=basic
        export VIRTUAL_HOST_DATA_MAP="%DATA%._PROJECT_demo.com@%DATA%,%DATA%.player._PROJECT_demo.com@%DATA%"
        export MULTI_TENANT_VIRTUAL_HOST='*._PROJECT_demo.com, *.product._PROJECT_demo.com'
    else
        export DEPLOY_STABILITY_TAG=prod
        export TOPLEVEL_DOMAIN=_PROJECT_.com
        export DEFAULT_COVERAGE=basic
        export VIRTUAL_HOST_DATA_MAP=foo
        export VIRTUAL_HOST_DATA_MAP="%DATA%._PROJECT_.com@%DATA%,%DATA%.player._PROJECT_.com@%DATA%,%DATA%.ratataa.se@%DATA%,sas.ratataa.se@sas,cokecce._PROJECT_.com@cokecce,bigbrother.ratataa.se@sbs-discovery"
        export MULTI_TENANT_VIRTUAL_HOST='*._PROJECT_.com, *.product._PROJECT_.com'
    fi
fi

# Docker images are managed by these user accounts
export TUTUM_USER=_PROJECT_
export DOCKER_REGISTRY_USER=_PROJECT_
export REPO=_PROJECT_-web-src

# Use the development ga tracking id for all deployments except production deployments
if [ "$DRONE_BRANCH" != master ]; then
    export GA_TRACKING_ID=$DEVELOPMENT_GA_TRACKING_ID
else
    export GA_TRACKING_ID=$PRODUCTION_GA_TRACKING_ID
fi

# default CI_SCOPE to "all" (other valid value: "frontends", which only includes consume-related parts)
#if [ "$CI_SCOPE" == "" ] || [ "$CI_SCOPE" == "{{CI_SCOPE}}" ]; then
    CI_SCOPE="all"
#fi

# double-check git version
#git --version

# vhostappname function
function vhostbranchname {

    local STR=$1

    # maximum length of the DRONE_BRANCH+projectref part of the APPNAME seems to be 45 chars, thus for "vizabi" a max of 38 chars would be ok. restricting to 35 chars to be on the safe side
    #if [ "$CI_SCOPE" == "frontends" ]; then
    #    STR=${STR:0:25} # shorter since the appname is prefixed with "frontends-"
    #else
        STR=${STR:0:35}
    #fi

    echo "$STR"

}

# set default BRANCH value
export BRANCH=$(vhostbranchname "$DRONE_BRANCH")

# vhostappname function
function vhostappname {

    local STR=$1

    # removes some invalid characters that will not work with dokku vhost set-up (only [a-z0-9-_.] are allowed)
    STR=${STR//\//_}
    #STR=${STR//./_}
    STR="$(echo $STR | tr '[:upper:]' '[:lower:]')" # UPPERCASE to lowercase

    echo "$STR"

}

# unique identifier of this build
cd $DRONE_BUILD_DIR
    DRONE_COMMIT=`git rev-parse --verify --short=7 HEAD`
    export CI_BUILD_ID="drone/$DRONE_COMMIT"

cd $DRONE_BUILD_DIR

    export COMMIT_MESSAGE=`git log -1 --pretty=%B $DRONE_COMMIT`

    # test if this build should be tested (warning: this causes deployments to be updated without being tested - use with caution!)
    if [[ $COMMIT_MESSAGE == *"[deploy without testing]"* ]]; then
        export DEPLOY_WITHOUT_TESTING=1
    fi
    if [[ "$DEPLOY_WITHOUT_TESTING" == "1" ]]; then
        export TESTED=""
    else
        export TESTED="tested "
    fi

    # test if this build should be skipped altogether
    if [[ $COMMIT_MESSAGE == *"[skip ci]"* ]]; then
        export SKIP_CI=1
    fi

    # check how well this build should be tested (COVERAGE can be minimal, basic, full or paranoid)
    export COVERAGE=$DEFAULT_COVERAGE
    if [[ $COMMIT_MESSAGE == *"[minimal test coverage]"* ]]; then
        export COVERAGE=minimal
    fi
    if [[ $COMMIT_MESSAGE == *"[basic test coverage]"* ]]; then
        export COVERAGE=basic
    fi
    if [[ $COMMIT_MESSAGE == *"[full test coverage]"* ]]; then
        export COVERAGE=full
    fi
    if [[ $COMMIT_MESSAGE == *"[paranoid test coverage]"* ]]; then
        export COVERAGE=paranoid
    fi

    # due to constant resource shortages on dokku host, commit-specific deployments are only enabled when commit message specifies it
    if [[ $COMMIT_MESSAGE == *"[commit-specific]"* ]]; then
        export COMMIT_SPECIFIC_DEPLOYMENTS="1"
    else
        export COMMIT_SPECIFIC_DEPLOYMENTS="0"
    fi

# export variables for each component that deploys separately

cd $DRONE_BUILD_DIR/_PROJECT_-product

    #if WEB_BRANCH=$(git symbolic-ref --short -q HEAD)
    #then
    #    export WEB_BRANCH=$(vhostbranchname "$WEB_BRANCH")
    #else
        export WEB_BRANCH=$BRANCH
    #fi

    if [ "$GRANULARITY" == "project-branch-commit-specific" ]; then
        PROJECT_COMMIT_SHORT=`git rev-parse --verify --short=7 HEAD`
        APPNAME=${PROJECT_COMMIT_SHORT}-${WEB_BRANCH}
    else
        APPNAME=${WEB_BRANCH}
    fi
    APPNAME=${APPNAME}-product-$DATA
    export APPNAME=$(vhostappname "$APPNAME")
    #echo APPNAME=$APPNAME

    # default setting for separate (single-tenant) deployments
    export APPVHOST=$APPNAME.$TOPLEVEL_DOMAIN
    export VIRTUAL_HOST=$APPVHOST
    export VIRTUAL_HOST_WEIGHT=100

    # setting for catch-all deployment
    # for tenants that are not requiring an individual SLA / deployment
    if [ "$DATA" == "%DATA%" ]; then
      export APPVHOST="%DATA%."$DEPLOY_STABILITY_TAG
      export VIRTUAL_HOST="$MULTI_TENANT_VIRTUAL_HOST"
      export VIRTUAL_HOST_WEIGHT=50
    fi

    # branded deployments
    if [[ "$APPNAME" == master* ]] || [[ "$APPNAME" == release_* ]] || [[ "$APPNAME" == live_* ]]; then
        # separate deployment since it was already deployed. any update and we can switch to catch-all instead
        if [ "$DATA" == "foo-client" ]; then
          export APPVHOST="foo.whitelabelclient.com"
          export TOPLEVEL_DOMAIN=whitelabelclient.com
        fi
    fi

cd $DRONE_BUILD_DIR

export WEB_BASE_URL="$APPVHOST"
export WEB_API_BASE_URL="$APPVHOST/api/"
