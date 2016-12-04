<?php

class AppCache
{

    static public function redis()
    {

        $redis = new Redis();
        $redis->connect('redisphpsessionhandler', 6379);
        //$redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_NONE);   // don't serialize data
        //$redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);    // use built-in serialize/unserialize
        $redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_IGBINARY);   // use igBinary serialize/unserialize
        return $redis;

    }

    static public function get($key)
    {

        $dataEnvironmentSpecificKey = DATA . "_" . $key;
        return static::redis()->get($dataEnvironmentSpecificKey);

    }

    static public function set($key, $value)
    {

        $dataEnvironmentSpecificKey = DATA . "_" . $key;
        static::redis()->set($dataEnvironmentSpecificKey, $value);

    }

}