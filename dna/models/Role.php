<?php

// auto-loading
Yii::setPathOfAlias('Role', dirname(__FILE__));
Yii::import('Role.*');

class Role extends MetadataRole
{
    // System roles
    const DEVELOPER = 'Developer';
    const SUPER_ADMINISTRATOR = 'SuperAdministrator';

    // Group roles
    const GROUP_ADMINISTRATOR = 'GroupAdministrator';
    const GROUP_PUBLISHER = 'GroupPublisher';
    const GROUP_EDITOR = 'GroupEditor';
    const GROUP_APPROVER = 'GroupApprover';
    const GROUP_MODERATOR = 'GroupModerator';
    const GROUP_CONTRIBUTOR = 'GroupContributor';
    const GROUP_REVIEWER = 'GroupReviewer';
    const GROUP_TRANSLATOR = 'GroupTranslator';
    const GROUP_MEMBER = 'GroupMember';

    // Default roles
    const AUTHENTICATED = 'Authenticated';
    const GUEST = 'Guest';

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
        return parent::getItemLabel();
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

}
