<?php
/**
 * Overrides the main application config - used when running the code locally
 */

namespace neam\bootstrap;

// Don't require VIRTUAL_HOST* as long as it is hard-coded into the local docker-compose.yml file

Config::expect("VIRTUAL_HOST", $default = "local-virtual-host", $required = false);
Config::expect("VIRTUAL_HOST_WEIGHT", $default = "999", $required = false);

// Don't require config for sentry error reporting nor google analytics tracking when running locally

Config::expect("SENTRY_DSN", $default = null, $required = false);

// Don't require anything else than mailcatcher for local dev

Config::expect("SMTP_USERNAME", $default = null, $required = false);
Config::expect("SMTP_PASSWORD", $default = null, $required = false);
Config::expect("SMTP_ENCRYPTION", $default = null, $required = false);

// Test database configuration is only used when running locally

Config::expect("TEST_DB_SCHEME", $default = null, $required = false);
Config::expect("TEST_DB_HOST", $default = null, $required = false);
Config::expect("TEST_DB_PORT", $default = null, $required = false);
Config::expect("TEST_DB_PASSWORD", $default = null, $required = false);

// Don't require CDN_PATH-config - locally we serve it from the same host

Config::expect("CDN_PATH_HTTP", $default = null, $required = false);
Config::expect("CDN_PATH_HTTPS", $default = null, $required = false);

// To be able to choose password for code-generator (only available locally)

Config::expect("YII_CODE_GENERATION_ADMIN_PASSWORD", $default = "foo", $required = false);

// To be able to connect to our virtual machine when not inside it, we need to know the address

Config::expect("LOCAL_VM_IP", $default = null, $required = !running_inside_docker_container());
