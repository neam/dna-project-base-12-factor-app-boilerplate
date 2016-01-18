<?php
// This is global bootstrap for autoloading 

$root = dirname(__FILE__) . '/../../..';
$dnaroot = $root . '/dna';

// require root project composer autoloader
// require_once($root . '/vendor/autoload.php');

// require project dna composer autoloader
require_once($dnaroot . '/vendor/autoload.php');
