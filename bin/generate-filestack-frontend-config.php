<?php

$basePath = dirname(__FILE__);
$root = $basePath . DIRECTORY_SEPARATOR . '..';

// include composer autoloaders
require_once("$root/vendor/autoload.php");
require_once("$root/dna/vendor/autoload.php");

// root-level bootstrap logic
require("$root/bootstrap.php");

class File
{
    use \neam\file_registry\FileTrait;
}

//print "== php ==\n";
//print FILESTACK_API_SECRET . "\n";
//print "\n";
//print 'creator' . "\n";
$policy = File::filestackCreatorPolicy();
print "\n";
print 'export FILESTACK_API_KEY="' . FILESTACK_API_KEY . '"' . "\n";
print 'export FILESTACK_CREATOR_POLICY="' . $policy . '"' . ' # ' . File::filestackCreatorJsonPolicy() . "\n";
print 'export FILESTACK_CREATOR_SIGNATURE="' . File::filestackSignature($policy) . '"' . "\n";
print "\n";
