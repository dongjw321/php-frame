<?php
namespace DongPHP\Libraries;

class Config
{
    public static $config;

    protected static $path = [];

    public static function setPath(array $path)
    {
        self::$path['normal']  = $path;
    }

    public static function setEnvironmentPath(array $path)
    {
        self::$path['environment'] = $path;
    }

    public static function loadFile($file, $path='')
    {
        $key = $file.'|'.(string)$path;

        if (empty(self::$config[$key])) {
            self::$config[$key] = self::$path['normal'] .$file.".php";
        }

        return self::$config[$key];
    }

    public static function get($key)
    {
        if (!$key) {
            throw new \Exception('请输入要加载的配置文件');
        }

        $argvs  = explode('.', $key);
        $file   = array_shift($argvs);
        if (isset(self::$config[$key])) {
            return self::$config[$key];
        }

        $config = self::loadFile($file);
        foreach ($argvs as $v) {
            if (isset($config[$v])) {
                $config = $config[$v];
            } else {
                return null;
            }
        }
        self::$config[$key] = $config;
        return $config;
    }

    /**
     * 根据运行环境取对应的配置
     * @param $key
     * @return null
     * @throws \Exception
     */
    public static function environment($key)
    {
        return self::get(self::$path['environment'].'/'.$key);
    }
}
