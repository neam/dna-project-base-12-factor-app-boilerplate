<?php

$approot = dirname(__FILE__) . '/../../dna-test-console-app';
$dnaroot = $approot . '/../..';
$root = $dnaroot . '/..';

// to mark that we are running tests
define('TESTING', true);

/*
// Necessary since we are bootstrapping the web application config and classes
$_SERVER['SCRIPT_FILENAME'] = realpath($approot.'www/index.php');
$_SERVER['DOCUMENT_URI'] = '/index-test.php';
$_SERVER['SCRIPT_NAME'] = '/index-test.php';
$_SERVER['REQUEST_URI'] = '/';
*/

// include bootstrap
require_once("$approot/bootstrap.php");

// include yii for testing
require_once($dnaroot . '/vendor/yiisoft/yii/framework/yiit.php');

// config file
$config = require("$approot/config/console.php");

// fix for fcgi (from yiic.php)
defined('STDIN') or define('STDIN', fopen('php://stdin', 'r'));

// https://github.com/Codeception/Codeception/issues/234
Yii::$enableIncludePath = false;

// initiate yii console app
$app=Yii::createConsoleApplication($config);
