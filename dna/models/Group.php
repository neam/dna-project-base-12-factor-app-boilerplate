<?php

// auto-loading
Yii::setPathOfAlias('Group', dirname(__FILE__));
Yii::import('Group.*');

class Group extends MetadataGroup
{

    // System groups
    const SYSTEM = 'System';

    // Add your model-specific methods here. This file will not be overriden by gtc except you force it.
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function init()
    {
        return parent::init();
    }

    public function relations()
    {
        return array_merge(
            array(
                'memberCount' => array(
                    self::STAT,
                    'GroupHasAccount',
                    'group_id',
                    'select' => 'COUNT( DISTINCT t.account_id )', // In case account has many roles in group
                ),
            ),
            $this->trait_relations()
        );
    }

    public function search($criteria = null)
    {
        if (is_null($criteria)) {
            $criteria = new CDbCriteria;
        }
        return new CActiveDataProvider(
            get_class($this), array(
                'criteria' => $this->searchCriteria($criteria),
            )
        );
    }

}
