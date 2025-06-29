<?php

namespace support;

use think\facade\Event as FacadeEvent;

class Event
{
    public static function dispatch($payload)
    {
        FacadeEvent::trigger(static::class,$payload);
    }

}