<?php
/**
 * @var $config
 */

// Apply DNA config in order to import the current DNA classes and configuration
require_once($projectRoot . '/dna/config/DnaConfig.php');
DnaConfig::applyConfig($config);

// Add backwards compatibility in case this is a published revision that is not the same as the current DNA revision
require(dirname(__FILE__) . '/backwards-compat.php');
