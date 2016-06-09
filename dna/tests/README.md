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

The below commands should be run in a tester shell:

    stack/tester-shell.sh

Ensure the existence of a clean test data profile corresponding to the current data profile:

    bin/ensure-clean-test-data-profile.sh
        
Use the test data profile for the tests:

    export DATA=test_$DATA  

Step into the path of this README:

    cd dna/tests

To run all db-agnostic tests in sequence:

    # We use two levels of bash processes in order to cope for failures when sourcing the test preparation script
    bash
    source _before-test.sh
    time ./_test.sh

It will default to `COVERAGE=full`. To override, set the COVERAGE env var before running the script, for instance:

    bash
    export COVERAGE=basic
    source _before-test.sh
    time ./_test.sh

To also run the db-dependent tests (takes 2-3min currently to set-up and tear down the db contents):

    time ./_test.sh --include-db-dependent

It is also possible to run the tests without resetting the database between each run (`_test.sh` must however first have been run without the skip-flag so that the db is set up properly):

    time ./_test.sh --include-db-dependent --skip-reset-db

Note that this is merely used in some cases during test development and is not expected to result in all tests passing.
