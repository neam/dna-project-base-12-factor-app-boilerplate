<?php

$config['components']['db'] = array(
    'class' => 'PropelDbConnection',
    //'schemaCachingDuration'=>3600*24,
);
if (defined('TEST_DB_HOST')) {
    $config['components']['dbTest'] = array(
        'class' => 'PropelDbConnection', // TODO: Enable test db connection also in propel
        'charset' => 'utf8',
        'enableParamLogging' => true, // Log SQL parameters
        //'schemaCachingDuration'=>3600*24,
        // This allows the exportDbConfig to work without a working test database available - so that the scripts then can set it up
        'autoConnect' => false,
    );
}
