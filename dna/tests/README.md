Project DNA
===========

## Tests

### Test suites

* `unit` - Contains unit tests that verify low-level functionality.

### Test groups

#### data

We want to be able to develop/test the code both starting from an empty database, and with data imported from a production deployment. These two testing-data-scenarios are referred to as "clean-db" vs "user-generated", and all acceptance tests are grouped into one or both of these.

* `clean-db` - used for the clean-db data profile
* `user-generated` - used for all other data profiles

#### coverage

We group all tests based on how much testing coverage is required, so that builds and tests can run faster in cases when full test coverage is not essential:

* `minimal` - The minimal set of tests (default setting for automatic tests in feature branches and on develop)
* `basic` - A little more refined set of tests (default setting for automatic tests in release branches and for production)
* `full` - Includes registering of test users and the life cycle scenario tests (default setting for developers running local tests)
* `paranoid` - Includes long-running tests that were created to protect against various regressions (it is recommended for developers to run these tests locally before finishing a feature branch)

### Running tests locally

The below commands should be run in a tester shell, choose any of the below:

    stack/tester-shell.sh hhvm
    stack/tester-shell.sh php5
    stack/tester-shell.sh php7.0 #default

Ensure the existence of an up-to-date test data profile corresponding to the current data profile:

    bin/sync-test-data-profile.sh
        
Use the test data profile for the tests:

    export DATA=test_$DATA  

Step into the path of this README:

    cd dna/tests
    # We use two levels of bash processes in order to cope for failures when sourcing the test preparation script
    bash

To run all db-agnostic tests in sequence:

    . bashrc/before-test.sh
    time bin/test-db-agnostic.sh

It will default to `COVERAGE=full`. To override, set the COVERAGE env var and re-source bashrc/before-test.sh before running the script, for instance:

    export COVERAGE=basic
    . bashrc/before-test.sh
    time bin/test-db-agnostic.sh

To run the db-dependent tests (takes 1-2min currently to set-up and tear down the db contents):

    time bin/test-db-dependent.sh

It is also possible to run the tests without resetting the database between each run (`_test-db-dependent.sh` must however first have been run without the skip-flag so that the db is set up properly):

    time bin/test-db-dependent.sh --skip-reset-db

Note that this is merely used in some cases during test development and is not expected to result in all tests passing, since views and routines are not reset.
