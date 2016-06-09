<?php

$approot = dirname(__FILE__);
$root = dirname(__FILE__) . '/..';

// include composer autoloaders
//require_once("$approot/vendor/autoload.php");
require_once("$root/vendor/autoload.php");
require_once("$root/dna/vendor/autoload.php");

// root-level bootstrap logic
require_once("$root/bootstrap.php");

// propel config
return [
    'propel' => [
        'database' => [
            'connections' => [
                'default' => [
                    'adapter' => 'mysql',
                    'classname' => 'Propel\Runtime\Connection\ConnectionWrapper',
                    'dsn' => 'mysql:host=' . DATABASE_HOST . (defined(
                            'DATABASE_PORT'
                        ) ? ';port=' . DATABASE_PORT : '') . ';dbname=' . DATABASE_NAME,
                    'user' => DATABASE_USER,
                    'password' => DATABASE_PASSWORD,
                    'attributes' => [],
                    'settings' => [
                        'charset' => 'utf8mb4',
                        'queries' => [
                            'utf8mb4' => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci, COLLATION_CONNECTION = utf8mb4_unicode_ci, COLLATION_DATABASE = utf8mb4_unicode_ci, COLLATION_SERVER = utf8mb4_unicode_ci"
                        ]
                    ]
                ]
            ]
        ],
        'runtime' => [
            'defaultConnection' => 'default',
            'connections' => ['default']
        ],
        'migrations' => [
            'parserClass' => '\\generators\\propel\\Reverse\\DnaProjectBaseMysqlSchemaContentModelMetadataDecorator', // Extending \Propel\Generator\Reverse\MysqlSchemaParser
        ],
        'generator' => [
            'defaultConnection' => 'default',
            'connections' => ['default'],
            'objectModel' => [
                'builders' => [
                    'objectstub' => '\\generators\\propel\\Builder\\Om\\ExtensionObjectBuilder', // Extending \Propel\Generator\Builder\Om\ExtensionObjectBuilder
                ],
            ],
        ],
    ]
];