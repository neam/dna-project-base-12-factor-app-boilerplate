<?php

// Makes environment variables mutable

\Dotenv::makeMutable();

// Loads sensitive (non-versioned) environment variables from .env to getenv(), $_ENV.

\Dotenv::load($project_root);

// Expect the "paas" config as base

require(dirname(
        __FILE__
    ) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'remote' . DIRECTORY_SEPARATOR . 'include.php');

// Add local overrides

require(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'overrides.php');
