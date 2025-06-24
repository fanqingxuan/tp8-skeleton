<?php


// 容器Provider定义文件

use support\ExceptionHandle;
use support\Request;

return [
    'think\Request'          => Request::class,
    'think\exception\Handle' => ExceptionHandle::class,
];
