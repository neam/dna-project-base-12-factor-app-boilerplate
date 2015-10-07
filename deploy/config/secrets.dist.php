<?php

namespace neam\bootstrap;

use Exception;

// Optionally include a identity file containing identity-related deployment defaults

$path = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'identity.php';
if (is_readable($path)) {
    require($path);
}

// Non-versioned secrets

$_ENV["SAUCE_ACCESS_KEY"] = "";
$_ENV["SAUCE_USERNAME"] = "";

$_ENV["USER_DATA_BACKUP_UPLOADERS_ACCESS_KEY"] = "";
$_ENV["USER_DATA_BACKUP_UPLOADERS_SECRET"] = "";
$_ENV["PUBLIC_FILE_UPLOADERS_ACCESS_KEY"] = "";
$_ENV["PUBLIC_FILE_UPLOADERS_SECRET"] = "";
$_ENV["PUBLIC_FILES_S3_REGION"] = "us-standard";
$_ENV["PUBLIC_FILES_S3_PATH"] = "/m/" . Config::read("APPVHOST") . "/";
$_ENV["CDN_PATH_HTTP"] = "http://static._PROJECT_.com" . $_ENV["PUBLIC_FILES_S3_PATH"];
$_ENV["CDN_PATH_HTTPS"] = "https://static._PROJECT_.com" . $_ENV["PUBLIC_FILES_S3_PATH"];

$_ENV["COMPOSER_GITHUB_TOKEN"] = "";
$_ENV["TUTUM_USER"] = "";
$_ENV["TUTUM_EMAIL"] = "";
$_ENV["TUTUM_PASSWORD"] = "";
$_ENV["TUTUM_APIKEY"] = "";
$_ENV["NEW_RELIC_LICENSE_KEY"] = "";

$_ENV["DEVELOPMENT_SMTP_HOST"] = "mailcatcher";
$_ENV["DEVELOPMENT_SMTP_PORT"] = "25";
$_ENV["DEVELOPMENT_SMTP_USERNAME"] = "foo";
$_ENV["DEVELOPMENT_SMTP_PASSWORD"] = "bar";
$_ENV["DEVELOPMENT_SMTP_ENCRYPTION"] = "foo";

$_ENV["PRODUCTION_SMTP_HOST"] = "smtp.example.com";
$_ENV["PRODUCTION_SMTP_PORT"] = "587";
$_ENV["PRODUCTION_SMTP_USERNAME"] = "changeme";
$_ENV["PRODUCTION_SMTP_PASSWORD"] = "changeme";
$_ENV["PRODUCTION_SMTP_ENCRYPTION"] = "tls";

$_ENV["FILEPICKER_API_KEY"] = "";

$_ENV["AUTH0_APPS"] = "";
$_ENV["AUTH0_CLIENT_IDS"] = "";
$_ENV["AUTH0_CLIENT_SECRETS"] = "";
$_ENV["CORS_ACL_ORIGIN_HOSTS"] = "localhost:9000,app._PROJECT_.com,_PROJECT_.com";

$_ENV["DEVELOPMENT_GA_TRACKING_ID"] = "";
$_ENV["PRODUCTION_GA_TRACKING_ID"] = "";

$_ENV["SENTRY_DSN"] = "";

// Deployment-specifics
$_ENV['WEB_SERVER_POSIX_USER'] = "www-data";
$_ENV['WEB_SERVER_POSIX_GROUP'] = "www-data";

// Smtp url
if (Config::read("DEPLOY_STABILITY_TAG") === "prod") {
    $_ENV["SMTP_HOST"] = $_ENV["PRODUCTION_SMTP_HOST"];
    $_ENV["SMTP_PORT"] = $_ENV["PRODUCTION_SMTP_PORT"];
    $_ENV["SMTP_USERNAME"] = $_ENV["PRODUCTION_SMTP_USERNAME"];
    $_ENV["SMTP_PASSWORD"] = $_ENV["PRODUCTION_SMTP_PASSWORD"];
    $_ENV["SMTP_ENCRYPTION"] = $_ENV["PRODUCTION_SMTP_ENCRYPTION"];
    $_ENV["GA_TRACKING_ID"] = $_ENV["DEVELOPMENT_GA_TRACKING_ID"];
} else {
    $_ENV["SMTP_HOST"] = $_ENV["DEVELOPMENT_SMTP_HOST"];
    $_ENV["SMTP_PORT"] = $_ENV["DEVELOPMENT_SMTP_PORT"];
    $_ENV["SMTP_USERNAME"] = $_ENV["DEVELOPMENT_SMTP_USERNAME"];
    $_ENV["SMTP_PASSWORD"] = $_ENV["DEVELOPMENT_SMTP_PASSWORD"];
    $_ENV["SMTP_ENCRYPTION"] = $_ENV["DEVELOPMENT_SMTP_ENCRYPTION"];
    $_ENV["GA_TRACKING_ID"] = $_ENV["DEVELOPMENT_GA_TRACKING_ID"];
}

// Amazon RDS administration

$_ENV["DEV_RDS_HOST"] = "";
$_ENV["PROD_RDS_HOST"] = "";
$_ENV["DATABASE_ROOT_USER"] = "";
$_ENV["DATABASE_ROOT_PASSWORD"] = "";

// Amazon RDS app access details

$app = Config::read("APPVHOST");
switch ($app) {
    default:
        throw new Exception("Amazon RDS deploy database access credentials missing for app '{$app}'");
        $_ENV["DATABASE_HOST"] = "";
        $_ENV["DATABASE_PORT"] = "";
        $_ENV["DATABASE_PASSWORD"] = "";
        break;
    case "";
        // During prepare-step APPVHOST will be empty, which is fine, we don't need database credentials at that stage
        break;
}
