<?php

// auto-loading
Yii::setPathOfAlias('MetadataGroup', dirname(__FILE__));
Yii::import('MetadataGroup.*');

class MetadataGroup extends BaseGroup
{

    use GroupTrait {
        GroupTrait::relations as trait_relations;
    }

}
