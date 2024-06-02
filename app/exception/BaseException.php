<?php
namespace app\exception;

use Exception;

class BaseException extends Exception
{
    
    public static function trigger(string $message):static {
        throw new static($message);
    }
}