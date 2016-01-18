<?php

$approot = dirname(__FILE__);
$dnaroot = dirname(__FILE__) . '/../..';
$root = dirname(__FILE__) . '/../../..';

// bootstrap
require("$approot/bootstrap.php");

// include yii
require_once("$dnaroot/vendor/yiisoft/yii/framework/yii.php");

// config file
$config = require("$approot/config/console.php");

// This will use $config and autostart a console application
require_once("$dnaroot/vendor/yiisoft/yii/framework/yiic.php");
