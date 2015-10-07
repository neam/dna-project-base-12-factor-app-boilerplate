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

    public function getItemLabel()
    {
        return isset($this->heading) ? $this->heading : 'Group #' . $this->id;
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
            parent::relations()
        );
    }

    public function rules()
    {
        $return = array_merge(
            parent::rules(),
            array( // Ordinary validation rules
            )
        );
        //Yii::log("model->rules(): " . print_r($return, true), "trace", __METHOD__);
        return $return;
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
