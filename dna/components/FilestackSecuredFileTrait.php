<?php

namespace neam\file_registry;

trait FilestackSecuredFileTrait
{

    static public function signFilestackUrl($filestackUrl, $admin = false)
    {

        // Determine correct signature and policy corresponding to the instance's filestack api key
        $handle = static::extractHandleFromFilestackUrl($filestackUrl);
        $policy = $admin ? static::filestackHandleAdminPolicy($handle) : static::filestackHandleReadOnlyPolicy($handle);
        $signature = static::filestackSignature($policy);

        // Returned signed url
        $glue = strpos($filestackUrl, '?') === false ? '?' : '&';
        return $filestackUrl . $glue . "signature=$signature&policy=$policy";

    }

    static public function filestackHandleReadOnlyJsonPolicy($handle)
    {

        $half_hour_expiry = strval(intval(time() + 60 * 30));
        return '{"handle": "' . $handle . '","call":["read"],"expiry":' . $half_hour_expiry . '}';

    }

    static public function filestackHandleReadOnlyPolicy($handle)
    {

        $json = static::filestackHandleReadOnlyJsonPolicy($handle);
        return static::filestackPolicy($json);

    }

    static public function filestackCreatorJsonPolicy()
    {

        $ten_year_expiry = strval(intval(time() + 60 * 60 * 24 * 365 * 10));
        return '{"call":["pick"],"expiry":' . $ten_year_expiry . '}';

    }

    static public function filestackCreatorPolicy()
    {

        $json = static::filestackCreatorJsonPolicy();
        return static::filestackPolicy($json);

    }

    static public function filestackHandleAdminJsonPolicy($handle)
    {

        $half_hour_expiry = strval(intval(time() + 60 * 30));
        return '{"handle": "' . $handle . '","call":["pick","read","stat","write","writeUrl","store","convert","remove"],"expiry":' . $half_hour_expiry . '}';

    }

    static public function filestackHandleAdminPolicy($handle)
    {

        $json_policy = static::filestackHandleAdminJsonPolicy($handle);
        return static::filestackPolicy($json_policy);

    }

    static public function filestackPolicy($json)
    {

        $safeUrlBase64Encoded = strtr(base64_encode($json), '+/', '-_');
        return $safeUrlBase64Encoded;

    }

    static public function filestackSignature($msg)
    {

        return hash_hmac('SHA256', $msg, FILESTACK_API_SECRET, false);

    }

}
