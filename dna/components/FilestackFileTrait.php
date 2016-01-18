<?php

namespace neam\file_registry;

use GuzzleHttp;
use propel\models\File;
use propel\models\FileInstance;

trait FilestackFileTrait
{

    static public function filestackCdnUrl($filestackUrl)
    {

        // Use Filestack's CDN - Including legacy Filepicker's CDN since it still is in use by Filestack for probably buggy reasons
        return str_replace(
            ["www.filestackapi.com", "www.filepicker.io"],
            ["cdn.filestackcontent.com", "cdn.filepicker.io"],
            $filestackUrl
        );

    }

    static public function createFileInstanceWithMetadataByFilestackUrl($filestackUrl)
    {

        $fileInstance = new FileInstance();
        $fileInstance->setStorageComponentRef('filestack');
        $fileInstance->setUri($filestackUrl);
        static::decorateFileInstanceWithFilestackMetadataByFilestackUrl($fileInstance, $filestackUrl);
        return $fileInstance;

    }

    static public function decorateFileInstanceWithFilestackMetadataByFilestackUrl(
        \propel\models\FileInstance $fileInstance,
        $filestackUrl
    ) {

        $handle = static::extractHandleFromFilestackUrl($filestackUrl);
        $client = new GuzzleHttp\Client();
        $response = $client->get('https://www.filestackapi.com/api/file/' . $handle . '/metadata');

        $data = new \stdClass();
        $data->fpfile = $response;
        $data->fpkey = FILEPICKER_API_KEY;

        $fileInstance->setDataJson(json_encode($data));

    }

    static public function extractHandleFromFilestackUrl($filestackUrl)
    {

        $urlinfo = parse_url($filestackUrl);
        $_ = explode("/", $urlinfo["path"]);
        return $_[3];

    }

    public function createFileFromFilestackUrl($filestackUrl)
    {

        $fileInstance = static::createFileInstanceWithMetadataByFilestackUrl($filestackUrl);
        $data = json_decode($fileInstance->getDataJson());

        $file = new File();
        $file->setSize($data->fpfile->size);
        $file->setMimetype($data->fpfile->mimetype);
        $file->setFilename($data->fpfile->filename);
        $file->setOriginalFilename($data->fpfile->filename);
        $fileInstances = $file->getFileInstances();
        $fileInstances->append($fileInstance);
        $file->setFileInstances($fileInstances);

        return $file;

    }

}
