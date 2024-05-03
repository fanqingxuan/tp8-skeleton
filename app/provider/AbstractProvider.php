<?php

namespace app\provider;

use think\Service;

abstract class AbstractProvider extends Service
{
    abstract public function register();
    abstract public function boot();
}