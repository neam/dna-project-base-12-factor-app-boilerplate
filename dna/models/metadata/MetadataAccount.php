<?php

// auto-loading
Yii::setPathOfAlias('MetadataAccount', dirname(__FILE__));
Yii::import('MetadataAccount.*');

class MetadataAccount extends BaseAccount
{

    use AccountTrait {
        AccountTrait::behaviors as trait_behaviors;
        AccountTrait::relations as trait_relations;
        AccountTrait::rules as trait_rules;
    }

}
