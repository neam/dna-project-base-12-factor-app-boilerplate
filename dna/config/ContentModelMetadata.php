<?php

namespace __PROJECT__\dna\config;

use stdClass;
use Exception;

/**
 * PHP wrapper around content-model-metadata.json used to extract information
 * used in dna project base
 *
 * Class DnaProjectBaseContentModelMetadata
 */
class ContentModelMetadata
{

    /**
     * @var stdClass representation of the content model metadata json
     */
    protected $model = null;

    /**
     * @var array
     */
    protected $itemTypes = [];

    /**
     * @return
     * @throws Exception
     */
    public function __construct($jsonPath)
    {
        $json = file_get_contents($jsonPath);
        $this->model = json_decode($json);
        if (empty($this->model)) {
            throw new Exception("Content model metadata json was found empty or was not parsed successfully");
        }
    }

    /**
     * @return array the meta version defined in the current content model metadata.
     */
    public function getMetaVersion()
    {
        return $this->model->meta->version;
    }

    /**
     * @return array the item types defined in the current content model metadata.
     */
    public function getItemTypes()
    {
        if (!empty($this->itemTypes)) {
            return $this->itemTypes;
        }

        $this->itemTypes = [];

        foreach ($this->model->itemTypes as $itemType) {

            $traitName = $itemType->model_class . 'Trait';
            $statusRequirements = $itemType->is_preparable ? static::generateStatusRequirements($itemType) : [];
            $flowSteps = $itemType->is_workflow_item ? static::generateFlowSteps($itemType) : [];
            $flowStepCaptions = $itemType->is_workflow_item ? static::generateFlowStepCaptions($itemType) : [];
            $dependencies = static::generateDependencies($itemType);
            //var_dump($dependencies);die();

            // Add additional metadata
            $itemType->computed = [
                'traitName' => $traitName,
                'statusRequirements' => $statusRequirements,
                'flowSteps' => $flowSteps,
                'flowStepCaptions' => $flowStepCaptions,
                'labels' => static::generateLabels($itemType),
                'hints' => static::generateHints($itemType),
                'dependencies' => $dependencies,
            ];

            $this->itemTypes[$itemType->model_class] = $itemType;

        }

        return $this->itemTypes;

    }

    public function generateStatusRequirements($itemType)
    {
        $statusRequirements = [];
        foreach ($itemType->attributes as $attribute) {
            if (empty($attribute->preparableStatusRequirement)) {
                continue;
            }
            $statusRequirements[$attribute->preparableStatusRequirement->ref][] = $attribute->ref;
        }
        return $statusRequirements;
    }

    public function generateFlowSteps($itemType)
    {
        $flowSteps = [];
        foreach ($itemType->attributes as $attribute) {
            if (empty($attribute->workflowItemStep)) {
                continue;
            }
            $flowSteps[$attribute->workflowItemStep->ref][] = $attribute->ref;
        }
        return $flowSteps;
    }

    public function generateFlowStepCaptions($itemType)
    {
        $flowStepCaptions = [];
        foreach ($itemType->attributes as $attribute) {
            if (empty($attribute->workflowItemStep)) {
                continue;
            }
            $flowStepCaptions[$attribute->workflowItemStep->ref] = $attribute->workflowItemStep->_title;
        }
        return $flowStepCaptions;
    }

    /**
     * Generates the attribute labels for the specified item type.
     * @param stdClass $itemType the item type metadata
     * @return array the generated attribute labels (name => label)
     */
    public function generateLabels($itemType)
    {
        $labels = [];
        foreach ($itemType->attributes as $attribute) {
            if (empty($attribute->label)) {
                continue;
            }
            $labels[$attribute->ref] = $attribute->label;
        }
        return $labels;
    }

    /**
     * Generates the attribute labels for the specified item type.
     * @param stdClass $itemType the item type metadata
     * @return array the generated attribute labels (name => label)
     */
    public function generateHints($itemType)
    {
        $hints = [];
        foreach ($itemType->attributes as $attribute) {
            if (empty($attribute->hint)) {
                continue;
            }
            $hints[$attribute->ref] = $attribute->hint;
        }
        return $hints;
    }

