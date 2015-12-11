<?php

// Static messages
$config['components']['messages'] = array(
    'class' => 'CPhpMessageSource',
);
// Yii core static messages
$config['components']['coreMessages'] = array(
    'basePath' => null,
    'forceTranslation' => true,
    // This is necessary to be able to override messages in the default language (en_us currently)
);
// Db messages - component 1 - used for output in views
$config['components']['displayedMessages'] = array(
    'class' => 'DbMessageSource',
    'missingTranslationAction' => 'langFallback',
);
// Db messages - component 2 - used for input forms through virtual attributes
$config['components']['editedMessages'] = array(
    'class' => 'DbMessageSource',
    'missingTranslationAction' => null,
);
