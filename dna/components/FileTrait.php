<?php

namespace neam\file_registry;

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
use Exception;

/**
 * Helper trait that encapsulates DNA project base file-handling logic
 *
 * Some principles:
 *  §1 Local file manipulation should be available simply by reading LOCAL_USER_FILES_PATH . DIRECTORY_SEPARATOR . $file->getPath() as defined in getLocalAbsolutePath()
 *  §2 The path to the file is relative to the storage component's file system and should follow the format $file->getId() . DIRECTORY_SEPARATOR . $file->getFilename() - this is the file's "correct path" and ensures that multiple files with the same filename can be written to all file systems
 *  §3 Running $file->ensureLocalFileInCorrectPath() ensures §1 and §2 (designed to run before local file manipulation, post file creation/modification time and/or as a scheduled process)
 *  §4 File instance records tell us where binary copies of the file are stored
 *  §5 File instances should (if possible) store it's binary copy using the relative path provided by $file->getPath(), so that retrieval of the file's binary contents is straightforward and eventual public url's follow the official path/name supplied by $file->getPath()
 *
 * Class FileTrait
 */
trait FileTrait
{

    static public function downloadRemoteFileToPath($url, $destination)
    {
        $targetFileHandle = fopen($destination, 'w');
        static::downloadRemoteFile($url, $targetFileHandle);
        fclose($targetFileHandle);
        return $targetFileHandle;
    }

    static public function downloadRemoteFileToStream($url, $targetFileHandle)
    {
        $BUFSIZ = 4095;
        $rfile = fopen($url, 'r');
        if (!$rfile) {
            throw new Exception("Failed to open file handle against $url");
        }
        $lfile = $targetFileHandle;
        while (!feof($rfile)) {
            fwrite($lfile, fread($rfile, $BUFSIZ), $BUFSIZ);
        }
        fclose($rfile);
        return $lfile;
    }

    protected $localFilesystem;

    protected function getLocalFilesystem()
    {
        if (empty($this->localFilesystem)) {
            $this->localFilesystem = new Filesystem(new Local($this->getLocalBasePath()));
        }
        return $this->localFilesystem;
    }

