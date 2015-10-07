<?php
/**
 * The main application config
 */

namespace neam\bootstrap;

// parse JWT for bootstrap-level tenant-specific-config (auth0)
require_once("$project_root/config/auth0-jwt-bootstrap.php");

// ==== DNA Revision ====

Config::expect("YII_DNA_REVISION", $default = "dna-rev-1", $required = false);

// ==== Metadata that determines overall app configuration ====

Config::expect("APPNAME", $default = null, $required = true); // Always set in deployments so that they can be aware of what deployment this is
Config::expect("APPVHOST", $default = null, $required = true); // Always set in deployments so that they can be aware of what deployment this is
Config::expect("ENV", $default = null, $required = true);
Config::expect("CONFIG_ENVIRONMENT", $default = 'production', $required = false); // Used in main-local.php and then in index.php to decide which env-*.php configuration file to include
Config::expect("DATA", $default = null, $required = (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'OPTIONS'));

// ==== Identity-related config ====

Config::expect("BRAND_HOME_URL", $default = null, $required = true);
Config::expect("SITENAME", $default = null, $required = true);
Config::expect("SUPPORT_EMAIL", $default = null, $required = true);
Config::expect("MAIL_SENDER_EMAIL", $default = null, $required = true);
Config::expect("MAIL_SENDER_NAME", $default = null, $required = true);

// ==== Restricted access ====

Config::expect("RESTRICTED_ACCESS_ROOT_GROUP", $default = "root", $required = false);

// ==== Defines infrastructure = all backing services, usernames, api:s, servers, ports etc depending on environment ====

// DB
Config::expect('DATABASE_USER', $default = null, $required = true);
Config::expect('DATABASE_PASSWORD', $default = null, $required = true);
Config::expect('DATABASE_HOST', $default = null, $required = true);
Config::expect('DATABASE_PORT', $default = null, $required = true);
Config::expect('DATABASE_NAME', $default = null, $required = true);
Config::expect('DATABASE_ROOT_USER', $default = null, $required = false);
Config::expect('DATABASE_ROOT_PASSWORD', $default = null, $required = false);

// Mailcatcher is used as SMTP while running tests
Config::expect("MAILCATCHER_HOST", $default = "mailcatcher", $required = false);
Config::expect("MAILCATCHER_HTTP_PORT", $default = "1080", $required = false);
Config::expect("MAILCATCHER_SMTP_PORT", $default = "1025", $required = false);

// Require setting smtp constants based on SMTP_URL environment variable
Config::expect("SMTP_HOST", $default = null, $required = true);
Config::expect("SMTP_PORT", $default = null, $required = true);
Config::expect("SMTP_USERNAME", $default = null, $required = true);
Config::expect("SMTP_PASSWORD", $default = null, $required = true);
Config::expect("SMTP_ENCRYPTION", $default = null, $required = true);

// Sentry is used to report errors remotely
Config::expect("SENTRY_DSN", $default = null, $required = true);

// Filepicker is used to handle user uploads
Config::expect("FILEPICKER_API_KEY", $default = null, $required = true);

// Auth0
Config::expect("AUTH0_APPS", $default = null, $required = true);
Config::expect("AUTH0_CLIENT_IDS", $default = null, $required = true);
Config::expect("AUTH0_CLIENT_SECRETS", $default = null, $required = true);
Config::expect("CORS_ACL_ORIGIN_HOSTS", $default = null, $required = true);

// To be able to reset to a database with user generated data as described in the README

Config::expect("USER_DATA_BACKUP_UPLOADERS_ACCESS_KEY", $default = null, $required = Config::read("DATA") !== "clean-db");
Config::expect("USER_DATA_BACKUP_UPLOADERS_SECRET", $default = null, $required = Config::read("DATA") !== "clean-db");
Config::expect("USER_GENERATED_DATA_S3_BUCKET", $default = null, $required = Config::read("DATA") !== "clean-db");
Config::expect("WEB_SERVER_POSIX_USER", $default = null, $required = true);
Config::expect("WEB_SERVER_POSIX_GROUP", $default = "", $required = false);

// To be able to sync files to s3 and not have to serve them from the compute cluster

Config::expect("PUBLIC_FILES_S3_BUCKET", $default = null, $required = true);
Config::expect("PUBLIC_FILES_S3_REGION", $default = null, $required = true);
Config::expect("PUBLIC_FILES_S3_PATH", $default = null, $required = true);
Config::expect("PUBLIC_FILE_UPLOADERS_ACCESS_KEY", $default = null, $required = true);
Config::expect("PUBLIC_FILE_UPLOADERS_SECRET", $default = null, $required = true);
Config::expect("CDN_PATH_HTTP", $default = null, $required = true);
Config::expect("CDN_PATH_HTTPS", $default = null, $required = true);

// ==== Misc ====

Config::expect("YII_GII_PASSWORD", $default = uniqid(), $required = false);
Config::expect("LOCAL_SERVICES_IP");

// ==== Debug-related config ====

Config::expect("DEV", $default = false);
Config::expect("YII_DEBUG", $default = false);
Config::expect("YII_TRACE_LEVEL", $default = 0);
Config::expect("DEBUG_REDIRECTS", $default = false);
Config::expect("DEBUG_LOGS", $default = false);
Config::expect("LOCAL_OFFLINE_DATA", $default = null);
Config::expect("YII2_ENABLE_ERROR_HANDLER", $default = false);

// ==== Runtime config ====

/*
 * Some config is altered on a per-request basis:
 * 1. We use db_%DATA% as the database name
 * 2. We use a DATABASE_NAME-based hash as the database user
 *
 * This makes it feasible to access DATA-specific databases.
 * An outside routine/script is responsible for creating the corresponding users and databases.
 */
$_ENV['DATABASE_NAME'] = 'db_' . str_replace("-", "_", Config::read("DATA"));
$_ENV['DATABASE_USER'] = substr(md5($_ENV['DATABASE_NAME']), 0, 16);
