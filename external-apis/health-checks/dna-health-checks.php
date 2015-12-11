<?php

$basePath = dirname(__FILE__);
$root = $basePath . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..';

// root-level bootstrap logic
require("$root/bootstrap.php");

echo "COMMITSHA: " . COMMITSHA . "\n ";
echo "DATA: " . DATA . "\n ";
