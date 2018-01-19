<?php
namespace DongPHP\Libraries;

class Lock
{
    const M_LOCK = 'lock:';

    public static function add($key, $time=3)
    {
        for ($i = 1 ; $i < 4; $i++) {
            $flag = Cache::memcache('lock')->add(self::M_LOCK . $key, 1, 0, $time);
            if(! $flag) {
                usleep(200000);
            } else {
                return $i;
            }
        }

        return false;
    }

    public static function del($key)
    {
        Cache::memcache('lock')->delete(self::M_LOCK . $key);
    }

}
