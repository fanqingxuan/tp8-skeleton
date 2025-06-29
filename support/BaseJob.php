<?php

namespace support;

use think\facade\Queue;

class BaseJob
{
    public static string $queue = 'default';
    public static int $delay = 0;

    public static function dispatch($data) {
        $jobClass = static::class.'@handle';
        if(static::$delay>0) {
            Queue::later(static::$delay, $jobClass, $data, static::$queue);
        } else {
            Queue::push($jobClass, $data, static::$queue);
        }
    }
}