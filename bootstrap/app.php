<?php

use support\Application;
use support\Console;
use support\ExceptionHandle;
use support\Request;

$app = new Application();

$bind = [
    
];
if($bind) {
    $app->bind($bind);
}

return $app;