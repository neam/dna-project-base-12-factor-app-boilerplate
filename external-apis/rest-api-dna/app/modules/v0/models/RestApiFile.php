<?php

class RestApiFile extends BaseRestApiFile
{

    /**
     * @inheritdoc
     */
    public static function getApiAttributes(\propel\models\File $item)
    {
        $return = parent::getApiAttributes($item);
        $return["fileInstances"] = RelatedItems::formatItems(
            "FileInstance",
            $item,
            "FileId",
            $level = 1 //+1 // Ensure one less level of this relation gets populated
        );
        return $return;
    }

    /**
     * @inheritdoc
     */
    public static function getWrapperAttributes(\propel\models\File $item = null)
    {
        $return = parent::getWrapperAttributes($item);
        $return["absolute_url"] = $item ? $item->absoluteUrl() : null;
        $return["filename"] = $item ? $item->getFilename() : null;
        $return["size"] = $item ? $item->getSize() : null;
        $return["created"] = $item ? $item->getCreated("Y-m-d H:i:s") : null;
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

        if (!empty($requestAttributes->attributes->localFileInstance->attributes)) {

            $fileInstanceInRequest = $requestAttributes->attributes->localFileInstance;
            $attributes = (array) $fileInstanceInRequest->attributes;
            unset($attributes["file"]); // Remove redundant file attribute which already is defined to be the current $item due to the request structure
            $fileInstance = new \propel\models\FileInstance();
            $fileInstance->fromArray($attributes, \Propel\Runtime\Map\TableMap::TYPE_FIELDNAME);
            $item->setFileInstanceRelatedByLocalFileInstanceId($fileInstance);

        }

        if (!empty($requestAttributes->attributes->publicFilesS3FileInstance->attributes)) {

            $fileInstanceInRequest = $requestAttributes->attributes->publicFilesS3FileInstance;
            $attributes = (array) $fileInstanceInRequest->attributes;
            unset($attributes["file"]); // Remove redundant file attribute which already is defined to be the current $item due to the request structure
            $fileInstance = new \propel\models\FileInstance();
            $fileInstance->fromArray($attributes, \Propel\Runtime\Map\TableMap::TYPE_FIELDNAME);
            $item->setFileInstanceRelatedByPublicFilesS3FileInstanceId($fileInstance);

        }

        if (!empty($requestAttributes->attributes->filestackFileInstance->attributes)) {

            $fileInstanceInRequest = $requestAttributes->attributes->filestackFileInstance;
            $attributes = (array) $fileInstanceInRequest->attributes;
            unset($attributes["file"]); // Remove redundant file attribute which already is defined to be the current $item due to the request structure
            $fileInstance = new \propel\models\FileInstance();
            $fileInstance->fromArray($attributes, \Propel\Runtime\Map\TableMap::TYPE_FIELDNAME);
            $item->setFileInstanceRelatedByFilestackFileInstanceId($fileInstance);

        }

        if (!empty($requestAttributes->attributes->filestackPendingFileInstance->attributes)) {

            $fileInstanceInRequest = $requestAttributes->attributes->filestackPendingFileInstance;
            $attributes = (array) $fileInstanceInRequest->attributes;
            unset($attributes["file"]); // Remove redundant file attribute which already is defined to be the current $item due to the request structure
            $fileInstance = new \propel\models\FileInstance();
            $fileInstance->fromArray($attributes, \Propel\Runtime\Map\TableMap::TYPE_FIELDNAME);
            $item->setFileInstanceRelatedByFilestackPendingFileInstanceId($fileInstance);

        }

    }

}
