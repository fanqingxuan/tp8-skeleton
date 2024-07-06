<?php

use app\event\Hello;
use app\listener\Hello as ListenerHello;

return [
    Hello::class    =>  [
        ListenerHello::class,
    ]
];