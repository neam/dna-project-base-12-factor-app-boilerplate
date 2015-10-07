<?php

$serviceContainer = \Propel\Runtime\Propel::getServiceContainer();
$serviceContainer->checkVersion('2.0.0-dev');
$serviceContainer->setAdapterClass('default', 'mysql');
$manager = new \Propel\Runtime\Connection\ConnectionManagerSingle();
$manager->setConfiguration(array (
  'classname' => 'Propel\\Runtime\\Connection\\ConnectionWrapper',
    /**
     * Start custom part
     */
    'dsn' => 'mysql:host=' . DATABASE_HOST .
        (defined('DATABASE_PORT') ? ';port=' . DATABASE_PORT : '') .
        ';dbname=' . DATABASE_NAME,
    'user' => DATABASE_USER,
    'password' => DATABASE_PASSWORD,
    /*
     * End custom part
     */
  'attributes' =>
  array (
    'ATTR_EMULATE_PREPARES' => false,
  ),
  'settings' =>
  array (
    'charset' => 'utf8',
    'queries' =>
    array (
      'utf8' => 'SET NAMES utf8 COLLATE utf8_unicode_ci, COLLATION_CONNECTION = utf8_unicode_ci, COLLATION_DATABASE = utf8_unicode_ci, COLLATION_SERVER = utf8_unicode_ci',
    ),
  ),
));
$manager->setName('default');
$serviceContainer->setConnectionManager('default', $manager);
$serviceContainer->setDefaultDatasource('default');