<?php

namespace neam\bootstrap;

use Exception;

// Non-versioned secrets that are only required in the deploy prepare step

$_ENV["DOCKERCLOUD_USER"] = "";
$_ENV["DOCKERCLOUD_EMAIL"] = "";
$_ENV["DOCKERCLOUD_PASSWORD"] = "";
$_ENV["DOCKERCLOUD_APIKEY"] = "";

// Non-versioned secrets that depend on production stability level

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

$_ENV["DEVELOPMENT_GA_TRACKING_ID"] = "";
$_ENV["PRODUCTION_GA_TRACKING_ID"] = "";

$_ENV["DEV_DATABASE_HOST"] = "";
$_ENV["DEV_DATABASE_PORT"] = "3306";
$_ENV["DEV_DATABASE_PASSWORD"] = "";
$_ENV["DEV_DATABASE_ROOT_USER"] = "master";
$_ENV["DEV_DATABASE_ROOT_PASSWORD"] = "";
$_ENV["DEMO_DATABASE_HOST"] = "";
$_ENV["DEMO_DATABASE_PORT"] = "3306";
$_ENV["DEMO_DATABASE_PASSWORD"] = "";
$_ENV["DEMO_DATABASE_ROOT_USER"] = "master";
$_ENV["DEMO_DATABASE_ROOT_PASSWORD"] = "";
$_ENV["PROD_DATABASE_HOST"] = "";
$_ENV["PROD_DATABASE_PORT"] = "3306";
$_ENV["PROD_DATABASE_PASSWORD"] = "";
$_ENV["PROD_DATABASE_ROOT_USER"] = "master";
$_ENV["PROD_DATABASE_ROOT_PASSWORD"] = "";
