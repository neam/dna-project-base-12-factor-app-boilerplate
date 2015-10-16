<?php

use \Firebase\JWT\JWT;
use \neam\bootstrap\Config;

class Auth0JwtBootstrap
{

    static public $headers = [];

    static public function config()
    {

        // Support multiple auth0 clients
        $auth0_apps = explode(",", Config::read('AUTH0_APPS', null, $required = true));
        $auth0_client_ids = explode(",", Config::read('AUTH0_CLIENT_IDS', null, $required = true));
        $auth0_client_secrets = explode(",", Config::read('AUTH0_CLIENT_SECRETS', null, $required = true));

        return [$auth0_apps, $auth0_client_ids, $auth0_client_secrets];

    }

    /**
     * Responsible for setting AUTH0_VALID_DECODED_TOKEN_SERIALIZED and AUTH0_APP
     * based on the JWT supplied with the request
     */
    static public function bootstrap()
    {

        $token = null;

        // Load request headers
        static::$headers = getallheaders();

        $LOCAL_OFFLINE_DATA = getenv('LOCAL_OFFLINE_DATA');

        // Allow local offline mode with fake token
        if (!empty($LOCAL_OFFLINE_DATA)) {
            static::$headers['X-Data-Profile'] = $LOCAL_OFFLINE_DATA;
            $fake_decoded_token = new stdClass();
            $fake_decoded_token->aud = 'auth0-mock-client-id-local-offline-dev';
            $fake_decoded_token->app_metadata = new stdClass();
            $fake_decoded_token->app_metadata->r0 = new stdClass();
            $fake_decoded_token->app_metadata->r0->permissions = new stdClass();
            $fake_decoded_token->app_metadata->r0->permissions->$LOCAL_OFFLINE_DATA = new stdClass();
            define('AUTH0_VALID_DECODED_TOKEN_SERIALIZED', serialize($fake_decoded_token));
            define('AUTH0_APP', 'local-offline');
            return;
        }

        // Allow passing token in Authorization header
        if (isset(static::$headers["Authorization"])) {
            $authHeaderToken = str_replace("Bearer ", "", static::$headers["Authorization"]);
            if (!empty($authHeaderToken)) {
                $token = $authHeaderToken;
            }
        }

        if ($token == null) {
            // Anonymous request without authentication information
            header('HTTP/1.0 401 Unauthorized');
            echo "No authorization header sent";
            exit();
        }

        define('AUTH0_REQUEST_TOKEN', $token);

        list($auth0_apps, $auth0_client_ids, $auth0_client_secrets) = static::config();

        $valid_decoded_token = null;
        try {

            foreach ($auth0_client_secrets as $k => $auth0_client_secret) {

                // Validate the token
                $secret = $auth0_client_secrets[$k];
                $id = $auth0_client_ids[$k];
                $app = $auth0_apps[$k];

                $decoded_token = null;
                try {
                    $decoded_token = JWT::decode($token, base64_decode(strtr($secret, '-_', '+/')), ["HS256"]);
                } catch (\Firebase\JWT\SignatureInvalidException $e) {
                    continue;
                } catch (UnexpectedValueException $ex) {
                    throw $ex;
                }

                // Validate that this token was made for us
                if ($decoded_token->aud === $id) {
                    // We have a valid token!
                    $valid_decoded_token = $decoded_token;
                    define('AUTH0_VALID_DECODED_TOKEN_SERIALIZED', serialize($valid_decoded_token));
                    define("AUTH0_APP", $app);
                    break;
                }

            }

        } catch (Exception $e) {
            //throw $e;
            header('HTTP/1.0 401 Unauthorized');
            echo "Invalid token [e]";
            exit();
        }

        if (empty($valid_decoded_token)) {
            header('HTTP/1.0 401 Unauthorized');
            echo "Invalid token [nv]";
            exit();
        }

        // We have a valid token!

    }

    /**
     * Set the DATA constant based on the JWT metadata
     */
    static public function setDataProfile()
    {

        $valid_decoded_token = unserialize(AUTH0_VALID_DECODED_TOKEN_SERIALIZED);

        // verify that the user has permissions metadata set
        if (!isset($valid_decoded_token->app_metadata->r0->permissions)) {
            header('HTTP/1.0 401 Unauthorized');
            echo "Invalid token [np]";
            exit();
        }

        // set DATA based on X-Data-Profile header
        if (!isset(static::$headers["X-Data-Profile"])) {
            header('HTTP/1.0 400 Bad Request');
            echo "No data profile specified";
        } else {
            // Set the DATA env var
            $_ENV['DATA'] = static::$headers["X-Data-Profile"];
            // Verify that the chosen data profile is part of the user's permissions
            $allowed_data_profiles = array_keys((array) $valid_decoded_token->app_metadata->r0->permissions);
            if (!in_array($_ENV['DATA'], $allowed_data_profiles)) {
                header('HTTP/1.0 401 Unauthorized');
                echo "Invalid token [ip]";
                exit();
            }
        }

    }

}