    const MIXIN_HAS_MANY_HANDSONTABLE_INPUT = 'has-many-handsontable-input';
    const MIXIN_I18N_ATTRIBUTE_MESSAGES = 'i18n-attribute-messages';
    const MIXIN_I18N_COLUMNS_ATTRIBUTE = 'i18n-columns-attribute';
    const MIXIN_I18N_COLUMNS_RELATION = 'i18n-columns-relation';
    const MIXIN_OWNABLE = 'ownable';
    const MIXIN_PERMALINKABLE_FILES = 'permalinkable-files';
    const MIXIN_PERMALINKABLE_ITEM = 'permalinkable-item';
    const MIXIN_RESTRICTED_ACCESS = 'restricted-access';
    const MIXIN_RELATIONAL_GRAPH_DB = 'relational-graph-db';
    const MIXIN_RELATED_ITEMS_SIR_TREVOR_UI = 'related-items-sir-trevor-ui-behavior';
    const MIXIN_QA_STATE = 'qa-state';

    protected function generateDependencies($itemType)
    {

        $traits = [];
        $mixins = [];
        $rules = [];
        $relations = [];
        $attributes = [];

        // is_translatable
        if ($itemType->is_translatable) {

            // Get attributes to translate and what mixin to use
            foreach ($itemType->attributes as $attribute) {
                if (empty($attribute->translatableBehaviorChoice)) {
                    continue;
                }

                // Check attribute type
                switch ($attribute->translatableBehaviorChoice->ref) {
                    case static::MIXIN_I18N_COLUMNS_ATTRIBUTE:
                    case static::MIXIN_I18N_ATTRIBUTE_MESSAGES:
                        $mixins[$attribute->translatableBehaviorChoice->ref][] = $attribute->ref;
                        break;
                    case static::MIXIN_I18N_COLUMNS_RELATION:
                        $mixins[static::MIXIN_I18N_COLUMNS_RELATION][$attribute->ref] = $attribute->db_column;
                        break;
                    default:
                        // Ignore
                }
            }

            $rules[] = '$this->i18nRules()';

        }

        // is_listable
        if ($itemType->is_listable) {

            $attributes[] = 'thumb';
            $attributes[] = 'heading';
            $attributes[] = 'subheading';
            $attributes[] = 'caption';

        }

        // is_presentable
        if ($itemType->is_presentable) {

            $attributes[] = 'about';
            $attributes[] = 'related';
            $attributes[] = 'contributions';

        }

        // is_composable
        if ($itemType->is_composable) {

            $attributes[] = 'composition';
            $attributes[] = 'composition_type_id';

        }

        // is_graph_relatable
        if ($itemType->is_graph_relatable) {

            $traits[] = 'GraphRelatableItemTrait';
            $mixins[static::MIXIN_RELATIONAL_GRAPH_DB] = [];
            $relations[] = '$this->graphRelatableItemBaseRelations()';
            $attributes[] = 'node_id';

            // has attributes with graph_relation_item_type_constraints
            $graph_relation_item_type_constraints = [];
            foreach ($itemType->attributes as $attribute) {
                if (!empty($attribute->graph_relation_item_type_constraint)) {
                    $relationName = $attribute->ref;
                    $modelClass = $attribute->graph_relation_item_type_constraint;
                    $relations[] = '$this->relationalGraphDbRelation("' . $relationName . '", "' . $modelClass . '")';
                    $graph_relation_item_type_constraints[$relationName] = $attribute->graph_relation_item_type_constraint;
                }
            }
            if (!empty($graph_relation_item_type_constraints)) {
                $mixins[static::MIXIN_RELATED_ITEMS_SIR_TREVOR_UI] = $graph_relation_item_type_constraints;
                $mixins[static::MIXIN_RELATIONAL_GRAPH_DB] = $graph_relation_item_type_constraints;
            }

        }

        // is_permalinkable
        if ($itemType->is_permalinkable) {

            $traits[] = '\neam\yii_permalinkable_items_core\traits\PermalinkableItemTrait';
            $mixins[static::MIXIN_HAS_MANY_HANDSONTABLE_INPUT][] = 'routes';
            $mixins[static::MIXIN_PERMALINKABLE_ITEM] = [];
            $relations[] = '$this->permalinkableItemRelations()';
            $attributes[] = 'routes';

        }

        // has_permalinkable_files
        $permalinkable_file_route_attribute_refs = [];
        foreach ($itemType->attributes as $attribute) {
            // Check for file route attributes
            if (!empty($attribute->permalinkable_file_route_attribute_ref)) {
                $permalinkable_file_route_attribute_refs[] = $attribute->permalinkable_file_route_attribute_ref;
            }
        }
        if (!empty($permalinkable_file_route_attribute_refs)) {
            $traits[] = '\neam\yii_permalinkable_items_core\traits\PermalinkableItemTrait';
            $mixins[static::MIXIN_HAS_MANY_HANDSONTABLE_INPUT][] = 'fileRoutes';
            $mixins[static::MIXIN_PERMALINKABLE_FILES] = $permalinkable_file_route_attribute_refs;
        }

        // attributes with edit_relation_using_handsontable_input
        foreach ($itemType->attributes as $attribute) {
            if (!empty($attribute->edit_relation_using_handsontable_input)) {
                $mixins[static::MIXIN_HAS_MANY_HANDSONTABLE_INPUT][] = $attribute->ref;
            }
        }

        // attributes with edit_relation_using_sir_trevor_ui
        foreach ($itemType->attributes as $attribute) {
            if (!empty($attribute->edit_relation_using_sir_trevor_ui)) {
                $mixins[static::MIXIN_RELATED_ITEMS_SIR_TREVOR_UI][] = [$attribute->ref => $attribute->graph_relation_item_type_constraint];
            }
        }

        // is_ownable
        if ($itemType->is_ownable) {

            $mixins[static::MIXIN_OWNABLE] = [];
            $attributes[] = 'owner_id';

        }

        // is_workflow_item
        if ($itemType->is_workflow_item) {

            //$traits[] = '\neam\yii_workflow_core\traits\ItemTrait';
            $traits[] = 'WorkflowUiItemTrait';
            $mixins[static::MIXIN_HAS_MANY_HANDSONTABLE_INPUT][] = 'changesets';
            $rules[] = '$this->flowStepRules()';
            $relations[] = "array('changesets' => array(CActiveRecord::HAS_MANY, 'Changeset', array('id' => 'node_id'), 'through' => 'node'))";

        }

        // is_preparable
        if ($itemType->is_preparable) {

            $mixins[static::MIXIN_QA_STATE] = [];
            $rules[] = '$this->statusRequirementsRules()';
            $attributes[] = $itemType->table . '_qa_state_id';

        }

        // is_access_restricted
        if ($itemType->is_access_restricted) {

            $traits[] = 'RestrictedAccessItemTrait';
            $mixins[static::MIXIN_RESTRICTED_ACCESS] = [];
            $attributes[] = 'node_id';
            $attributes[] = 'owner_id';

        }

        // is_versioned
        if ($itemType->is_versioned) {

            $attributes[] = 'version';
            $attributes[] = 'cloned_from_id';

        }

        // is_timestamped
        if ($itemType->is_timestamped) {

            $attr = $itemType->attributes;

            // generate correct settings for created and/or modified fields
            if (array_key_exists("created", $attr) && array_key_exists("modified", $attr)) {
                $behaviors['CTimestampBehavior'] = array(
                    'class' => 'zii.behaviors.CTimestampBehavior',
                    'createAttribute' => 'created',
                    'updateAttribute' => 'modified',
                );
            }
            if (array_key_exists("created", $attr) && !array_key_exists("modified", $attr)) {
                $behaviors['CTimestampBehavior'] = array(
                    'class' => 'zii.behaviors.CTimestampBehavior',
                    'createAttribute' => 'created',
                    'updateAttribute' => null,
                );
            }

        }

        // is_labeled
        if ($itemType->is_labeled) {

            $attributes[] = 'label';

        }

        return [
            'traits' => $traits,
            'mixins' => $mixins,
            'rules' => $rules,
            'relations' => $relations,
            'attributes' => $attributes,
        ];

    }

/*
<?php foreach ($traits as $trait): ?>
    use <?= "$trait;\n" ?>
<?php endforeach; ?>

    public function behaviors()
    {
        $behaviors = [];

<?php // MIXIN_HAS_MANY_HANDSONTABLE_INPUT

if (isset($mixins[Generator::MIXIN_HAS_MANY_HANDSONTABLE_INPUT])): $attributes = $mixins[Generator::MIXIN_HAS_MANY_HANDSONTABLE_INPUT]; ?>
        $behaviors['HasManyHandsontableInputBehavior'] = array(
            'class' => '\neam\yii_relations_ui_core\behaviors\HasManyHandsontableInputBehavior',
            'attributes' => array(
<?php foreach ($attributes as $attribute): ?>
                <?= "'$attribute',\n" ?>
<?php endforeach; ?>
            ),
        );

<?php endif;

// MIXIN_I18N_ATTRIBUTE_MESSAGES

if (isset($mixins[Generator::MIXIN_I18N_ATTRIBUTE_MESSAGES])): $attributes = $mixins[Generator::MIXIN_I18N_ATTRIBUTE_MESSAGES]; ?>
        // Model attributes and relations to make multilingual using yii-i18n-attribute-messages
        $behaviors['i18n-attribute-messages'] = array(
            'class' => 'I18nAttributeMessagesBehavior',
            'translationAttributes' => array(
<?php foreach ($attributes as $attribute): ?>
                <?= "'$attribute',\n" ?>
<?php endforeach; ?>
            ),
            'languageSuffixes' => LanguageHelper::getCodes(),
            'behaviorKey' => 'i18n-attribute-messages',
            'displayedMessageSourceComponent' => 'displayedMessages',
            'editedMessageSourceComponent' => 'editedMessages',
        );

<?php endif;

// MIXIN_I18N_COLUMNS_ATTRIBUTE
// MIXIN_I18N_COLUMNS_RELATION

if (isset($mixins[Generator::MIXIN_I18N_COLUMNS_ATTRIBUTE]) || isset($mixins[Generator::MIXIN_I18N_COLUMNS_RELATION])):
    $attributes = !empty($mixins[Generator::MIXIN_I18N_COLUMNS_ATTRIBUTE]) ? $mixins[Generator::MIXIN_I18N_COLUMNS_ATTRIBUTE] : [];
    $multilingualRelations = !empty($mixins[Generator::MIXIN_I18N_COLUMNS_RELATION]) ? $mixins[Generator::MIXIN_I18N_COLUMNS_RELATION] : [];
    ?>
        // Model attributes and relations to make multilingual using yii-i18n-columns
        $behaviors['i18n-columns'] = array(
            'class' => 'I18nColumnsBehavior',
            'translationAttributes' => array(
<?php foreach ($attributes as $attribute): ?>
                <?= "'$attribute',\n" ?>
<?php endforeach; ?>
            ),
            'multilingualRelations' => array(
<?php foreach ($multilingualRelations as $relation => $db_column): ?>
                <?= "'$relation' => '$db_column',\n" ?>
<?php endforeach; ?>
            ),
        );

<?php endif;

// MIXIN_OWNABLE

if (isset($mixins[Generator::MIXIN_OWNABLE])): ?>
        $behaviors['owner-behavior'] = array(
            'class' => 'OwnerBehavior',
        );

        // Do not attach owner-behavior when running unit/functional tests - TODO: remove this silly workaround
        if (defined('TESTING')) {
            unset($behaviors['owner-behavior']);
        }

<?php endif;

// MIXIN_PERMALINKABLE_FILES

if (isset($mixins[Generator::MIXIN_PERMALINKABLE_FILES])): $attributes = $mixins[Generator::MIXIN_PERMALINKABLE_FILES]; ?>
        // Permalinkable/routable files
        $behaviors['permalinkable-files'] = array(
            'class' => '\neam\yii_permalinkable_items_core\behaviors\PermalinkableItemBehavior',
            'relation' => 'fileRoutes',
            'routeClass' => 'FileRoute',
            'fileRouteAttributeRefs' => array(
<?php foreach ($attributes as $attribute): ?>
                <?= "'$attribute',\n" ?>
<?php endforeach; ?>
            ),
        );

<?php endif;

// MIXIN_PERMALINKABLE_ITEM

if (isset($mixins[Generator::MIXIN_PERMALINKABLE_ITEM])): ?>
        // Permalinkable/routable items
        $behaviors['permalinkable-item'] = array(
            'class' => '\neam\yii_permalinkable_items_core\behaviors\PermalinkableItemBehavior',
            'relation' => 'routes',
            'routeClass' => 'Route',
            'fileRouteAttributeRefs' => array(),
        );

<?php endif;

// MIXIN_RESTRICTED_ACCESS

if (isset($mixins[Generator::MIXIN_RESTRICTED_ACCESS])): ?>
        $behaviors['RestrictedAccessBehavior'] = array(
            'class' => '\RestrictedAccessBehavior',
        );

<?php endif;

// MIXIN_RELATIONAL_GRAPH_DB

if (isset($mixins[Generator::MIXIN_RELATIONAL_GRAPH_DB])): ?>
        $behaviors['relational-graph-db'] = array(
            'class' => 'dna.vendor.neam.yii-relational-graph-db.behaviors.RelatedNodesBehavior',
        );

<?php endif;

// MIXIN_RELATED_ITEMS_SIR_TREVOR_UI

if (isset($mixins[Generator::MIXIN_RELATED_ITEMS_SIR_TREVOR_UI])): $attributes = $mixins[Generator::MIXIN_RELATED_ITEMS_SIR_TREVOR_UI]; ?>
        $behaviors['related-items-sir-trevor-ui-behavior'] = array(
            'class' => 'dna.vendor.neam.yii-relational-graph-db.behaviors.RelatedNodesSirTrevorUiBehavior',
            'attributes' => array(),
        );

<?php foreach ($attributes as $relationName => $constraint): ?>
        $behaviors['related-items-sir-trevor-ui-behavior']['attributes']['<?= "$relationName" ?>'] = array(
            "ordered" => true,
            "relation" => "<?= "$relationName" ?>",
            "ModelClass" => "<?= "$constraint" ?>",
        );

<?php endforeach; ?>
<?php endif;

// MIXIN_QA_STATE

if (isset($mixins[Generator::MIXIN_QA_STATE])): ?>
        $behaviors['qa-state'] = array(
            'class' => 'QaStateBehavior',
            'scenarios' => array_merge(
                MetaData::qaStateCoreScenarios(),
                LanguageHelper::qaStateTranslateScenarios()
            ),
        );

<?php endif; ?>
        return array_merge(
            parent::behaviors(), $behaviors
        );

    }

    public function relations()
    {
        return array_merge(
            parent::relations(),
<?php foreach ($relations as $relation): ?>
            <?= "$relation,\n" ?>
<?php endforeach; ?>
            array()
        );
    }

    public function rules()
    {
        return array_merge(
            parent::rules(),
<?php foreach ($rules as $rule): ?>
            <?= "$rule,\n" ?>
<?php endforeach; ?>
            array()
        );
    }
*/

