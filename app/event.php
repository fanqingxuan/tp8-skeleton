<?php
// 事件定义文件

use app\event\HelloEvent;
use app\listener\HelloListenerA;
use app\listener\HelloListenerB;

return [
    'bind'      => [
    ],

    'listen'    => [
        'AppInit'  => [],
        'HttpRun'  => [],
        'HttpEnd'  => [],
        'LogLevel' => [],
        'LogWrite' => [],
        HelloEvent::class => [
            HelloListenerA::class,
            HelloListenerB::class,
        ],
    ],

    'subscribe' => [
    ],
];
