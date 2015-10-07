<?php

$config['import'][] = 'vendor.phundament.p3media.models.*';
$config['import'][] = 'vendor.bwoester.yii-static-events-component.*';
$config['import'][] = 'vendor.bwoester.yii-event-interceptor.*';
$config['import'][] = 'vendor.sammaye.auditrail2.models.AuditTrail';

$config['aliases']['jquery-file-upload'] = 'vendor.phundament.jquery-file-upload';
$config['aliases']['jquery-file-upload-widget'] = 'vendor.phundament.p3extensions.widgets.jquery-file-upload';
$config['aliases']['jquery-file-upload-widget.EFileUpload'] = 'vendor.phundament.p3extensions.widgets.jquery-file-upload.EFileUpload';

// attach EventBridgeBehavior to application, so we can attach to
// application events on a per class base.
$config['behaviors']['eventBridge'] = array(
    'class'  => 'EventBridgeBehavior',
);

$config['components']['events'] = array(
    'class'  => 'EventRegistry',
    'attach' => array(
        // eg. set default access fields in models with event-bridge behavior
        /*
        'P3Widget' => array(
            'onAfterConstruct' => array(
                function( $event ) {
                    //$event->sender->access_delete = 'Editor';
                },
            ),
        ),
        'P3Page' => array(
            'onAfterConstruct' => array(
                function( $event ) {

                },
            ),
        ),
        */
        'P3Media' => array(
            'onAfterConstruct' => array(
                function( $event ) {

                },
            ),
        ),
    ),
);
