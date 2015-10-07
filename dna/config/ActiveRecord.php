<?php

class ActiveRecord extends CActiveRecord
{

    public function behaviors()
    {
        $behaviors = array();

        $attr = $this->getAttributes();

        // If the model has created and/or modified fields - we make sure they are used
        if (array_key_exists("created", $attr) && array_key_exists("modified", $attr)) {
            $behaviors['CTimestampBehavior'] = array(
                'class' => 'zii.behaviors.CTimestampBehavior',
                'createAttribute' => 'created',
                'updateAttribute' => 'modified',
                'timestampExpression' => 'gmdate("Y-m-d H:i:s")',
            );
        }
        if (array_key_exists("created", $attr) && !array_key_exists("modified", $attr)) {
            $behaviors['CTimestampBehavior'] = array(
                'class' => 'zii.behaviors.CTimestampBehavior',
                'createAttribute' => 'created',
                'updateAttribute' => null,
                'timestampExpression' => 'gmdate("Y-m-d H:i:s")',
            );
        }

        return array_merge(parent::behaviors(), $behaviors);
    }

    public function relations()
    {
        $relations = array();
        return array_merge(
            parent::relations(),
            $relations
        );
    }

    public function attributeLabels()
    {
        return array();
    }

    public function attributeHints()
    {
        return array();
    }

}
