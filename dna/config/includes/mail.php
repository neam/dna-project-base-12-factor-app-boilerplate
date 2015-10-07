<?php

$config['components']['email'] = array(
    'class' => 'vendor.nordsoftware.yii-emailer.components.Emailer',
    'transportType' => 'smtp',
    'smtpOptions' => array(
        'host' => SMTP_HOST,
        'username' => SMTP_USERNAME,
        'password' => SMTP_PASSWORD,
        'port' => SMTP_PORT,
        'timeout' => 2,
        'encryption' => SMTP_ENCRYPTION
    ),
);

// Use mailcatcher when running from within continuous integration
if (defined('CONFIG_ENVIRONMENT') && CONFIG_ENVIRONMENT === 'ci') {
    $config['components']['email']['smtpOptions'] = array(
        'host' => MAILCATCHER_HOST,
        'username' => null,
        'password' => null,
        'port' => MAILCATCHER_SMTP_PORT,
        'timeout' => 2,
        'encryption' => null
    );
}

// Allow but inactivate email sending and warn when no SMTP_HOST
if (SMTP_HOST === null) {
    $config['components']['email']['dryRun'] = true;
    Yii::log("All mail sending is disabled until SMTP_HOST or SMTP_URL is set through envbootstrap", CLogger::LEVEL_WARNING);
}

$config['aliases']['vendor.crisu83.yii-formatter.behaviors.FormatterBehavior'] = 'dna.vendor.crisu83.yii-formatter.behaviors.FormatterBehavior';
$config['import'][] = 'dna.vendor.nordsoftware.yii-emailer.models.EmailMessage';

// YiiMail
/*
$config['import'][] = 'ext.yii-mail.YiiMailMessage';
$config['components']['mail'] = array_merge(array(
    'class' => 'ext.yii-mail.YiiMail',
    'viewPath' => 'application.views.mail',
), $GLOBALS['env_config']['components-mail']);
*/
