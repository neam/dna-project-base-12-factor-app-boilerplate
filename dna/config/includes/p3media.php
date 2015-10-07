<?php

$config['modules']['p3media'] = array(
    'class' => 'vendor.phundament.p3media.P3MediaModule',
    'dataAlias' => 'dataPath',
    'importAlias' => 'dataImportPath',
    'params' => array(
        'publicRuntimePath' => '../www/runtime/p3media',
        'publicRuntimeUrl' => '/runtime/p3media',
        'protectedRuntimePath' => 'runtime/p3media',
        'presets' => array(), // set in includes/p3media-presets.php
    ),
);