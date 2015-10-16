<?php

namespace propel\models;

use propel\models\Base\Node as BaseNode;

/**
 * Skeleton subclass for representing a row from the 'node' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class Node extends BaseNode
{

    use \NodeTrait;

    /**
     * Skeleton method for returning the label for a specific item.
     * Customize to return a representable label string.
     *
     * @return string
     */
    public function getItemLabel()
    {
        return "Node #" . $this->getId();
    }

}
