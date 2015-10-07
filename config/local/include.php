<?php

// Makes environment variables mutable

\Dotenv::makeMutable();

// Override local services ip when running from inside docker container (identified by the simple code path "/app")

function running_inside_docker_container() {
    return substr(getcwd(), 0, 4) == "/app";
}

if (running_inside_docker_container()) {
    $_ENV["LOCAL_SERVICES_IP"] = "172.17.42.1";
} else {
    $_ENV["LOCAL_SERVICES_IP"] = getenv("LOCAL_VM_IP");
}
if (empty($_ENV["LOCAL_SERVICES_IP"])) {
    throw new Exception("LOCAL_SERVICES_IP is empty");
}

// Loads sensitive (non-versioned) environment variables from .env to getenv(), $_ENV.

\Dotenv::load($project_root);

// Expect the "paas" config as base

require(dirname(
        __FILE__
    ) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'remote' . DIRECTORY_SEPARATOR . 'include.php');

// Add local overrides

require(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'overrides.php');
