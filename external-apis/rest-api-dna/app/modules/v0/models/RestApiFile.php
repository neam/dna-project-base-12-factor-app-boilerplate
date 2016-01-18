<?php

class RestApiFile extends BaseRestApiFile
{

    /**
     * @inheritdoc
     */
    public static function getApiAttributes(\propel\models\File $item)
    {
        /*
        $return["fileInstances"] = RelatedItems::formatItems(
            "FileInstance",
            $item,
            "FileId",
            $level //+1 // Ensure one less level of this relation gets populated
        );
        */
        $return = parent::getApiAttributes($item);
        return $return;
    }

    /**
     * @inheritdoc
     */
    public static function getWrapperAttributes(\propel\models\File $item = null)
    {
        $return = parent::getWrapperAttributes($item);
        $return["absolute_url"] = $item ? $item->absoluteUrl() : null;
        return $return;
    }

    /**
     * @inheritdoc
     */
    public static function getItemAttributes(\propel\models\File $item)
    {
        $return = parent::getItemAttributes($item);
        return $return;
    }

    /**
     * @inheritdoc
     */
    public static function setCreateAttributes(\propel\models\File $item, $requestAttributes)
    {
        parent::setCreateAttributes($item, $requestAttributes);
    }

    /**
     * @inheritdoc
     */
    public static function setUpdateAttributes(\propel\models\File $item, $requestAttributes)
    {
        parent::setUpdateAttributes($item, $requestAttributes);
    }

    /**
     * @inheritdoc
     */
    public static function setItemAttributes(\propel\models\File $item, $requestAttributes)
    {
        parent::setItemAttributes($item, $requestAttributes);

        // Also add file-instances that are specified in the request
        // For now, we simply replace existing file-instances with the ones sent in the request
        if (!empty($requestAttributes->attributes->fileInstances)) {

            $fileInstances = new \Propel\Runtime\Collection\Collection();
            foreach ($requestAttributes->attributes->fileInstances as $fileInstanceInRequest) {

                $attributes = (array) $fileInstanceInRequest->attributes;
                // Remove redundant file attribute which already is defined to be the current $item due to the request structure
                unset($attributes["file"]);
                $fileInstance = new \propel\models\FileInstance();
                $fileInstance->fromArray($attributes, \Propel\Runtime\Map\TableMap::TYPE_FIELDNAME);
                $fileInstances->append($fileInstance);

            }
            $item->setFileInstances($fileInstances);

        }

    }

}
