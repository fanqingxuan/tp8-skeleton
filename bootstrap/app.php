<?php

use support\ExceptionHandle;
use support\Request;
use think\App;

$app = new App();

$bind = [
    'think\Request'          => Request::class,
    'think\exception\Handle' => ExceptionHandle::class,
];
$app->bind($bind);

return $app;