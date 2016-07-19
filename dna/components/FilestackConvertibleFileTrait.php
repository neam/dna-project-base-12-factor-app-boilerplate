<?php

namespace neam\file_registry;

use GuzzleHttp;
use propel\models\File;
use Exception;
use propel\models\FileInstance;

/**
 * Holds methods relevant for converting files using the filestack api
 *
 * Class FilestackConvertibleFileTrait
 * @package neam\file_registry
 */
trait FilestackConvertibleFileTrait
{

    /**
     * Return the n
     */

    /**
     *
     * @return string
     */
    static public function convertDynamicallyCroppedImageToOrdinaryImage()
    {

        return 'foo';

    }

    public function getFilestackPendingFileInstanceConvertMetadata()
    {
        /** @var \propel\models\File $this */
        if ($fileInstance = $this->getFileInstanceRelatedByFilestackPendingFileInstanceId()) {
            $data = GuzzleHttp\Utils::jsonDecode($fileInstance->getDataJson());
            if (isset($data->convertMetadata)) {
                return $data->convertMetadata;
            }
        }
        return null;
    }

    public function setFilestackPendingFileInstanceConvertMetadata($convertConfig = null, $convertStatus = null)
    {
        /** @var \propel\models\File $this */
        if (!($fileInstance = $this->getFileInstanceRelatedByFilestackPendingFileInstanceId())) {
            $fileInstance = new FileInstance();
            $fileInstance->setStorageComponentRef('filestack-pending');
            $this->setFileInstanceRelatedByFilestackPendingFileInstanceId($fileInstance);
        }
        $data = GuzzleHttp\Utils::jsonDecode($fileInstance->getDataJson());
        if (empty($data)) {
            $data = new \stdClass();
        }
        $convertMetadata = new \stdClass();
        $convertMetadata->convertConfig = $convertConfig;
        $convertMetadata->convertStatus = $convertStatus;
        if ($convertStatus && isset($convertStatus->timestamp)) {
            $dateTime = new \DateTime();
            $dateTime->setTimestamp($convertStatus->timestamp);
            $convertMetadata->formattedConvertStatusTimestamp = $dateTime->format("Y-m-d H:i:s");
        }
        $data->convertMetadata = $convertMetadata;
        $fileInstance->setDataJson(json_encode($data));
    }

    /**
     * @param File $sourceFile
     * @param File $destinationFile
     * @param $convertConfigJson
     * @throws FilestackMovieConversionAttemptException
     */
    static public function softAttemptFilestackMovieConversion(
        \propel\models\File $sourceFile = null,
        \propel\models\File $destinationFile = null,
        $convertConfigJson = null,
        $filestackConversionResultAttributeToPromote = null
    ) {

        $exception = null;

        try {
            $convertConfig = GuzzleHttp\Utils::jsonDecode($convertConfigJson);
            File::attemptFilestackMovieConversion($sourceFile, $destinationFile, $convertConfig, $filestackConversionResultAttributeToPromote);
        } catch (FilestackMovieConversionAttemptException $e) {
            $exception = $e;
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            $exception = $e;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $exception = $e;
        } catch (\InvalidArgumentException $e) {
            $exception = $e;
        }

        if (!empty($exception)) {

            // Save the exception-status in the destination file record (if exists)
            if (!empty($destinationFile)) {
                $convertStatus = new \stdClass();
                $convertStatus->status = "exception";
                $convertStatus->message = $exception->getMessage();
                $destinationFile->setFilestackPendingFileInstanceConvertMetadata($convertConfig, $convertStatus);
                $destinationFile->save();
            }

        }

    }


    /**
     * @param File $sourceFile
     * @param File $destinationFile
     * @param $convertConfig
     * @throws FilestackMovieConversionAttemptException
     */
    static public function attemptFilestackMovieConversion(
        \propel\models\File $sourceFile = null,
        \propel\models\File $destinationFile = null,
        $convertConfig = null,
        $filestackConversionResultAttributeToPromote = null
    ) {

        // Check if conversion can be initiated
        if (empty($sourceFile)) {
            throw new FilestackMovieConversionAttemptException("Empty source file");
        }
        if (empty($convertConfig)) {
            throw new FilestackMovieConversionAttemptException("Empty convert config");
        }
        if (empty($destinationFile)) {
            throw new FilestackMovieConversionAttemptException("Empty destination file");
        }
        if (empty($filestackConversionResultAttributeToPromote)) {
            throw new FilestackMovieConversionAttemptException("Empty conversion result attribute to promote");
        }
        if (!$fileInstance = $sourceFile->getFileInstanceRelatedByFilestackFileInstanceId()) {
            throw new FilestackMovieConversionAttemptException("No file stack file instance");
        }
        $sourceFilestackUrl = $fileInstance->getUri();
        if (empty($sourceFilestackUrl)) {
            throw new FilestackMovieConversionAttemptException("Empty file stack file instance uri");
        }

        // Do not attempt conversion if there is already a finished and promoted
        // conversion job for the same convert config = job already done and no need to restart it
        $pendingConvertMetadata = $destinationFile->getFilestackPendingFileInstanceConvertMetadata();
        if (!empty($pendingConvertMetadata)
            && !empty($pendingConvertMetadata->convertConfig)
            && (json_encode($pendingConvertMetadata->convertConfig) === json_encode($convertConfig))
            && ($pendingConvertMetadata->convertStatus->status === 'promoted')
        ) {
            // Do nothing
            return;
        }

        $handle = File::extractHandleFromFilestackUrl($sourceFilestackUrl);
        $convertStatus = File::filestackVideoConvertRequest($handle, $convertConfig);
        //$convertStatus = File::filestackVideoConvertRequestMock($handle, $convertConfig);

        // Save the convert metadata in the pending file instance data
        $destinationFile->setFilestackPendingFileInstanceConvertMetadata($convertConfig, $convertStatus);
        $destinationFile->save();

        static::promoteCompletedFilestackConvertion($destinationFile, $filestackConversionResultAttributeToPromote);

    }