    public function getLocalBasePath()
    {
        /** @var \propel\models\File $this */
        return rtrim(LOCAL_USER_FILES_PATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }

    public function getPathForManipulation($ensure = true)
    {
        /** @var \propel\models\File $this */
        if ($ensure) {
            $this->ensureLocalFileInCorrectPath();
        }
        if (empty($this->getPath())) {
            throw new Exception("File's path not set");
        }
        return $this->getPath();
    }

    public function getAbsolutePathForManipulation($ensure = true)
    {
        /** @var \propel\models\File $this */
        return $this->getLocalBasePath() . $this->getPathForManipulation($ensure);
    }

    protected function getCorrectPath()
    {
        /** @var \propel\models\File $this */
        $id = $this->getId();
        if (empty($id)) {
            throw new Exception("File's id not set - can't calculate the correct path");
        }
        $filename = $this->getFilename();
        if (empty($filename)) {
            throw new Exception("File's filename not set - can't calculate the correct path");
        }
        return $this->getId() . DIRECTORY_SEPARATOR . $this->getFilename();
    }

    /**
     * Ensures:
     * 1. That the file-record have a local file instance
     * 2. That the local file instance actually has it's file in place locally
     * @param null $params
     */
    public function ensureLocalFileInCorrectPath()
    {

        /** @var \propel\models\File $this */

        $correctPath = $this->getCorrectPath();

        // Get the ensured local file instance with a binary copy of the file
        $localFileInstance = $this->getEnsuredLocalFileInstance();

        // Move the local file instance to correct path if not already there
        if ($localFileInstance->getUri() !== $correctPath) {
            if (!$this->checkIfLocalFileIsInCorrectPath()) {
                $this->getLocalFilesystem()->rename($localFileInstance->getUri(), $correctPath);
            }
            $localFileInstance->setUri($correctPath);
            $localFileInstance->save();
        }

        // Dummy check
        if (!$this->checkIfLocalFileIsInCorrectPath()) {
            throw new Exception("ensureLocalFileInCorrectPath() failure - local file is still not in correct path");
        }

        // Save the correct path in file.path
        if ($this->getPath() !== $correctPath) {
            $this->setPath($correctPath);
            $this->save();
        }

    }

    protected function checkIfLocalFileIsInCorrectPath()
    {

        /** @var \propel\models\File $this */

        $correctPath = $this->getCorrectPath();

        // Check if file exists
        $exists = $this->getLocalFilesystem()->has($correctPath);
        if (!$exists) {
            return false;
        }

        // Check if existing file has the correct size
        $size = $this->getLocalFilesystem()->getSize($correctPath);
        if ($size !== $this->getSize()) {
            return false;
        }

        // Check hash/contents to verify that the file is the same
        // TODO

        return true;

    }

    protected function getEnsuredLocalFileInstance()
    {

        /** @var \propel\models\File $this */
        $localFileInstance = $this->localFileInstance();
        if (!empty($localFileInstance)) {
            return $localFileInstance;
        }

        $remoteFileInstance = $this->remoteFileInstance();
        if (empty($remoteFileInstance)) {
            throw new Exception("No file instance available to get a binary copy of the file from");
        }

        // Download the file
        if (!$this->checkIfLocalFileIsInCorrectPath()) {
            $publicUrl = $this->fileInstanceAbsoluteUrl($remoteFileInstance);
            $tmpStream = tmpfile();
            $this->downloadRemoteFileToStream($publicUrl, $tmpStream);
            $correctPath = $this->getCorrectPath();
            $this->getLocalFilesystem()->writeStream($correctPath, $tmpStream);
        }

        // Create a local file instance since none exists
        $correctPath = $this->getCorrectPath();
        $localFileInstance = new \propel\models\FileInstance();
        $localFileInstance->setUri($correctPath);
        $localFileInstance->setStorageComponentRef('local');
        $localFileInstance->setFile($this);
        $localFileInstance->save();

        return $localFileInstance;

    }

    public function localFileInstance()
    {
        /** @var \propel\models\File $this */
        $fileInstances = $this->getFileInstances();
        foreach ($fileInstances as $fileInstance) {
            if ($fileInstance->getStorageComponentRef() === 'local') {
                return $fileInstance;
            }
        }
        return null;
    }

    public function remoteFileInstance()
    {
        /** @var \propel\models\File $this */
        $fileInstances = $this->getFileInstances();
        foreach ($fileInstances as $fileInstance) {
            if ($fileInstance->getStorageComponentRef() === 'filepicker') {
                return $fileInstance;
            }
        }
        return null;
    }

    public function ensureFileMetadata()
    {
        /** @var \propel\models\File $this */

        $localFileInstance = $this->getEnsuredLocalFileInstance();
        $localPath = $localFileInstance->getUri();
        $absoluteLocalPath = $this->getLocalBasePath() . $localPath;

        if (empty($this->getMimetype())) {
            $this->setMimetype($this->getLocalFilesystem()->getMimetype($localPath));
        }
        if ($this->getSize() === null) {
            $this->setMimetype($this->getLocalFilesystem()->getSize($localPath));
        }
        if (empty($this->getFilename())) {
            $filename = pathinfo($absoluteLocalPath, PATHINFO_FILENAME);
            $this->setFilename($filename);
        }
        // TODO: hash/checksum
        // $md5 = md5_file($absoluteLocalPath);
        // Possible TODO: image width/height if image
        // getimagesize($absoluteLocalPath)

        $this->save();

    }

    /**
     * Return the first available absolut url to an instance of the current file
     * @return string
     */
    public function absoluteUrl()
    {
        if (get_class($this) === 'File') {
            return $this->absoluteUrl_yii($this);
        } else {
            return $this->absoluteUrl_propel($this);
        }
    }

    protected function absoluteUrl_yii(\File $file)
    {
        $fileInstances = $file->fileInstances;
        /** @var \FileInstance $fileInstance */
        foreach ($fileInstances as $fileInstance) {
            if ($fileInstance->storage_component_ref === 'local') {
                // Local files are assumed published to a CDN
                return CDN_PATH . 'media/' . $file->path;
            } elseif ($fileInstance->storage_component_ref === 'filepicker') {
                // Use Filepicker's CDN
                return str_replace("www.filepicker.io", "cdn.filepicker.io", $fileInstance->uri);
            }
        }
        return null;
    }

    protected function absoluteUrl_propel(\propel\models\File $file)
    {
        $fileInstances = $file->getFileInstances();
        foreach ($fileInstances as $fileInstance) {
            if ($absoluteUrl = $this->fileInstanceAbsoluteUrl($fileInstance)) {
                return $absoluteUrl;
            }
        }
        return null;
    }

    protected function fileInstanceAbsoluteUrl(\propel\models\FileInstance $fileInstance)
    {

        /** @var \propel\models\File $this */
        if ($fileInstance->getStorageComponentRef() === 'local') {
            // Local files are assumed published to a CDN
            return CDN_PATH . 'media/' . $this->getPath();
        } elseif ($fileInstance->getStorageComponentRef() === 'filepicker') {
            // Use Filepicker's CDN
            return str_replace("www.filepicker.io", "cdn.filepicker.io", $fileInstance->getUri());
        }

    }

}