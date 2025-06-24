<?php

use support\Application;
use support\ExceptionHandle;
use support\Request;

$app = new Application();

$bind = [
    'think\Request'          => Request::class,
    'think\exception\Handle' => ExceptionHandle::class,
];
$app->bind($bind);

return $app;