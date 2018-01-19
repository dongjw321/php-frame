<?php

namespace DongPHP\Libraries;

class DataConfigLoader
{
    protected static $hash_class = [];

    public static function setHash($class_name, $type="")
    {
        self::$hash_class[$type] = $class_name;
    }

    public static function db($table, $hash_key=null) {
        if (!$table) {
            throw new \Exception('table 参数错误');
        }
        $con    = self::parseTable($table);
        $table  = $con['table'];
        $config = Config::environment($con['dir'].'/db.'.$con['table']);
        if (!$config) {
            $config = Config::environment($con['dir'].'/db.default');
            if (!$config) {
                throw new \Exception('db: ' . $table . '对应的配置信息不存在');
            }
        }

        $database    = isset($config['database']) ? $config['database'] : null ;
        $table_alias = '';

        if ($hash_key && isset(self::$hash_class['mysql'])) {
            $return = call_user_func_array(self::$hash_class['mysql'],[$database, $table, $hash_key]);
            $database    = $return['database'];
            $table       = $return['table'];
            $table_alias = $return['table_alias'];
        }

        /* 最终返回的信息, 以下字段为必须返回的 */
        $ret = array(
            'host'        => $config['host'],
            'user'        => $config['user'],
            'pass'        => $config['pass'],
            'port'        => $config['port'],
            'database'    => $database,
            'table'       => $table,
            'table_alias' => $table_alias
        );

        if (isset($config['write'])) {
            $ret['write'] = $config['write'];
        }
        if (isset($config['read'])) {
            $ret['read']  = $config['read'];
        }
        return $ret;
    }

    public static function redis($table, $hash=null) {
        if (!$table) {
            throw new \Exception('table 参数错误');
        }

        $con    = self::parseTable($table);
        $config = Config::environment($con['dir'].'/redis.'.$con['table']);
        if (!$config) {
            throw new \Exception('redis: '.$table.'对应的配置信息不存在');
        }
        /* 最终返回的信息, 以下字段为必须返回的 */
        $ret = array(
            'host'        => $config['host'],
            'port'        => $config['port'],
            'timeout'     => $config['timeout'],
            'auth'        => isset($config['auth']) ? $config['auth']: null ,
        );
        return $ret;
    }

    public static function memcache($table, $hash=null) {
        if (!$table) {
            throw new \Exception('table 参数错误');
        }

        $con    = self::parseTable($table);
        $config = Config::environment($con['dir'].'/memcache.'.$con['table']);
        if (!$config) {
            throw new \Exception('memcache'.$table.'对应的配置信息不存在');
        }
        /* 最终返回的信息, 以下字段为必须返回的 */
        $ret = array(
            'host'        => $config['host'],
            'port'        => $config['port'],
            'auth'        => isset($config['auth']) ? $config['auth']: null ,
        );
        return $ret;
    }

    public static function parseTable($table)
    {
        $ret   = ['dir'=>'', 'table'=>$table];
        $table = str_replace('.','/',$table);
        $last  = strrpos( $table, '/');
        if ($last !== false) {
            $dir   = substr($table,0,$last);
            $table = substr($table, $last+1);
            $ret = ['dir'=>$dir, 'table'=>$table];
        }
        return $ret;
    }

}