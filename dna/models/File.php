<?php

// auto-loading
Yii::setPathOfAlias('File', dirname(__FILE__));
Yii::import('File.*');

class File extends MetadataFile
{

    use \neam\file_registry\FileTrait;

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
        return $this->defaultItemLabel("path");
    }

    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            array()
        );
    }

    public function rules()
    {
        return array_merge(
            parent::rules()
        /* , array(
          array('column1, column2', 'rule1'),
          array('column3', 'rule2'),
          ) */
        );
    }

    public function search($criteria = null)
    {
        if (is_null($criteria)) {
            $criteria = new CDbCriteria;
        }
        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $this->searchCriteria($criteria),
        ));
    }

    /**
     * Helper method. Returns file constraints config for images, used by for instance File::forItemRelationSelect()
     * @return P3Media[]
     */
    static public function imageFileConstraints()
    {
        return [
            'mimeTypes' => [
                'image/jpeg',
                'image/png',
            ],
            'fileExtensions' => [],
        ];
    }

    /**
     * Helper method. Returns a "no" file constraints config, used by for instance File::forItemRelationSelect()
     * @return P3Media[]
     */
    static public function noFileConstraints()
    {
        return [
            'mimeTypes' => [],
            'fileExtensions' => [],
        ];
    }

    /**
     * Returns related P3Media options.
     * @param P3Media[] $models
     * @return array
     */
    static public function listData($models, $valueField = 'id', $textField = 'original_name', $groupField = '')
    {
        return TbHtml::listData(
            $models,
            $valueField,
            $textField,
            $groupField
        );
    }

    public function forItemRelationSelect($modelClass, $relationName, $ownedOnly = false)
    {

        $constraints = $modelClass::model()->getFileConstraints()[$relationName];

        $criteria = new CDbCriteria();

        if (!empty($constraints['mimeTypes'])) {
            $criteria->addInCondition('t.mime_type', $constraints['mimeTypes']);
        }

        if (!empty($constraints['fileExtensions'])) {
            $fileExtensions = '\.(' . implode('|', $constraints['fileExtensions']) . ')';
            $criteria->addCondition("t.original_name REGEXP '$fileExtensions$'");
        }

        $criteria->limit = 100;
        $criteria->order = 't.created DESC';

        $this->getDbCriteria()->mergeWith($criteria);
        return $this;
    }

}
