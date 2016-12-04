<?php

trait DnaProjectBaseSuggestionsTrait
{

    static public function getAvailableAlgorithms_DnaProjectBase()
    {

        $algorithms = [
            "ensureCorrectLocalFiles" => [
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
            "ensureRemotePublicFiles" => [
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
            "setFileMetadataWhereMissingAndPossibleWithoutAccessingLocalFiles" => [
                "affected-item-types" => [
                    "File" => [
                        static::UPDATE,
                    ],
                ],
            ],
            "setFileMetadataWhereMissing" => [
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
        ];

        return $algorithms;

    }

    /**
     * Ensures:
     * 1. That all file-records have a correct local file instance
     * 2. That all file-record local file instances actually has their files in place locally
     */
    static public function ensureCorrectLocalFiles(stdClass $params)
    {
        Suggestions::status(__METHOD__);

        if (empty($params->limit)) {
            $params->limit = 10;
        }
        $filesQuery = \propel\models\FileQuery::create()
            ->filterByFilename(null, \Propel\Runtime\ActiveQuery\Criteria::NOT_EQUAL)
            ->limit($params->limit);

        if (!empty($params->campaignId)) {
            $filesQuery->filterByRelevantForCampaignId($params->campaignId);
        }

        foreach ($filesQuery->find() as $file) {
            $file->ensureCorrectLocalFile();
        }

    }

    /**
     * Ensures:
     * 1. That all file-records have a correct remote public file instance
     * 2. That all file-record remote public file instances actually has their files in place
     */
    static public function ensureRemotePublicFiles(stdClass $params)
    {
        Suggestions::status(__METHOD__);

        if (empty($params->limit)) {
            $params->limit = 10;
        }
        $filesQuery = \propel\models\FileQuery::create()
            ->limit($params->limit)
            ->orderByPublicFilesS3FileInstanceId(
            // Pick those that have no instance first
                \Propel\Runtime\ActiveQuery\Criteria::ASC
            )
            ->orderByModified(
            // Pick those that where modified the longest time ago (more likely to need publishing first) TODO: Use a proper public_files_s3_last_ensured_utc_datetime attribute instead so that we always know that subsequent calls to this operation will lead to all files being published and updated continuously
                \Propel\Runtime\ActiveQuery\Criteria::ASC
            );

        if (!empty($params->campaignId)) {
            $filesQuery->filterByRelevantForCampaignId($params->campaignId);
        }

        foreach ($filesQuery->find() as $file) {
            $file->ensureRemotePublicFileInstance();
        }

    }

    static public function setFileMetadataWhereMissingAndPossibleWithoutAccessingLocalFiles(stdClass $params)
    {
        Suggestions::status(__METHOD__);

        if (empty($params->limit)) {
            $params->limit = 10;
        }
        $filesQuery = \propel\models\FileQuery::create()
            ->filterBySize(null)
            ->filterByMimetype(null)
            ->filterByFilename(null)
            ->filterByOriginalFilename(null)
            ->filterByFilestackFileInstanceId(null, \Propel\Runtime\ActiveQuery\Criteria::NOT_EQUAL)
            ->limit($params->limit);
        foreach ($filesQuery->find() as $file) {
            // Fill out the necessary metadata in the parent file record
            $fileInstance = $file->getFileInstanceRelatedByFilestackFileInstanceId();
            File::setFileMetadataFromFilestackFileInstanceMetadata($destinationFile, $fileInstance);
        }

    }

    static public function setFileMetadataWhereMissing(stdClass $params)
    {
        Suggestions::status(__METHOD__);

        if (empty($params->limit)) {
            $params->limit = 10;
        }
        $filesQuery = \propel\models\FileQuery::create()
            ->filterByFilestackFileInstanceId(null, \Propel\Runtime\ActiveQuery\Criteria::NOT_EQUAL)
            ->_or()
            ->filterByPublicFilesS3FileInstanceId(null, \Propel\Runtime\ActiveQuery\Criteria::NOT_EQUAL)
            ->limit($params->limit);
        foreach ($filesQuery->find() as $file) {
            $file->determineFileMetadata();
            $file->save();
        }

    }

}
