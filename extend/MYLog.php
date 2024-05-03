<?php
namespace extend;

use Psr\Log\LogLevel;
use think\facade\Log;

class MYLog {
    
    public static function debug(string $keyword,mixed $message) {
        self::log(LogLevel::DEBUG,$keyword,$message);
    }

    public static function info(string $keyword,mixed $message) {
        self::log(LogLevel::INFO,$keyword,$message);
    }

    public static function notice(string $keyword,mixed $message) {
        self::log(LogLevel::NOTICE,$keyword,$message);
    }

    public static function error(string $keyword,mixed $message) {
        self::log(LogLevel::ERROR,$keyword,$message);
    }

    private static function log($level,string $keyword, string|array $message) {
        $msg = $keyword.' --> '.(is_array($message)?json_encode($message,JSON_UNESCAPED_UNICODE):$message);
        Log::log($level,$msg);
    }
}