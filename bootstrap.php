<?php

$root = dirname(__FILE__);

// make app config available as PHP constants

require("$root/vendor/neam/php-app-config/include.php");

// General error reporting level
error_reporting(E_ALL);

// Quiet error output when not in debug mode
if (DEV) {
    if (!defined('YII_DEBUG')) define('YII_DEBUG', true);
    if (!defined('YII_TRACE_LEVEL')) define('YII_TRACE_LEVEL', 3);
    ini_set("display_errors", true);
} else {
    if (!defined('YII_DEBUG')) define('YII_DEBUG', false);
    ini_set("display_errors", false);
}

// Include propel orm config
require "$root/dna/generated-conf/config.php";
