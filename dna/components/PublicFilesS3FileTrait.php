<?php

namespace neam\file_registry;

use Aws\S3\S3Client;
use League\Flysystem\AdapterInterface;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;
use Exception;

trait PublicFilesS3FileTrait
{

    protected $publicFilesS3Filesystem;

    /**
     * @propel
     * @return Filesystem
     */
    public function getPublicFilesS3Filesystem()
    {

        if (empty($this->publicFilesS3Filesystem)) {

            $client = S3Client::factory(
                [
                    'credentials' => [
                        'key' => PUBLIC_FILE_UPLOADERS_ACCESS_KEY,
                        'secret' => PUBLIC_FILE_UPLOADERS_SECRET,
                    ],
                    'region' => PUBLIC_FILES_S3_REGION,
                    'version' => 'latest',
                ]
            );

            $adapter = new AwsS3Adapter($client, str_replace("s3://", "", PUBLIC_FILES_S3_BUCKET), 'files/' . DATA);

            $filesystem = new Filesystem(
                $adapter, [
                    'visibility' => AdapterInterface::VISIBILITY_PUBLIC
                ]
            );

            $this->publicFilesS3Filesystem = $filesystem;

        }
        return $this->publicFilesS3Filesystem;

    }

    /**
     * @propel
     * @yii
     * @return string
     */
    public function getPublicFilesS3BaseUrl()
    {
        return str_replace("s3://", "//", PUBLIC_FILES_S3_BUCKET) . '/files/' . DATA . '/';
    }

    /**
     * Assumptions are:
     * 1. A CNAME is provided for the bucket which allows public access on the same domain as the bucket is named
     * 2. $uri is the relative path as specified in the database for this data profile
     * @param $uri
     * @return string
     */
    public function publicFilesS3Url($uri)
    {
        return $this->getPublicFilesS3BaseUrl() . str_replace('%2F', '/', rawurlencode($uri));
    }

    /**
     * Ensures:
     * 1. That the file-record have a remote public file instance
     * 2. That the remote public file instance actually has it's file in place
     * @propel
     * @param null $params
     */
    public function ensureRemotePublicFileInstance()
    {
        \Suggestions::status(__METHOD__);

        /** @var \propel\models\File $this */

        // Get the ensured remote public file instance with a binary copy of the file (binary copy is guaranteed to be found at this file instance's uri but not necessarily in the correct path)
        $remotePublicFileInstance = $this->getEnsuredRemotePublicFileInstance();

        // Move the remote public file instance to correct path if not already there
        $correctPath = $this->getCorrectPath();
        $this->moveTheRemotePublicFileInstanceToPathIfNotAlreadyThere($remotePublicFileInstance, $correctPath);

        // Dummy check
        if (!$this->checkIfCorrectRemotePublicFileIsInPath($correctPath)) {
            if ($this->getPublicFilesS3Filesystem()->has($correctPath)) {
                $metadata = $this->getPublicFilesS3Filesystem()->getMetadata($correctPath);
            } else {
                $metadata = ["not-in-path"];
            }

            throw new Exception(
                "ensureCorrectLocalFile() failure - remote public file instance's (id '{$remotePublicFileInstance->getId()}') file (id '{$this->getId()}', with expected size {$this->getSize()}) is not in path ('$correctPath') after an attempted move to correct that. Currently in path: "
                . print_r($metadata, true)
            );
        }

        // Set the correct path in file.path
        if ($this->getPath() !== $correctPath) {
            $this->setPath($correctPath);
        }

        // Save the file and file instance only first now when we know it is in place
        $remotePublicFileInstance->save();
        $this->save();

    }

    /**
     * @propel
     * @return mixed|null|\propel\models\FileInstance
     * @throws Exception
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function getEnsuredRemotePublicFileInstance()
    {
        \Suggestions::status(__METHOD__);

        /** @var \propel\models\File $this */
        $publicFilesS3Filesystem = $this->getPublicFilesS3Filesystem();

        $remotePublicFileInstance = $this->remotePublicFileInstance();

        // Create a public remote file instance since none exists - but do not save it until we have put the binary in place...
        if (empty($remotePublicFileInstance)) {
            $remotePublicFileInstance = new \propel\models\FileInstance();
            $remotePublicFileInstance->setStorageComponentRef('public-files-s3');
            $remotePublicFileInstance->setFileRelatedByFileId($this); // <-- TODO: Remove this column
            $this->setFileInstanceRelatedByPublicFilesS3FileInstanceId($remotePublicFileInstance);
        }

        // Upload the file
        $path = $remotePublicFileInstance->getUri();
        if (empty($path)) {
            $path = $this->getCorrectPath();
            $remotePublicFileInstance->setUri($path);
        }
        if (!$this->checkIfCorrectRemotePublicFileIsInPath($path)) {

            $localFileInstance = $this->getEnsuredLocalFileInstance();
            if (empty($localFileInstance)) {
                throw new Exception("No local file instance available to upload the file from");
            }

            // Remove any existing incorrect file in the location
            try {
                $this->getPublicFilesS3Filesystem()->delete($path);
            } catch (FileNotFoundException $e) {
            }

            // Upload to specified path
            $localFilesystem = $this->getLocalFilesystem();
            $publicFilesS3Filesystem->writeStream(
                $path,
                $localFilesystem->readStream($localFileInstance->getUri())
            );

            // Update file instance to reflect the path to where it is currently found
            $remotePublicFileInstance->setUri($path);

        }

        return $remotePublicFileInstance;

    }

    /**
     * @param \propel\models\FileInstance $fileInstance
     * @param $path
     */
    protected function moveTheRemotePublicFileInstanceToPathIfNotAlreadyThere(
        \propel\models\FileInstance $fileInstance,
        $path
    ) {
        \Suggestions::status(__METHOD__);

        /** @var \propel\models\File $this */
        if ($fileInstance->getUri() !== $path) {
            if (!$this->checkIfCorrectRemotePublicFileIsInPath($path)) {
                // Remove any existing incorrect file in the location
                try {
                    $this->getPublicFilesS3Filesystem()->delete($path);
                } catch (FileNotFoundException $e) {
                }
                $this->getPublicFilesS3Filesystem()->rename($fileInstance->getUri(), $path);
                $fileInstance->setUri($path);
            }
        }

    }

    /**
     * @propel
     * @return bool
     * @throws Exception
     */
    protected function checkIfCorrectRemotePublicFileIsInPath($path)
    {
        \Suggestions::status(__METHOD__);
        \Suggestions::status($path);

        // Check if file exists
        $exists = $this->getPublicFilesS3Filesystem()->has($path);
        if (!$exists) {
            //\Suggestions::status("Does not exist");
            return false;
        }

        /** @var \propel\models\File $this */

        if ($this->getSize() === null) {
            throw new Exception(
                "A file already exists in the path ('{$path}') but we can't compare it to the expected file size since it is missing from the file record ('{$this->getId()}') metadata"
            );
        }

        // Check if existing file has the correct size
        $size = $this->getPublicFilesS3Filesystem()->getSize($path);
        if ($size !== $this->getSize()) {
            //\Suggestions::status("Wrong size (expected: {$this->getSize()}, actual: $size)");
            return false;
        }

        // Check hash/contents to verify that the file is the same
        // TODO

        //\Suggestions::status("Correct remote public file is in path");
        return true;

    }

}