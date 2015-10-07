<?php
/**
 * Resource-manager specific config
 */
$config['import'][] = 'vendor.2amigos.resource-manager.*';
$config['components']['publicFilesResourceManager'] = array(
    'class' => 'EAmazonS3ResourceManager',
    'key' => PUBLIC_FILE_UPLOADERS_ACCESS_KEY,
    'secret' => PUBLIC_FILE_UPLOADERS_SECRET,
    'bucket' => str_replace("s3://", "", PUBLIC_FILES_S3_BUCKET),
    'region' => PUBLIC_FILES_S3_REGION,
);
