<?php

namespace App\Utils;

class Logger
{
    private static $logDirectory = __DIR__ . '/../../logs';
    private static $logFileName = 'app.log';

    public static function error($message)
    {
        self::write('ERROR', $message);
    }

    public static function warning($message)
    {
        self::write('WARNING', $message);
    }

    public static function info($message)
    {
        self::write('INFO', $message);
    }

    private static function write($level, $message)
    {
        $timestamp = date('Y-m-d H:i:s');
        $logLine = "[$timestamp] [$level] $message" . PHP_EOL;
        
        $logFilePath = self::$logDirectory . '/' . self::$logFileName;
        
        file_put_contents($logFilePath, $logLine, FILE_APPEND);
    }
}
