<?php

$approot = dirname(__FILE__);
$root = dirname(__FILE__) . '/..';

// include composer autoloaders
//require_once("$approot/vendor/autoload.php");
require_once("$root/vendor/autoload.php");
require_once("$root/dna/vendor/autoload.php");

// root-level bootstrap logic
require("$root/bootstrap.php");

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
                        'charset' => 'utf8',
                        'queries' => [
                            'utf8' => "SET NAMES utf8 COLLATE utf8_unicode_ci, COLLATION_CONNECTION = utf8_unicode_ci, COLLATION_DATABASE = utf8_unicode_ci, COLLATION_SERVER = utf8_unicode_ci"
                        ]
                    ]
                ]
            ]
        ],
        'runtime' => [
            'defaultConnection' => 'default',
            'connections' => ['default']
        ],
        'generator' => [
            'defaultConnection' => 'default',
            'connections' => ['default'],
            'objectModel' => [
                'builders' => [
                    'objectstub' => '\generators\propel\Builder\Om\ExtensionObjectBuilder',
                ],
            ],
        ],
    ]
];