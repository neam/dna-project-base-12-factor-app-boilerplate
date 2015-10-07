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
    use \\".$this->getUnqualifiedClassName()."Trait;

    /**
     * Skeleton method for returning the label for a specific item.
     * Customize to return a representable label string.
     *
     * @return string
     */
    public function getItemLabel()
    {
        return \$this->defaultItemLabel(\"getId\");
    }
    ";

    }

}
