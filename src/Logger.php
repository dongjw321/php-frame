<?php
/*
 * This file is part of the DongPHP package.
 *
 * (c) dongjw321 <dongjw321@163.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DongPHP;

use DongPHP\Logger\AbstractLogger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger as MonologLogger;

class Logger extends AbstractLogger
{
    public static $container = null;

    private $name = null;

    private static $instances;

    /**
     * @param $name
     * @return MonologLogger
     */
    public static function get($name)
    {
        $key = $name;
        if (!isset(self::$instances[$key]) || !(self::$instances[$key] instanceof self)) {
            self::$instances[$key] = new self($key);
        }
        return self::$instances[$key];
    }

    public function __construct($name)
    {
        if (method_exists(__NAMESPACE__ . '\Logger', 'get' . ucfirst($name))) {
            $this->logger = call_user_func_array([__NAMESPACE__ . '\Logger', 'get' . ucfirst($name)], []);
        } else if (class_exists(__NAMESPACE__ . '\Logger\\'.ucfirst($name).'Logger')) {
            $logger_name  = __NAMESPACE__ . '\Logger\\'.ucfirst($name).'Logger';
            $this->logger = new $logger_name();
        } else {
            $this->name    = $name;
            $logger        = new MonologLogger($name);
            $logger->pushHandler($this->getDebugHandler(MonologLogger::DEBUG));
            $logger->pushHandler( new RotatingFileHandler('system', 30, MonologLogger::ERROR));
            //$logger->pushHandler($this->getSocketHandler(MonologLogger::ERROR));
            $this->logger = $logger;
        }
        return $this;
    }
}
