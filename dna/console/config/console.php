<?php

$approot = $applicationDirectory = realpath(
    dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..'
);
$root = $projectRoot = $applicationDirectory . DIRECTORY_SEPARATOR . '..';

$config = array(
    'basePath' => $approot . DIRECTORY_SEPARATOR . 'console',
    'aliases' => array(
        'vendor' => 'dna-vendor',
    ),
    'commandMap' => array(
        'worker' => array(
            'class' => 'dna.console.commands.WorkerCommand',
        ),
    ),
);

// Import the DNA classes and configuration
require($projectRoot . '/dna/dna-api-revisions/' . YII_DNA_REVISION . '/include.php');

unset($config['theme']);

return $config;