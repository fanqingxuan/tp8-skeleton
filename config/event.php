<?php

use app\event\HelloEvent;
use app\listener\HelloListener;

return [
    HelloEvent::class   =>  [
        HelloListener::class,
    ]
];