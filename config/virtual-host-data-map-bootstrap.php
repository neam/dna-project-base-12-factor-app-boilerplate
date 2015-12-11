<?php

use \neam\bootstrap\Config;

class VirtualHostDataMapBootstrap
{

    static public $headers = [];

    static public function config()
    {

        $_ = explode(",", Config::read('VIRTUAL_HOST_DATA_MAP', null, $required = true));
        $virtual_host_data_mappings = [];
        foreach ($_ as $mapping) {
            $__ = explode("@", $mapping);
            $virtual_host_data_mappings[$__[0]] = $__[1];
        }
        return $virtual_host_data_mappings;

    }

    static public function setDataProfile()
    {

        // Load request headers
        static::$headers = getallheaders();

        $virtual_host_data_mappings = static::config();

        $DATA = null;
        // array_reverse ensures that the leftmost mappings are overriding the rightmost more general mappings
        foreach (array_reverse($virtual_host_data_mappings, true) as $virtual_host => $data_profile) {

            if ($data_profile !== '%DATA%') {
                // Check direct hits in VIRTUAL_HOST_DATA_MAP (for instance %DATA%.player.adoveo.local@%DATA%,%DATA%.adoveo.local@%DATA%,%DATA%.ratataa.local@%DATA%,sas.ratataa.se@sas,cokecce.adoveo.com@cokecce,bigbrother.ratataa.se@sbs-discovery)
                if ($_SERVER['HTTP_HOST'] == $virtual_host) {
                    $DATA = $data_profile;
                    break;
                }
            } elseif ($data_profile === '%DATA%') {
                // Use subdomain pattern %DATA%.player.adoveo.com or %DATA%.adoveo.com or %DATA%.ratataa.se etc as specified in VIRTUAL_HOST_DATA_MAP
                $virtual_host_without_data_token = str_replace('%DATA%', '', $virtual_host);
                $http_host_data_profile = str_replace($virtual_host_without_data_token, '', $_SERVER['HTTP_HOST']);

                if (!empty($http_host_data_profile) && $http_host_data_profile !== $_SERVER['HTTP_HOST']) {
                    $DATA = $http_host_data_profile;
                    break;
                }
            }

        }

        // Set the DATA env var
        $_ENV['DATA'] = $DATA;

    }

}
