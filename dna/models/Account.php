<?php

// auto-loading
Yii::setPathOfAlias('Account', dirname(__FILE__));
Yii::import('Account.*');

/**
 * Note: This class must replicate the functionality of \nordsoftware\yii_account\models\ar\Account
 * Until that functionality is implemented entirely as behaviors/traits, we must manually make sure
 * that the validation rules, behavior settings, constants etc are up to date with the upstream model class
 */
class Account extends MetadataAccount
{
    const PASSWORD_MIN_LENGTH = 4;
    const USERNAME_MIN_LENGTH = 3;

    // Auth item types
    const AUTH_ITEM_TYPE_OPERATION = 0;
    const AUTH_ITEM_TYPE_TASK = 1;
    const AUTH_ITEM_TYPE_ROLE = 2;

    // Add your model-specific methods here. This file will not be overridden by gtc except you force it.
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
    }

    /**
     * @inheritDoc
     */
    public function getItemLabel()
    {
        return parent::getItemLabel();
    }

    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            $this->trait_behaviors(),
            array(
                'PasswordBehavior' => array(
                    'class' => '\YiiPassword\Behavior',
                    'defaultStrategyName' => 'bcrypt',
                    'strategies' => array(
                        'bcrypt' => array(
                            'class' => '\YiiPassword\Strategies\Bcrypt',
                            'minLength' => self::PASSWORD_MIN_LENGTH,
                            'workFactor' => 12,
                        ),
                        'legacy' => array(
                            'class' => '\YiiPassword\Strategies\LegacyMd5',
                            'minLength' => self::PASSWORD_MIN_LENGTH,
                        )
                    ),
                ),
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function relations()
    {
        return array_merge(
            $this->trait_relations(),
            array(
                'profile' => array(self::HAS_ONE, 'Profile', 'account_id'),
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function rules()
    {
        $parentRules = $this->trait_rules();

        // the \nordsoftware\yii_account\models\ar\Account schema states that create_at and salt are required
        // but the extension chooses to validate against the model before setting these values...
        // thus we need to manually remove this validation rule for yii-account sign-up form to work as expected
        unset($parentRules[array_search(array('create_at, salt', 'required'), $parentRules)]);

        return array_merge(
            $parentRules,
            array(
                array('username', 'length', 'min' => self::USERNAME_MIN_LENGTH),
                array('password', 'length', 'min' => self::PASSWORD_MIN_LENGTH),
                array('username, email', 'required'),
                array(
                    'username',
                    'unique',
                    'allowEmpty' => false,
                    'message' => Yii::t('app', 'Username already exists.')
                ),
                array(
                    'username',
                    'match',
                    'pattern' => '/^[A-Za-z0-9_]+$/u',
                    'message' => Yii::t('app', 'Incorrect symbols (A-z0-9).')
                ),
                array('email', 'unique', 'message' => Yii::t('app', 'Email address already exists.')),
                array('email', 'email'),
                array('activkey', 'default', 'setOnEmpty' => true, 'value' => ''),
            )
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

    /**
     * Returns the auth items.
     * @param integer $type the item type (0: operation, 1: task, 2: role). Defaults to 2 (role).
     * @return array the auth items.
     */
    public function getAuthItems($type = self::AUTH_ITEM_TYPE_ROLE)
    {
        return array_keys(Yii::app()->authManager->getAuthItems($type, $this->id));
    }

    /**
     * Returns the roles.
     * @return array
     */
    public function getGroupHasAccounts()
    {
        return U::arAttributes(PermissionHelper::getGroupHasAccountsForAccount($this->id));
    }

    /**
     * Automatically assign default group roles to new members
     */
    public function assignDefaultGroupRoles()
    {
        //PermissionHelper::addAccountToGroup($this->id, Group::TRANSLATORS, Role::GROUP_TRANSLATOR);
        //PermissionHelper::addAccountToGroup($this->id, Group::REVIEWERS, Role::GROUP_REVIEWER);
    }

    /**
     * Checks if the item has a group.
     * @param string $group
     * @param string|null $role
     * @return boolean
     */
    public function belongsToGroup($group, $role = null)
    {
        $attributes = array(
            'account_id' => $this->id,
            'group_id' => PermissionHelper::groupNameToId($group),
        );

        if ($role !== null) {
            $attributes['role_id'] = PermissionHelper::roleNameToId($role);
        }

        return PermissionHelper::groupHasAccount($attributes);
    }

    /**
     * Checks if a role and its associated groups are active.
     * @param string $roleName
     * @return boolean
     */
    public function roleIsActive($roleName)
    {
        $roleId = PermissionHelper::roleNameToId($roleName);
        $roleToGroupsMap = MetaData::roleToGroupsMap($roleName);
        $groups = isset($roleToGroupsMap[$roleName]) ? $roleToGroupsMap[$roleName] : array();
        $groupCount = count($groups);
        $groupHasAccountCount = 0;

        if ($groupCount > 0) {
            foreach ($groups as $group) {
                $groupHasAccount = PermissionHelper::groupHasAccount(
                    array(
                        'account_id' => $this->id,
                        'group_id' => PermissionHelper::groupNameToId($group),
                        'role_id' => $roleId,
                    )
                );

                if ($groupHasAccount) {
                    $groupHasAccountCount++;
                }
            }

            $roleIsActive = (int) $groupHasAccountCount === (int) $groupCount;
        } else {
            $roleIsActive = false;
        }

        return $roleIsActive;
    }

    public function groupRoleIsActive($group, $role)
    {
        return PermissionHelper::groupHasAccount(
            array(
                'account_id' => $this->id,
                'group_id' => PermissionHelper::groupNameToId($group),
                'role_id' => PermissionHelper::roleNameToId($role),
            )
        );
    }

}
