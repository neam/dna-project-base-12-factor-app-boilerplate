<?php

$applicationDirectory = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);
$projectRoot = $applicationDirectory . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..';

$consoleConfig = array(
    'aliases' => array(
        'root' => $projectRoot,
        'app' => $applicationDirectory,
        'vendor' => $projectRoot . DIRECTORY_SEPARATOR . 'vendor',
        'dna' => $projectRoot . DIRECTORY_SEPARATOR . 'dna',
    ),
    'basePath' => $applicationDirectory,
    'name' => 'Project DNA Test Console App (minimal app using dna config which\'s config is used for unit test bootstrapping)',
    'import' => array(),
    'commandMap' => array(),
    'components' => array(),
);

$config = array();

// Import the DNA classes and configuration into $config
require($projectRoot . '/dna/dna-api-revisions/' . YII_DNA_REVISION . '/include.php');

// create base console config from web configuration
$consoleRelevantDnaConfig = array(
    'name' => $config['name'],
    'language' => $config['language'],
    'aliases' => $config['aliases'],
    'import' => $config['import'],
    'components' => $config['components'],
    'modules' => $config['modules'],
    'params' => $config['params'],
);

// apply console config
$consoleConfig = CMap::mergeArray($consoleRelevantDnaConfig, $consoleConfig);

return $consoleConfig;
