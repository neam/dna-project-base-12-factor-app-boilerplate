<?php

namespace neam\file_registry;

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local as Adapter;

/**
 * Helper to ensure eventual consistency of files across
 * local filesystem and s3, with optional
 * Class FileHandler
 */
trait FileTrait
{

    static public function downloadRemoteFileToPath($url, $destination)
    {
        $targetFileHandle = fopen($destination, 'w');
        return static::downloadRemoteFile($url, $targetFileHandle);
    }

    static public function downloadRemoteFile($url, $targetFileHandle)
    {
        $BUFSIZ = 4095;
        $rfile = fopen($url, 'r');
        $lfile = $targetFileHandle;
        while (!feof($rfile)) {
            fwrite($lfile, fread($rfile, $BUFSIZ), $BUFSIZ);
        }
        fclose($rfile);
        fclose($lfile);
        return $lfile;
    }

    public function ensureLocalFile()
    {

        // kolla befintliga file instances
        // plocka ut local

        $file = tmpfile();
        FileHandler::downloadRemoteFile($file_url, $file);

    }

    public function saveMetadata()
    {

        //original_name
        //mime_type

        $localFileHandle = $this->getLocalFileHandle();

        $filePath = str_replace(Yii::getPathOfAlias($this->module->dataAlias) . DIRECTORY_SEPARATOR, "", $fullFilePath);

        $md5 = md5_file($fullFilePath);
        $getimagesize = getimagesize($fullFilePath);

        //$model->_label = $fileName;
        $this->original_name = $fileName;

        $this->path = $filePath;
        $this->hash = $md5;

        if (function_exists("mime_content_type")) {
            $mime = mime_content_type($fullFilePath);
        } else {
            if (function_exists("finfo_open")) {
                $finfo = finfo_open($fullFilePath);
                $m = finfo_file($finfo, $filename);
                finfo_close($finfo);
            } else {
                $mime = $getimagesize['mime'];
            }
        }
        $this->mime_type = $mime;
        $this->info_php_json = CJSON::encode(getimagesize($fullFilePath));
        $this->size = filesize($fullFilePath);

        if (!$this->save()) {
            throw new SaveException($this);
        }

    }

    public function getLocalFileHandle()
    {
        return $flysystemfoo->bar();
    }

    /*
    public $dataPath;

    public function __construct($dataPath)
    {

        $localFilesystem = new Filesystem(new Adapter(LOCAL_USER_FILES_PATH));

    }

    /**
     * Action for importing a file available on a public uri
     *
     * @param $root relative base folder
     * @return array|mixed
     * @throws CException
     * /
    public function actionFilepickerUrl()
    {

        $response = new stdClass();
        $response->message = "File";

        if ($_POST['url']) {

            $fileName = $_POST['filename'];

            // ANVÄND TMP-PATH HÄR
            $dataFilePath = $this->module->getDataPath() . DIRECTORY_SEPARATOR . $fileName;

            if ($this->downloadRemoteFile($_POST['url'], $dataFilePath)) {

                /* @var P3Media $model * /
                $model = $this->createMedia($fileName, $dataFilePath);

                if ($model) {
                    $response->file = array(
                        'url' => $model->createUrl("original-public", true),
                        'media' => $model->attributes,
                    );
                    header("HTTP/1.1 200 OK");
                } else {
                    header("HTTP/1.1 500 Database record could not be saved.");
                }

            } else {
                header("HTTP/1.1 500 File could not be saved.");
            }

        } else {
            header("HTTP/1.1 500 No url to download locally.");
        }

        echo CJSON::encode($response);
        exit;

    }

    protected function createMedia($fileName, $fullFilePath)
    {
        $filePath = str_replace(Yii::getPathOfAlias($this->module->dataAlias) . DIRECTORY_SEPARATOR, "", $fullFilePath);

        $md5 = md5_file($fullFilePath);
        $getimagesize = getimagesize($fullFilePath);

        $model = new P3Media;
        $model->detachBehavior('Upload');

        $model->default_title = $fileName;
        $model->original_name = $fileName;

        $model->type = P3Media::TYPE_FILE;
        $model->path = $filePath;
        $model->hash = $md5;

        $model->access_domain = '*';

        if (function_exists("mime_content_type")) {
            $mime = mime_content_type($fullFilePath);
        } else {
            if (function_exists("finfo_open")) {
                $finfo = finfo_open($fullFilePath);
                $m = finfo_file($finfo, $filename);
                finfo_close($finfo);
            } else {
                $mime = $getimagesize['mime'];
            }
        }
        $model->mime_type = $mime;
        $model->info_php_json = CJSON::encode(getimagesize($fullFilePath));
        $model->size = filesize($fullFilePath);

        if ($model->save()) {
            return $model;
        } else {
            $errorMessage = "";
            foreach ($model->errors AS $attrErrors) {
                $errorMessage .= implode(',', $attrErrors);
            }
            throw new CHttpException(500, $errorMessage);
        }
    }

    */
}