    /**
     * @return mixed
     */
    static public function filestackVideoConvertRequest($handle, $convertConfig)
    {

        // Assert arguments
        $convertConfigDecoded = $convertConfig;
        if (empty($convertConfigDecoded)) {
            throw new Exception("filestackVideoConvertRequest() requires a non-empty convertConfig");
        }

        // Sign request with an admin policy
        $securityParams = new \stdClass();
        $securityParams->policy = File::filestackHandleAdminPolicy($handle);
        $securityParams->signature = File::filestackSignature($securityParams->policy);

        // Compile request url parameters
        $video_convert_param = static::filestackBuildRequestParam($convertConfigDecoded);
        $security_param = static::filestackBuildRequestParam($securityParams);

        // Build request url
        // From Filestack support: The policy and signature are added to the request as part of the security task. So your request should be something like this: curl -X GET "https://process.filestackapi.com/AKw4FdqUITxOs472rjZhpz/video_convert=preset:h264.hi,extname:.mp4/security=policy:eyJoYW5kbGUiOiAiT2dJNDdGTHFRUzJwNTRPcmQ4c04iLCJjYWxsIjpbInBpY2siLCJyZWFkIiwic3RhdCIsIndyaXRlIiwid3JpdGVVcmwiLCJzdG9yZSIsImNvbnZlcnQiLCJyZW1vdmUiXSwiZXhwaXJ5IjoxNDUyODU5NjQ2fQ%3D%3D,signature:f7ec33869c239dbe24154f91b9b20c13f5c4a0990377d0aebfc40bd712325c69/OgI47FLqQS2p54Ord8sN"
        $filestackRequestUrl = 'https://process.filestackapi.com/' . FILESTACK_API_KEY . '/video_convert=' . $video_convert_param . '/security=' . $security_param . '/' . $handle;

        // Perform request with a short timeout
        $client = new GuzzleHttp\Client();
        $response = $client->get($filestackRequestUrl, ['connect_timeout' => 2]);
        return GuzzleHttp\Utils::jsonDecode($response->getBody());

    }

    static public function filestackBuildRequestParam($params)
    {

        return str_replace("=", ":", http_build_query($params, '', ','));

    }

    static public function filestackVideoConvertRequestMock($handle, $convertConfig)
    {

        return json_decode(
            '{
  "data":{},
  "status":"started",
  "timestamp":"1450730720",
  "uuid":"638311d89d2bc849563a674a45809b7c"
}'
        );

    }

    /**
     * A completed filestack conversion should when encountered trigger a promotion of the converted video file instance into the file' main filestack file instance
     *
     * @param \propel\models\File $destinationFile
     * @throws Exception
     */
    static public function promoteCompletedFilestackConvertion(\propel\models\File $destinationFile, $filestackConversionResultAttributeToPromote)
    {

        $pendingConvertMetadata = $destinationFile->getFilestackPendingFileInstanceConvertMetadata();

        if ($pendingConvertMetadata
            && $pendingConvertMetadata->convertStatus
            && ($pendingConvertMetadata->convertStatus->status === 'completed')
            && (!empty($pendingConvertMetadata->convertStatus->data))
            && (!empty($pendingConvertMetadata->convertStatus->data->$filestackConversionResultAttributeToPromote))
        ) {

            // Mark pending file instance as promoted
            $pendingConvertMetadata->convertStatus->status = 'promoted';
            $destinationFile->setFilestackPendingFileInstanceConvertMetadata(
                $pendingConvertMetadata->convertConfig,
                $pendingConvertMetadata->convertStatus
            );

            // Create and set the main filestack file instance based on the converted media's filestack url
            $filestackUrl = $pendingConvertMetadata->convertStatus->data->$filestackConversionResultAttributeToPromote;
            $fileInstance = File::createFileInstanceWithMetadataByFilestackUrl($filestackUrl);
            $destinationFile->setFileInstanceRelatedByFilestackFileInstanceId($fileInstance);

            // Save file and it's file instances
            $destinationFile->save();

        }

    }

}

class FilestackMovieConversionAttemptException extends Exception
{
}