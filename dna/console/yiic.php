<?php

$approot = dirname(__FILE__);
$root = dirname(__FILE__) . '/../..';

// include composer autoloaders
//require_once("$approot/vendor/autoload.php");
require_once("$root/vendor/autoload.php");
require_once("$root/dna/vendor/autoload.php");

// root-level bootstrap logic
require("$root/bootstrap.php");

// include yii
require_once("$root/vendor/yiisoft/yii/framework/yii.php");

// config file
$config = require("$approot/config/console.php");

// This will use $config and autostart a console application
require_once("$root/vendor/yiisoft/yii/framework/yiic.php");
