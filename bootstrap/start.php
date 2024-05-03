<?php

namespace think;

use extend\core\Application;
use extend\core\ExceptionHandle;
use extend\core\Request;

$app = new Application();

$app->bind('think\Request', Request::class);
$app->bind('think\exception\Handle', ExceptionHandle::class);

if (is_file($app->getConfigPath() . 'middleware.php')) {
    $app->middleware->import(include $app->getConfigPath() . 'middleware.php');
}
return $app;