<?php

class RestApiFile extends BaseRestApiFile
{

    /**
     * @inheritdoc
     */
    public static function getApiAttributes(\propel\models\File $item, $level = 0)
    {
        $return = parent::getApiAttributes($item, $level);
        $return["absolute_url"] = $item->absoluteUrl();
        $return["fileInstances"] = RelatedItems::formatItems(
            "FileInstance",
            $item,
            "FileId",
            $level //+1 // Ensure one less level of this relation gets populated
        );
        return $return;
    }

    /**
     * @inheritdoc
     */
    public static function getListableAttributes(\propel\models\File $item, $level = 0)
    {
        $return = parent::getListableAttributes($item, $level);
        return $return;
    }

    /**
     * @inheritdoc
     */
    public static function getRelatedAttributes(\propel\models\File $item, $level)
    {
        $return = parent::getRelatedAttributes($item, $level);
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
        if (!empty($requestAttributes['attributes']->fileInstances)) {

            $fileInstances = new \Propel\Runtime\Collection\Collection();
            foreach ($requestAttributes['attributes']->fileInstances as $fileInstanceInRequest) {

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
