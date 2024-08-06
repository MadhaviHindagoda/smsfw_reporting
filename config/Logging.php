<?php

namespace config;

require_once __DIR__ . '/../vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Logging
{
    private static $logger = null;

    public static function getLogger()
    {
        if (self::$logger === null) {
            self::$logger = new Logger('db');
            self::$logger->pushHandler(new StreamHandler(__DIR__ . '/../logs/app.log'));
        }
        return self::$logger;
    }

    public static function logInfo($message)
    {
        self::getLogger()->info($message);
    }

    public static function logError($message)
    {
        self::getLogger()->error($message);
    }
}

