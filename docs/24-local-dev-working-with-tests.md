Local Development: Working with tests
====================================

The project's DNA directory houses core tests. Refer to the following readmes for instructions on how to run these tests:
 
 * dna/tests/README.md
 
Currently, the remaining component's (external API:s and UI:s) lack automated tests, thus testing is made by running the components and verifying that they work. 

To ensure all data profiles are testable, you might want to ensure and reset to all databases to the currently committed user generated data:

    bin/ensure-and-reset-db-force-s3-sync-for-all-hosted-data-profiles.sh
