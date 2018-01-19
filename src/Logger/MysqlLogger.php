<?php
namespace DongPHP\Logger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class MysqlLogger extends AbstractLogger
{
    public function __construct()
    {
        $logger = new Logger(__CLASS__);
        $logger->pushHandler($this->getDebugHandler(Logger::DEBUG));
        $logger->pushHandler( new RotatingFileHandler('memcache', 30, Logger::ERROR));
        //$logger->pushHandler($this->getSocketHandler(Logger::ERROR));
        $this->logger = $logger;
        return $this->logger;
    }
}