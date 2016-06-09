<?php

trait DnaProjectBaseSuggestionsTrait
{

    static public function getAvailableAlgorithms_DnaProjectBase()
    {

        $algorithms = [
            "ensureLocalFiles" => [
                "affected-item-types" => [
                    "File" => [
                        static::UPDATE,
                    ],
                    "FileInstance" => [
                        static::CREATE,
                    ],
                ],
                // Since it moves the local files - TODO: Make previewable by implementing roll back for file operations
                "rollback-supported" => false,
            ],
            "ensureRemoteFiles" => [
                "affected-item-types" => [
                    "File" => [
                        static::UPDATE,
                    ],
                    "FileInstance" => [
                        static::CREATE,
                    ],
                ],
                // Since it moves the local files - TODO: Make previewable by implementing roll back for file operations
                "rollback-supported" => false,
            ],
            "setFileMetadataWhereMissing" => [
                "affected-item-types" => [
                    "File" => [
                        static::UPDATE,
                    ],
                ],
            ],
        ];

        return $algorithms;

    }

    /**
     * Ensures:
     * 1. That all file-records has a local file in the correct path
     * 2. That all file-records have a local file instance
     */
    static public function ensureLocalFiles()
    {

        $files = \propel\models\FileQuery::create()->find();
        foreach ($files as $file) {
            $file->ensureLocalFileInCorrectPath();
        }

    }

    /**
     * Ensures:
     * 1. That all file-records has a remote file in the correct path
     * 2. That all file-records have a remote file instance
     */
    static public function ensureRemoteFiles()
    {

        $files = \propel\models\FileQuery::create()->find();
        foreach ($files as $file) {
            throw new Exception("TODO");
            $file->ensureRemoteFileInCorrectPath();
        }

    }

    static public function setFileMetadataWhereMissing()
    {

        $files = \propel\models\FileQuery::create()->find();
        foreach ($files as $file) {
            $file->ensureFileMetadata();
        }

    }

}