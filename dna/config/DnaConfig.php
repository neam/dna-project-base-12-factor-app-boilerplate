<?php

/**
 * DNA configuration class for Yii apps
 */
class DnaConfig
{

    static $dnaDirectory;

    static public function bootstrap()
    {
        // always use UTC
        date_default_timezone_set('UTC');

        // set dna directory
        static::$dnaDirectory = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..');

        // Necessary to work around some path alias issues that does not resolve anything after "dna-vendor." unless setPathOfAlias is used
        Yii::setPathOfAlias('dna-vendor', static::$dnaDirectory . DIRECTORY_SEPARATOR . 'vendor');
        Yii::setPathOfAlias('dataPath', '/files/' . DATA . '/media');
        Yii::setPathOfAlias('dataImportPath', '/files/' . DATA . '/media-import');

    }

    /**
     * Dynamic merging of configuration - to use while in development mode.
     * In production mode, 'DnaConfig::bootstrap();' should be run, followed by including an exported file of the merged configuration.
     * @param $originalConfig
     */
    static public function applyConfig(&$originalConfig)
    {

        DnaConfig::bootstrap();

        // main paths
        $projectRoot = static::$dnaDirectory . DIRECTORY_SEPARATOR . '..';

        $languages = require(static::$dnaDirectory . '/config/includes/languages.php');
        $languageDirections = require(static::$dnaDirectory . '/config/includes/languageDirections.php');

        // DNA config
        $dnaConfig = array(
            'name' => 'DNA',
            'language' => 'en', // default language, see also components.langHandler
            'theme' => null,
            'sourceLanguage' => 'en', // source code language
            'preload' => array(
                'log',
                'langHandler',
            ),
            'aliases' => array(
                'root' => $projectRoot,
                'dna' => static::$dnaDirectory,
                // i18n
                'i18n-columns' => 'dna-vendor.neam.yii-i18n-columns',
                'i18n-attribute-messages' => 'dna-vendor.neam.yii-i18n-attribute-messages',
                // qa-state
                'qa-state' => 'dna-vendor.neam.yii-qa-state',
                // relational-graph-db
                'relational-graph-db' => 'dna-vendor.neam.yii-relational-graph-db',
                // phpoffice libraries
                'phpexcel' => 'dna-vendor.phpoffice.phpexcel.Classes',
                'phpword' => 'dna-vendor.phpoffice.phpword.src',
                'phppowerpoint' => 'dna-vendor.phpoffice.phppowerpoint.Classes',
                // hacks to get some classes to load. they are used with incorrect aliases within P3Media
                'vendor.sammaye.auditrail2.behaviors.LoggableBehavior' => 'dna-vendor.sammaye.auditrail2.behaviors.LoggableBehavior',
                'vendor.yiiext.status-behavior.EStatusBehavior' => 'dna-vendor.yiiext.status-behavior.EStatusBehavior',
                'vendor.mikehaertl.translatable.Translatable' => 'dna-vendor.mikehaertl.translatable.Translatable',
                // hack for YiiPassword to work
                'YiiPassword' => 'dna-vendor.phpnode.yiipassword.src.YiiPassword',
                '\YiiPassword\Behavior' => 'dna-vendor.phpnode.yiipassword.src.YiiPassword.Behavior',
            ),
            'import' => array(
                'i18n-columns.behaviors.I18nColumnsBehavior',
                'i18n-attribute-messages.behaviors.I18nAttributeMessagesBehavior',
                'i18n-attribute-messages.components.MissingTranslationHandler',
                'dna-vendor.motin.yii-owner-behavior.OwnerBehavior',
                'qa-state.behaviors.QaStateBehavior',
                'relational-graph-db.behaviors.RelationalGraphDbBehavior',
                'dna.components.*',
                'dna.config.*',
                'dna.components.validators.*',
                'dna.behaviors.*',
                'dna.models.*',
                'dna.models.base.*',
                'dna.models.metadata.*',
                'dna.models.metadata.traits.*',
                'dna.models.unused.*',
                'dna.helpers.*',
                'dna.traits.*',
                'dna-vendor.neam.yii-i18n-tools.helpers.LanguageHelper',
            ),
            'modules' => array(
                // code generator
                'gii' => array(
                    'class' => 'system.gii.GiiModule',
                    'password' => YII_GII_PASSWORD,
                    // If removed, Gii defaults to localhost only. Edit carefully to taste.
                    'ipFilters' => array('127.0.0.1', '::1', '10.0.2.2'),
                    'generatorPaths' => array(
                        'dna-vendor.phundament.gii-template-collection', // giix generators
                        'dna-vendor.mihanentalpo.yii-sql-migration-generator',
                        'bootstrap.gii', // bootstrap generator
                    ),
                ),
            ),
            'params' => array(
                'languages' => $languages,
                'languageDirections' => $languageDirections,
            ),
        );

        // Not understood by Yii 1 import due to namespaces
        require(static::$dnaDirectory . '/components/FileTrait.php');

        // Reference the $config variable that other configuration includes expect
        $extensionsConfig = static::extensionConfig();

        // Merge already defined config on top of the DNA config (so that applications can override the dna config)
        $dnaConfig = CMap::mergeArray($dnaConfig, $originalConfig);

        // Merge DNA config on top of the extensions-defined config (so that applications and the DNA can override the extensions-defined config)
        $dnaConfig = CMap::mergeArray($extensionsConfig, $dnaConfig);

        // Set merged config as the complete config
        $originalConfig = $dnaConfig;

    }

    static public function extensionConfig()
    {

        // A $config variable that other configuration includes expect
        $config = array('modules' => array('p3media' => array()), 'components' => array());

        // Database config
        require(static::$dnaDirectory . '/db/config.inc.php');

        // Extension-specific config
        require(static::$dnaDirectory . '/vendor/neam/yii-relational-graph-db/config/yii-relational-graph-db.php');
        require(static::$dnaDirectory . '/vendor/neam/yii-workflow-core/config/yii-workflow-core.php');
        require(static::$dnaDirectory . '/vendor/neam/yii-restricted-access/config/yii-restricted-access.php');
        require(static::$dnaDirectory . '/vendor/neam/yii-workflow-task-list/config/yii-workflow-task-list.php');
        require(static::$dnaDirectory . '/config/includes/p3media.php');
        require(static::$dnaDirectory . '/config/includes/p3media-extras.php');
        require(static::$dnaDirectory . '/config/includes/p3media-presets.php');
        require(static::$dnaDirectory . '/config/includes/yii-dna-cms.php');
        //require(static::$dnaDirectory . '/config/includes/resource-manager.php');
        require(static::$dnaDirectory . '/config/includes/wrest.php');

        // Make sure dna extensions use the dna-vendor alias
        array_walk_recursive(
            $config,
            function (&$item) {
                if (!is_string($item)) {
                    return false;
                }
                $item = str_replace("vendor.", "dna-vendor.", $item);
            }
        );

        return $config;

    }

}

