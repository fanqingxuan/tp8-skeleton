<?php

use support\Application;

$app = new Application();

$bind = [
    
];
if($bind) {
    $app->bind($bind);
}
return $app;