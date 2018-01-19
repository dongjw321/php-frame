<?php
namespace DongPHP;

use DongPHP\Libraries\DataConfigLoader;
use DongPHP\Libraries\Memcache;
use DongPHP\Libraries\Redis;

class Data
{
    /**
     * @param $key
     * @param null $hash
     * @return \Redis
     */
    public static function redis($key, $hash=null)
    {
        $config = DataConfigLoader::redis($key, $hash);
        return Redis::getInstance($config['host'], $config['port'], $config['timeout'], $config['auth']);
    }

    /**
     * @param $key
     * @param null $hash
     * @return \Memcache
     */
    public static function memcache($key, $hash=null)
    {
        $config = DataConfigLoader::memcache($key, $hash);
        return Memcache::getInstance($config['host'], $config['port']);
    }
}
