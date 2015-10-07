<?php

namespace neam\bootstrap;

// Identity-related deployment defaults

switch (Config::read('TOPLEVEL_DOMAIN')) {

    case "_PROJECT_dev.com":
        $_ENV['BRAND_HOME_URL'] = "http://_PROJECT_.com";
        $_ENV['SITENAME'] = "DEV-_PROJECT_";
        $_ENV['SUPPORT_EMAIL'] = "dev+dev-deployment@_PROJECT_.com";
        $_ENV['MAIL_SENDER_EMAIL'] = "info+dev-deployment@_PROJECT_.com";
        $_ENV['MAIL_SENDER_NAME'] = "DEV-_PROJECT_";
        break;

    case "_PROJECT_demo.com":
        $_ENV['BRAND_HOME_URL'] = "http://_PROJECT_demo.com";
        $_ENV['SITENAME'] = "DEMO-_PROJECT_";
        $_ENV['SUPPORT_EMAIL'] = "dev+dev-deployment@_PROJECT_.com";
        $_ENV['MAIL_SENDER_EMAIL'] = "info+dev-deployment@_PROJECT_.com";
        $_ENV['MAIL_SENDER_NAME'] = "DEMO-_PROJECT_";
        break;

    case "_PROJECT_.com":
        $_ENV['BRAND_HOME_URL'] = "http://_PROJECT_.com";
        $_ENV['SITENAME'] = "_PROJECT_";
        $_ENV['SUPPORT_EMAIL'] = "dev@_PROJECT_.com";
        $_ENV['MAIL_SENDER_EMAIL'] = "info@_PROJECT_.com";
        $_ENV['MAIL_SENDER_NAME'] = "_PROJECT_";
        break;

}