    /**
     * Define item type attributes
     * @return array
    public function itemTypeAttributes()
    {
        return array(
<?php foreach ($itemType->attributes as $attribute): ?>
            '<?= "$attribute->ref" ?>' => array(
                'ref' => <?= var_export($attribute->ref, true) . ",\n" ?>
                'type' => <?= var_export($attribute->attributeType->ref, true) . ",\n" ?>
                'about_this_attribute' => <?= var_export($attribute->about_this_attribute, true) . ",\n" ?>
                'label' => <?= var_export($attribute->label, true) . ",\n" ?>
                'hint' => <?= var_export($attribute->hint, true) . ",\n" ?>
                'translatable_behavior_choice' => <?= ($attribute->translatableBehaviorChoice ? var_export($attribute->translatableBehaviorChoice->ref, true) : "NULL") . ",\n" ?>
            ),
<?php endforeach; ?>
        );
    }

<?php if ($itemType->is_preparable): ?>
    /**
     * Define status-dependent fields
     * @return array
    public function statusRequirements()
    {
        return array(
<?php foreach ($statusRequirements as $statusRequirementRef => $statusRequirement): ?>
            '<?= "$statusRequirementRef" ?>' => array(
    <?php foreach ($statusRequirement as $attribute): ?>
                <?= "'$attribute',\n" ?>
    <?php endforeach; ?>
            ),
<?php endforeach; ?>
        );
    }
<?php endif; ?>

<?php if ($itemType->is_workflow_item): ?>
    /**
     * Define step-dependent fields
     * @return array
    public function flowSteps()
    {
        return array(
<?php foreach ($flowSteps as $flowStepRef => $flowStep): ?>
            '<?= "$flowStepRef" ?>' => array(
    <?php foreach ($flowStep as $attribute): ?>
                <?= "'$attribute',\n" ?>
    <?php endforeach; ?>
            ),
<?php endforeach; ?>
        );
    }
<?php endif; ?>

<?php if ($itemType->is_workflow_item): ?>
    public function flowStepCaptions()
    {
        return array(
<?php foreach ($flowStepCaptions as $name=>$flowStepCaption): ?>
            <?= "'$name' => " . $generator->generateString($flowStepCaption) . ",\n" ?>
<?php endforeach; ?>
        );
    }
<?php endif; ?>

    public function attributeLabels()
    {
        return array_merge(
            parent::attributeLabels(), array(
<?php foreach ($labels as $name => $label): ?>
            <?= "'$name' => " . $generator->generateString($label) . ",\n" ?>
<?php endforeach; ?>
            )
        );
    }

    public function attributeHints()
    {
        return array_merge(
            parent::attributeHints(), array(
<?php foreach ($hints as $name => $label): ?>
            <?= "'$name' => " . $generator->generateString($label) . ",\n" ?>
<?php endforeach; ?>
            )
        );
    }
*/

}