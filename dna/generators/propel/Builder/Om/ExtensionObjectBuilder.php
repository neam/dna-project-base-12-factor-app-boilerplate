<?php

/**
 * This file is part of the Propel package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license MIT License
 */

namespace generators\propel\Builder\Om;

/**
 * Generates the empty PHP5 stub object class for user object model (OM).
 */
class ExtensionObjectBuilder extends \Propel\Generator\Builder\Om\ExtensionObjectBuilder
{

    /**
     * Specifies the methods that are added as part of the stub object class.
     *
     * @see ObjectBuilder::addClassBody()
     */
    protected function addClassBody(&$script)
    {

        $script .= "
    use \\" . $this->getUnqualifiedClassName() . "Trait;

    /**
     * Skeleton method for returning the label for a specific item.
     * Customize to return a representable label string.
     *
     * @return string
     */
    public function getItemLabel()
    {
        return \$this->defaultItemLabel(\"get" . $this->getLabelAttribute() . "\");
    }
    ";

    }

    /**
     * Best guess of which attribute is the label attribute
     * TODO: Should be based on content model metadata instead of guessing
     */
    protected function getLabelAttribute()
    {

        $tableColumns = $this->getTable()->getColumns();

        /**
         * @var string $name
         * @var \Propel\Generator\Model\Column $column
         */
        foreach ($tableColumns as $name => $column) {

            // First check common attributes for labels
            if ($column->getLowercasedName() === 'label') {
                return $column->getPhpName();
            }
            if ($column->getLowercasedName() === 'title') {
                return $column->getPhpName();
            }
            if ($column->getLowercasedName() === 'name') {
                return $column->getPhpName();
            }
            if ($column->getLowercasedName() === 'ref') {
                return $column->getPhpName();
            }

            // Find the first column of type 'string'
            if ($column->getType() !== 'TIMESTAMP'
                && $column->getPhpType() === 'string'
                && !$column->isPrimaryKey()
            ) {
                return $column->getPhpName();
                break;
            }

        }

        // If the columns contains no column of type 'string', return the
        // first column (usually the primary key)
        return $tableColumns[0]->getPhpName();

    }

}
