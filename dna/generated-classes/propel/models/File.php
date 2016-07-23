<?php

namespace propel\models;

use propel\models\Base\File as BaseFile;

/**
 * Skeleton subclass for representing a row from the 'file' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class File extends BaseFile
{

    use \FileTrait;
    use \neam\file_registry\FileTrait;

    /**
     * Skeleton method for returning the label for a specific item.
     * Customize to return a representable label string.
     *
     * @return string
     */
    public function getItemLabel()
    {
        $label = "";
        if (!empty($this->getFilename())) {
            $label .= $this->getFilename();
        } else {
            $label .= "[missing file name]";
        }
        if (!empty($this->getMimetype())) {
            $label .= " [";
            $label .= $this->getMimetype();
            $label .= "]";
        } else {
            $label .= " [missing mime type]";
        }
        if (!empty($this->absoluteUrl())) {
            $label .= " " . $this->absoluteUrl();
        }
        if (!empty($this->getCreated("Y-m-d H:i:s"))) {
            $label .= " - Created ";
            $label .= $this->getCreated("Y-m-d H:i:s");
        }
        return $label;
    }

}
