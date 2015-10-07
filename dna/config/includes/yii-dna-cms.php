<?php

$languages = require(static::$dnaDirectory . '/config/includes/languages.php');

$config['components']['urlManager'] = array(
    'class' => 'vendor.phundament.p3extensions.components.P3LangUrlManager',
    'urlFormat' => 'path',
    'showScriptName' => false,
    'appendParams' => false, // in general more error resistant according to Tobias Munk
);
$config['components']['langHandler'] = array(
    'class' => 'vendor.phundament.p3extensions.components.P3LangHandler',
    'languages' => array_keys($languages), // available languages, eg. 'lv', 'ru', 'fr'
);
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
/*
$config['components']['messages'] = array(
    'class' => 'P3PhpMessageSource',
    'mappings' => array(
        'en_us' => 'en',
        'es_es' => 'es',
        'fa_ir' => 'fa',
        'hi_in' => 'hi',
        'pt_pt' => 'pt',
        'sv_se' => 'sv',
    )
);
*/
$config['components']['authManager'] = array(
    'class' => 'vendor.codemix.hybridauthmanager.HybridAuthManager',
    'authFile' => Yii::getPathOfAlias('root') . '/app/data/auth.php',
    'defaultRoles' => array('Anonymous', 'Member'),
);
$config['components']['user'] = array(
    'class' => 'application.components.WebUser',
    'loginUrl' => array('/account/authenticate/login'),
    'behaviors' => array(
        'vendor.neam.yii-restricted-access.behaviors.RestrictedAccessWebUserBehavior',
    ),
);

class DataModel
{

    /**
     * The corresponding qa state models used by yii-qa-state
     * @return array
     */
    static public function qaStateModels()
    {

        $qaStateModels = array();
        foreach (ItemTypes::where('is_preparable') as $model => $table) {
            $qaStateModels[$model . "QaState"] = $table . "_qa_state";
        }
        return $qaStateModels;

    }

    /**
     * @return array List of model attributes and relations to track translation progress for using recursive validator logic
     */
    static public function i18nRecursivelyValidated()
    {
        return array(
            'attributes' => array(
                'Foo' => array('bar_contents' => 'validateBarContentsTranslation'),
            ),
            'relations' => array(
                'Zoo' => array(
                    'baz' => 'validateRelatedTranslation',
                ),
            ),
        );
    }

}

class MetaData
{

    static public function qaStateCoreScenarios()
    {
        return array(
            'draft',
            'reviewable',
            'publishable',
        );

    }

}
