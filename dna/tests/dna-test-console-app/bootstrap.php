<?php

// include composer autoloaders
require_once("$root/vendor/autoload.php");

// Dna composer autoloader required for all requests nowadays
require_once("$root/dna/vendor/autoload.php");

// Make app config available as PHP constants
require("$root/vendor/neam/php-app-config/include.php");

// Prevent error notices in FileTrait - TODO: Get rid of CDN_PATH usage
define('CDN_PATH', 'http://localhost:9000/todo-get-rid-of-cdn-path-usage/');

// Include propel orm config
require "$root/dna/generated-conf/config.php";
