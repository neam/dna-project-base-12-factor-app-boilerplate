<?php

if (defined('DNA_BOOTSTRAP')) {
    throw new Exception('DNA Bootstrap include should only be run once');
}

$root = dirname(__FILE__);

// HHVM SCRIPT_NAME difference vs php-fpm workaround
if (defined('HHVM_VERSION') && isset($_SERVER['NGINX_SCRIPT_NAME'])) {
    $_SERVER['DOCUMENT_ROOT'] = $_SERVER['NGINX_DOCUMENT_ROOT'];
    $_SERVER['SCRIPT_NAME'] = $_SERVER['NGINX_SCRIPT_NAME'];
    $_SERVER['PHP_SELF'] = $_SERVER['NGINX_SCRIPT_NAME'];
}

// Dna composer autoloader required for all requests nowadays
require_once("$root/dna/vendor/autoload.php");

// Make app config available as PHP constants
require("$root/vendor/neam/php-app-config/include.php");

// General error reporting level
error_reporting(E_ALL);

// Quiet error output when not in debug mode
if (DEV) {
    if (!defined('YII_DEBUG')) define('YII_DEBUG', true);
    if (!defined('YII_TRACE_LEVEL')) define('YII_TRACE_LEVEL', 3);
    //ini_set("display_errors", true);
} else {
    if (!defined('YII_DEBUG')) define('YII_DEBUG', false);
    //ini_set("display_errors", false);
}

// Include propel orm config
require "$root/dna/generated-conf/config.php";

// Define app as bootstrapped
define('DNA_BOOTSTRAP', true);
